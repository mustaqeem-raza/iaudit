<?php

namespace App\Imports;

use App\Models\AnswerIaudit;
use App\Models\CriteriaIaudit;
use App\Models\QuestionIaudit;
use App\Models\QuestionNcIaudit;
use App\Models\TextBoxIaudit;
use App\Services\QuestionReferenceData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Imports the single flat `Data` sheet of IPM_Schema_Update.xlsx.
 *
 * One row can produce writes to up to three tables depending on its
 * row-type (column C, `Criteria`), which is why this is ToCollection
 * rather than ToModel — ToModel's "return one model" contract doesn't fit.
 *
 * Upsert-by-[template, short_code], scoped strictly to the sheet's own
 * template. Existing questions are updated in place so their question_id
 * (and any answers_iaudit rows pointing at it) stay intact — a wipe-and
 * reinsert would hit the answers_iaudit.question_id FK (onDelete: restrict)
 * on any environment that already has submitted audits. After the sheet
 * is written, questions that disappeared from the workbook are deleted
 * (hidden via deleted_at, see QuestionIaudit) when nothing references them;
 * questions that still have answers are retired (`is_active = false`)
 * instead. A deleted question that is back in the workbook is restored.
 * Department, heading, subheading and category names and new question ids
 * come from QuestionReferenceData, shared with the admin question form.
 * This never touches the 150
 * legacy rows from the old workbook (template IS NULL, out of scope) or
 * any other template's rows. See retireOrRemoveStaleTemplateData().
 *
 * Skip `Config` and `Report` — this class is only ever pointed at the
 * `Data` sheet by QuestionWorkbookImport.
 */
class QuestionDataSheetImport implements ToCollection, WithHeadingRow
{
    /** Fallback template identifier if the Data sheet's `Template` row is ever missing. */
    public const DEFAULT_TEMPLATE = 'ARTEMPLATE';

    /** @var string captured from the sheet's one `template`-type row (or the fallback above) */
    public string $templateName = self::DEFAULT_TEMPLATE;

    /** @var array<string,int> counts of newly-created rows, keyed by row_type */
    public array $created = ['question' => 0, 'text' => 0, 'criteria' => 0];

    /** @var array<string,int> counts of updated (matched) rows, keyed by row_type */
    public array $updated = ['question' => 0, 'text' => 0, 'criteria' => 0];

    /** @var array<int,array{row:int,reason:string}> rows with *some* data that couldn't be written, with why */
    public array $skipped = [];

    /**
     * Rows with absolutely nothing in any column — an Excel "used range"
     * artifact (formatting applied below the real data, a table/filter
     * range that outlived the data, etc.), not a data problem. Counted
     * separately and never surfaced as a skip/warning: an admin seeing
     * "25 rows skipped" for rows that were never real data reads as
     * something going wrong when nothing did.
     */
    public int $blankRowsIgnored = 0;

    /** @var string[] non-fatal data-quality notes (missing optional fields etc.) */
    public array $warnings = [];

    /** @var array<string,int> counts of rows removed (or retired) because they were no longer in the sheet */
    public array $replaced = ['question' => 0, 'text' => 0, 'criteria' => 0];

    /** Questions dropped from the sheet but kept because submitted answers still point at them. */
    public int $retired = 0;

    private ?int $nextCriteriaId = null;
    private ?int $nextTextBoxId = null;

    /** @var array<string,array<string,true>> short_codes written this run, keyed by row_type */
    private array $seenCodes = ['question' => [], 'text' => [], 'criteria' => []];

    /** One instance per import run, so its name caches and id counters span the whole sheet. */
    public function __construct(
        private QuestionReferenceData $references = new QuestionReferenceData(),
    ) {
    }

