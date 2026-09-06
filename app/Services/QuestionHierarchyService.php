<?php

namespace App\Services;

use App\Models\DepartmentIaudit;
use App\Models\QuestionIaudit;
use Illuminate\Support\Collection;

/**
 * Builds the department → heading → subheading → category → question
 * hierarchy shared by GET /api/questions and the audit report/PDF.
 *
 * Two schema generations of `questions_iaudit` currently coexist (see
 * refactor-schema.md / schema-update-minimal-approach.md):
 *  - "legacy" rows (`template IS NULL`), seeded by the old 10-tab
 *    WorkbookImport, reachable only via
 *    departments_iaudit → templates_iaudit → questions_iaudit(reference_id).
 *  - "imported" rows (`template` set), written by the new
 *    QuestionDataSheetImport, which never populates `reference_id` and so
 *    is invisible to that old traversal.
 *
 * Both branches now resolve to the *same* real `departments_iaudit.department_id`
 * (fixed by the 2026_09_06 migration + importer change — department_id used
 * to be the sheet's raw department text, not a foreign key), so they're
 * merged here by that id before the heading/subheading/category tree is
 * built, rather than being built as two separate trees and concatenated.
 * A legacy and an imported department sharing a name now correctly land in
 * one combined block instead of two.
 */
class QuestionHierarchyService
{
    /**
     * @param \Closure|null $questionMapper Shapes one QuestionIaudit model
     *   into the leaf array the caller wants (e.g. adding answer data for
     *   the report, or whitelisting API-facing fields). Defaults to a bare
     *   id/text shape if omitted.
     */
    public function buildTree(?\Closure $questionMapper = null): Collection
    {
        $mapper = $questionMapper ?? fn (QuestionIaudit $q) => [
            'question_id'   => $q->question_id,
            'question_text' => $q->question_text,
        ];

        return $this->legacyQuestions()
            ->concat($this->importedQuestions())
            ->groupBy(fn ($item) => $item['department_id'] ?? 'unassigned')
            ->map(function ($items, $departmentKey) use ($mapper) {
                $questions = $items->pluck('question')->filter();

                return [
                    'department_id'   => $departmentKey === 'unassigned' ? null : $departmentKey,
                    'department_name' => $items->first()['department_name'],
                    'headings'        => $this->buildHeadingLevel($questions, $mapper),
                ];
            })
            ->values();
    }

    /**
     * Existing seed data, unchanged traversal: department → templates →
     * questions (via reference_id). questionRows()/active() are no-ops
     * against today's legacy rows (row_type is already null, is_active
     * already true) — added so this stays correct once either is populated.
     *
     * Departments with zero questions still emit one placeholder item
     * (question: null, filtered out in buildTree()) so an empty department
     * still surfaces as an empty block — matches the original API's
     * behaviour for departments with nothing linked to them.
     */
    private function legacyQuestions(): Collection
    {
        $departments = DepartmentIaudit::with([
            'templates.questions' => fn ($q) => $q->questionRows()->active()
                ->with(['heading', 'subHeading', 'category', 'ncs']),
        ])->get();

        return $departments->flatMap(function ($department) {
            $questions = $department->templates
                ->flatMap(fn ($template) => $template->questions)
                ->filter()
                ->unique('question_id');

            if ($questions->isEmpty()) {
                $questions = collect([null]);
            }

            return $questions->map(fn ($question) => [
                'department_id'   => $department->department_id,
                'department_name' => $department->name,
                'question'        => $question,
            ]);
        });
    }

    /**
     * Rows written by the new per-template importer. `department_id` is a
     * real departments_iaudit FK (see class docblock), resolved via the
     * `department` relation for the display name — the same table legacy
     * rows resolve their department name from, which is what lets the two
     * branches merge correctly in buildTree().
     */
    private function importedQuestions(): Collection
    {
        return QuestionIaudit::whereNotNull('template')
            ->questionRows()
            ->active()
            ->with(['department', 'heading', 'subHeading', 'category', 'ncs'])
            ->get()
            ->map(fn ($question) => [
                'department_id'   => $question->department_id,
                'department_name' => optional($question->department)->name ?? 'Unassigned',
                'question'        => $question,
            ]);
    }

    /**
     * Groups a flat question collection into heading → subheading →
     * category → questions. This is the traversal that used to live
     * duplicated (and drifting slightly out of sync) in both
     * ApiController::questions() and AuditReportController::buildAuditData().
     */
    private function buildHeadingLevel(Collection $questions, \Closure $mapper): Collection
    {
        return $questions
            ->groupBy(fn ($q) => optional($q->heading)->heading_id)
            ->map(function ($headingQuestions, $headingId) use ($mapper) {
                $headingName = optional($headingQuestions->first()->heading)->name ?? 'Untitled Heading';

                $subheadings = $headingQuestions
                    ->groupBy(fn ($q) => optional($q->subHeading)->subheading_id)
                    ->map(function ($subQuestions, $subheadingId) use ($mapper) {
                        $subheadingName = optional($subQuestions->first()->subHeading)->name ?? 'General';
                        $informationText = $subQuestions->pluck('information_text')->filter()->first();

                        $categories = $subQuestions
                            ->groupBy(fn ($q) => optional($q->category)->category_id)
                            ->map(function ($categoryQuestions, $categoryId) use ($mapper) {
                                $categoryName = optional($categoryQuestions->first()->category)->name ?? 'Uncategorized';

                                return [
                                    'category_id'   => $categoryId,
                                    'category_name' => $categoryName,
                                    'questions'     => $categoryQuestions->map($mapper)->values(),
                                ];
                            })
                            ->values();

                        return [
                            'subheading_id'    => $subheadingId,
                            'subheading_name'  => $subheadingName,
                            'information_text' => $informationText,
                            'categories'       => $categories,
                        ];
                    })
                    ->values();

                return [
                    'heading_id'   => $headingId,
                    'heading_name' => $headingName,
                    'subheadings'  => $subheadings,
                ];
            })
            ->values();
    }
}