    public function collection(Collection $rows): void
    {
        $this->templateName = $this->resolveTemplateName($rows);

        foreach ($rows as $i => $row) {
            // +1 for the zero-based index, +1 for the header row itself.
            $excelRow = $i + 2;

            if ($this->rowIsEntirelyBlank($row)) {
                $this->blankRowsIgnored++;
                continue;
            }

            $criteriaCell = strtolower(trim((string) ($row['criteria'] ?? '')));
            $rowType = match (true) {
                $criteriaCell === ''         => 'question',
                $criteriaCell === 'template' => 'template',
                $criteriaCell === 'text'     => 'text',
                $criteriaCell === 'criteria' => 'criteria',
                default                      => null,
            };

            if ($rowType === null) {
                $this->skipped[] = [
                    'row'    => $excelRow,
                    'reason' => "Unrecognised Criteria value '{$row['criteria']}'",
                ];
                continue;
            }

            // Already captured up-front in resolveTemplateName(); nothing to write for this row.
            if ($rowType === 'template') {
                continue;
            }

            $shortCode = trim((string) ($row['short_code'] ?? ''));
            if ($shortCode === '') {
                $this->skipped[] = ['row' => $excelRow, 'reason' => 'Missing Short_Code'];
                continue;
            }

            match ($rowType) {
                'question' => $this->importQuestionRow($row, $excelRow, $shortCode),
                'text'     => $this->importTextRow($row, $excelRow, $shortCode),
                'criteria' => $this->importCriteriaRow($row, $excelRow, $shortCode),
            };
        }

        $this->retireOrRemoveStaleTemplateData();
    }

    /**
     * Find the sheet's one `template`-type row (row-order independent) and
     * use its Short_Code as the template identifier for every other row in
     * this run. Falls back to the hardcoded constant if that row is missing
     * or blank. Multi-template support is out of scope — see refactor-schema.md §3.4.
     */
    private function resolveTemplateName(Collection $rows): string
    {
        $templateRow = $rows->first(
            fn ($row) => strtolower(trim((string) ($row['criteria'] ?? ''))) === 'template'
        );

        $shortCode = trim((string) ($templateRow['short_code'] ?? ''));

        return $shortCode !== '' ? $shortCode : self::DEFAULT_TEMPLATE;
    }

    /**
     * After the sheet has been upserted, drop this template's rows that are
     * no longer in the workbook. Questions without answers are deleted, which
     * hides them (deleted_at) so their ids stay valid for cached mobile apps.
     * Questions that still have submitted answers are retired instead.
     * Scoped strictly by `template` — legacy rows (template IS NULL) and
     * any other template are never touched.
     */
    private function retireOrRemoveStaleTemplateData(): void
    {
        $staleQuestionIds = $this->staleTemplateQuery(QuestionIaudit::query(), 'short_code', 'question')
            ->where('row_type', 'question')
            ->pluck('question_id')
            ->map(fn ($id) => (int) $id);

        $idsWithAnswers = $staleQuestionIds->isEmpty()
            ? collect()
            : AnswerIaudit::whereIn('question_id', $staleQuestionIds)
                ->pluck('question_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values();

        $idsToDelete = $staleQuestionIds->diff($idsWithAnswers);

        if ($idsWithAnswers->isNotEmpty()) {
            QuestionIaudit::whereIn('question_id', $idsWithAnswers)->update(['is_active' => false]);
            $this->retired = $idsWithAnswers->count();
            $this->warnings[] = $this->retired === 1
                ? '1 question was retired (kept for existing audit answers) because it was removed from the workbook.'
                : "{$this->retired} questions were retired (kept for existing audit answers) because they were removed from the workbook.";
        }

        if ($idsToDelete->isNotEmpty()) {
            QuestionNcIaudit::whereIn('question_id', $idsToDelete)->delete();
            QuestionIaudit::whereIn('question_id', $idsToDelete)->delete();
        }

        $this->replaced = [
            'question' => $idsToDelete->count() + $idsWithAnswers->count(),
            'text'     => $this->staleTemplateQuery(TextBoxIaudit::query(), 'reference_code', 'text')->delete(),
            'criteria' => $this->staleTemplateQuery(CriteriaIaudit::query(), 'short_code', 'criteria')->delete(),
        ];
    }

    /**
     * Rows of this template whose code was not written in this run.
     * An empty seen-list means the sheet had no rows of that type, so
     * every existing row is stale — we must not pass `whereNotIn([], …)`
     * because Laravel compiles that to `0 = 1` and would keep them all.
     */
    private function staleTemplateQuery(Builder $query, string $codeColumn, string $rowType): Builder
    {
        $query->where('template', $this->templateName);

        $codes = array_keys($this->seenCodes[$rowType]);
        if ($codes !== []) {
            $query->whereNotIn($codeColumn, $codes);
        }

        return $query;
    }

    /** True when every cell in the row is null or blank after trimming. */
    private function rowIsEntirelyBlank(Collection $row): bool
    {
        return $row->every(fn ($value) => trim((string) $value) === '');
    }

    private function importQuestionRow(Collection $row, int $excelRow, string $shortCode): void
    {
        $qtn = trim((string) ($row['qtn'] ?? ''));
        $nct = trim((string) ($row['nct'] ?? ''));

        if ($qtn === '' && $nct === '') {
            $this->skipped[] = ['row' => $excelRow, 'reason' => 'Question row has neither QTN nor NCT text'];
            return;
        }

        $department = trim((string) ($row['departments'] ?? ''));
        $title      = trim((string) ($row['title'] ?? ''));
        $heading    = trim((string) ($row['heading'] ?? ''));
        $category   = trim((string) ($row['category'] ?? ''));

        if ($department === '') {
            $this->warnings[] = "Row {$excelRow} ({$shortCode}): missing Department";
        }

        $headingId = $title !== '' ? $this->references->headingId($title) : null;

        $attrs = [
            // Resolved against departments_iaudit (get-or-create by name,
            // same policy as heading/subheading/category below) — never the
            // sheet's raw department text. See the 2026_09_06 migration
            // that fixed the same issue for every row imported before this.
            'department_id' => $department !== '' ? $this->references->departmentId($department) : null,
            'template'      => $this->templateName,
            'row_type'      => 'question',
            'text_icon'     => $this->nullable($row['text_icons'] ?? null),
            'ordinal'       => is_numeric($row['ordinal'] ?? null) ? (int) $row['ordinal'] : null,
            'block_ref'     => $this->nullable($row['block_ref'] ?? null),
            'question_text' => $qtn !== '' ? $qtn : null,
            'heading_id'    => $headingId,
            'subheading_id' => $heading !== '' ? $this->references->subheadingId($heading, $headingId) : null,
            'category_id'   => $category !== '' ? $this->references->categoryId($category) : null,
            'is_active'     => true,
            'imported_at'   => now(),
        ];

        // withTrashed: [template, short_code] is unique across deleted rows too.
        $existing = QuestionIaudit::withTrashed()
            ->where('template', $this->templateName)
            ->where('short_code', $shortCode)
            ->first();

        if ($existing) {
            // A question deleted on screen comes back while the workbook still
            // has it: the workbook stays the master until Week 3's import preview.
            $existing->fill($attrs);
            $existing->deleted_at = null;
            $existing->save();
            $question = $existing;
            $this->updated['question']++;
        } else {
            $question = QuestionIaudit::create(array_merge($attrs, [
                'question_id' => $this->references->nextQuestionId(),
                'short_code'  => $shortCode,
            ]));
            $this->created['question']++;
        }

        $this->seenCodes['question'][$shortCode] = true;

        QuestionNcIaudit::updateOrCreate(
            ['question_id' => $question->question_id],
            [
                'nc_text'           => $nct !== '' ? $nct : null,
                'responsibility'    => $this->nullable($row['responsibility'] ?? null),
                'consultant_remark' => $this->nullable($row['consultant'] ?? null),
                'vsp_item_no'       => $this->nullable($row['vsp_item_no'] ?? null),
                'point_loss'        => is_numeric($row['point_loss'] ?? null) ? (int) $row['point_loss'] : null,
                'vsp_reference'     => $this->nullable($row['vsp_reference'] ?? null),
                'vsp_description'   => $this->nullable($row['vsp_description'] ?? null),
            ]
        );

        if ($this->nullable($row['responsibility'] ?? null) === null) {
            $this->warnings[] = "Row {$excelRow} ({$shortCode}): missing Responsibility";
        }
    }

    private function importTextRow(Collection $row, int $excelRow, string $shortCode): void
    {
        // Short_Code is the only hard requirement (checked by the caller).
        // Confirmed against the real sheet: some Text rows are pure
        // section-heading markers (short_code like TEXTHD*) carrying only a
        // Title, no QTN body — legitimate content, not bad data. A row with
        // neither is unusual but still explicitly marked row_type=text by
        // the sheet's own Criteria column, so it's written through with a
        // warning rather than silently dropped.
        $body    = trim((string) ($row['qtn'] ?? ''));
        $title   = trim((string) ($row['title'] ?? ''));
        $heading = trim((string) ($row['heading'] ?? ''));

        if ($body === '' && $title === '' && $heading === '') {
            $this->warnings[] = "Row {$excelRow} ({$shortCode}): Text row has no body, Title, or Heading";
        }

        $attrs = [
            'template'       => $this->templateName,
            'reference_code' => $shortCode, // reused as the natural dedup key for this pathway
            'main_heading'   => $heading !== '' ? $heading : ($title !== '' ? $title : null),
            'body'           => $body !== '' ? $body : null,
        ];

        $existing = TextBoxIaudit::where('template', $this->templateName)
            ->where('reference_code', $shortCode)
            ->first();

        if ($existing) {
            $existing->fill($attrs)->save();
            $this->updated['text']++;
        } else {
            TextBoxIaudit::create(array_merge($attrs, [
                'text_box_id' => $this->nextTextBoxId(),
            ]));
            $this->created['text']++;
        }

        $this->seenCodes['text'][$shortCode] = true;
    }

    private function importCriteriaRow(Collection $row, int $excelRow, string $shortCode): void
    {
        $title   = trim((string) ($row['title'] ?? ''));
        $heading = trim((string) ($row['heading'] ?? ''));
        $body    = trim((string) ($row['qtn'] ?? ''));

        $attrs = [
            'template'      => $this->templateName,
            'short_code'    => $shortCode,
            'main_heading'  => $title !== '' ? $title : null,
            'table_heading' => $heading !== '' ? $heading : null,
            'question'      => $body !== '' ? $body : null,
        ];

        $existing = CriteriaIaudit::where('template', $this->templateName)
            ->where('short_code', $shortCode)
            ->first();

        if ($existing) {
            $existing->fill($attrs)->save();
            $this->updated['criteria']++;
        } else {
            CriteriaIaudit::create(array_merge($attrs, [
                'criteria_id' => $this->nextCriteriaId(),
            ]));
            $this->created['criteria']++;
        }

        $this->seenCodes['criteria'][$shortCode] = true;
    }

    // --- Hand-assigned PK helpers -------------------------------------
    // criteria_iaudit and text_boxes_iaudit use hand-assigned keys. max()+1
    // is computed once per import run and incremented in PHP, so there is no
    // query per row and two rows created in one run cannot collide. Question,
    // department, heading, subheading and category ids come from
    // QuestionReferenceData, which works the same way.

    private function nextCriteriaId(): int
    {
        $this->nextCriteriaId ??= (int) (CriteriaIaudit::max('criteria_id') ?? 0);

        return ++$this->nextCriteriaId;
    }

    private function nextTextBoxId(): int
    {
        $this->nextTextBoxId ??= (int) (TextBoxIaudit::max('text_box_id') ?? 0);

        return ++$this->nextTextBoxId;
    }

    /**
     * Normalize a sheet cell to null when blank, trimmed string otherwise.
     * Used for optional columns so "" and null are treated identically.
     */
    private function nullable($value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
