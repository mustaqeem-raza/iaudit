<?php

namespace App\Imports;

use App\Models\CategoryIaudit;
use App\Models\CriteriaIaudit;
use App\Models\DepartmentIaudit;
use App\Models\HeadingIaudit;
use App\Models\QuestionIaudit;
use App\Models\QuestionNcIaudit;
use App\Models\SubHeadingIaudit;
use App\Models\TextBoxIaudit;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use RuntimeException;

/**
 * Imports the single flat `Data` sheet of IPM_Schema_Update.xlsx.
 *
 * One row can produce writes to up to three tables depending on its
 * row-type (column C, `Criteria`), which is why this is ToCollection
 * rather than ToModel — ToModel's "return one model" contract doesn't fit.
 *
 * Delete-and-replace, scoped strictly to the sheet's own template: every
 * existing questions_iaudit/criteria_iaudit/text_boxes_iaudit row for
 * `$this->templateName` is removed before the sheet's rows are written
 * back fresh, so re-importing the same (or an updated) workbook never
 * leaves stale rows behind. This never touches the 150 legacy rows from
 * the old workbook (template IS NULL, out of scope) or any other
 * template's rows. It also never reaches answers_iaudit directly — but if
 * a question being replaced already has live submitted answers attached,
 * the answers_iaudit.question_id FK (onDelete: restrict, see the Week 1
 * migrations) blocks the delete outright and the whole import rolls back
 * (config/excel.php wraps Excel::import() in one DB transaction), rather
 * than silently losing real audit data. See replaceExistingTemplateData().
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

    /** @var array<string,int> counts of rows removed before this run's fresh insert, keyed by row_type */
    public array $replaced = ['question' => 0, 'text' => 0, 'criteria' => 0];

    private ?int $nextQuestionId = null;
    private ?int $nextCriteriaId = null;
    private ?int $nextTextBoxId = null;
    private ?int $nextHeadingId = null;
    private ?int $nextSubheadingId = null;
    private ?int $nextCategoryId = null;
    private ?int $nextDepartmentId = null;

    /** @var array<string,int> lookup-table name => id caches, populated lazily within one import run */
    private array $headingIds = [];
    private array $subheadingIds = [];
    private array $categoryIds = [];
    private array $departmentIds = [];

    public function collection(Collection $rows): void
    {
        $this->templateName = $this->resolveTemplateName($rows);
        $this->replaceExistingTemplateData();

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
     * Remove every existing questions_iaudit/criteria_iaudit/text_boxes_iaudit
     * row belonging to $this->templateName, so this run's inserts start from
     * a clean slate for that template and a re-import can never leave a
     * question behind that was deleted from the sheet. Scoped strictly by
     * `template` — the 150 legacy rows (template IS NULL) and any other
     * template are never touched.
     *
     * If a question being removed already has live submitted answers
     * attached, answers_iaudit's FK (onDelete: restrict) blocks the delete
     * and throws a QueryException here. That's deliberately turned into a
     * clear, specific error rather than left as a raw SQL message — and
     * because the whole import runs inside one DB transaction, nothing
     * partial is left behind: either the whole replace+reimport succeeds,
     * or none of it does.
     */
    private function replaceExistingTemplateData(): void
    {
        $questionIds = QuestionIaudit::where('template', $this->templateName)
            ->where('row_type', 'question')
            ->pluck('question_id');

        $this->replaced = [
            'question' => $questionIds->count(),
            'text'     => TextBoxIaudit::where('template', $this->templateName)->count(),
            'criteria' => CriteriaIaudit::where('template', $this->templateName)->count(),
        ];

        try {
            if ($questionIds->isNotEmpty()) {
                QuestionNcIaudit::whereIn('question_id', $questionIds)->delete();
                QuestionIaudit::whereIn('question_id', $questionIds)->delete();
            }

            TextBoxIaudit::where('template', $this->templateName)->delete();
            CriteriaIaudit::where('template', $this->templateName)->delete();
        } catch (QueryException $e) {
            if ($this->isForeignKeyViolation($e)) {
                throw new RuntimeException(
                    "Cannot replace template \"{$this->templateName}\" — some of its questions already have "
                        . 'submitted audit answers attached, which must be preserved. Nothing was changed. '
                        . 'This template can\'t be safely re-imported until that\'s resolved.',
                    previous: $e
                );
            }

            throw $e;
        }
    }

    private function isForeignKeyViolation(QueryException $e): bool
    {
        // MySQL 1451: "Cannot delete or update a parent row: a foreign key constraint fails"
        return ($e->errorInfo[1] ?? null) == 1451;
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

        $headingId = $title !== '' ? $this->getOrCreateHeadingId($title) : null;

        $attrs = [
            // Resolved against departments_iaudit (get-or-create by name,
            // same policy as heading/subheading/category below) — never the
            // sheet's raw department text. See the 2026_09_06 migration
            // that fixed the same issue for every row imported before this.
            'department_id' => $department !== '' ? $this->getOrCreateDepartmentId($department) : null,
            'template'      => $this->templateName,
            'row_type'      => 'question',
            'text_icon'     => $this->nullable($row['text_icons'] ?? null),
            'ordinal'       => is_numeric($row['ordinal'] ?? null) ? (int) $row['ordinal'] : null,
            'block_ref'     => $this->nullable($row['block_ref'] ?? null),
            'question_text' => $qtn !== '' ? $qtn : null,
            'heading_id'    => $headingId,
            'subheading_id' => $heading !== '' ? $this->getOrCreateSubheadingId($heading, $headingId) : null,
            'category_id'   => $category !== '' ? $this->getOrCreateCategoryId($category) : null,
            'is_active'     => true,
            'imported_at'   => now(),
        ];

        $existing = QuestionIaudit::where('template', $this->templateName)
            ->where('short_code', $shortCode)
            ->first();

        if ($existing) {
            $existing->fill($attrs)->save();
            $question = $existing;
            $this->updated['question']++;
        } else {
            $question = QuestionIaudit::create(array_merge($attrs, [
                'question_id' => $this->nextQuestionId(),
                'short_code'  => $shortCode,
            ]));
            $this->created['question']++;
        }

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
    }

    private function getOrCreateHeadingId(string $name): int
    {
        if (isset($this->headingIds[$name])) {
            return $this->headingIds[$name];
        }

        $heading = HeadingIaudit::where('name', $name)->first();
        if (! $heading) {
            $heading = HeadingIaudit::create([
                'heading_id' => $this->nextHeadingId(),
                'name'       => $name,
            ]);
        }

        return $this->headingIds[$name] = $heading->heading_id;
    }

    private function getOrCreateSubheadingId(string $name, ?int $headingId): int
    {
        $cacheKey = $headingId . '|' . $name;
        if (isset($this->subheadingIds[$cacheKey])) {
            return $this->subheadingIds[$cacheKey];
        }

        $subheading = SubHeadingIaudit::where('name', $name)
            ->where('heading_id', $headingId)
            ->first();

        if (! $subheading) {
            $subheading = SubHeadingIaudit::create([
                'subheading_id' => $this->nextSubheadingId(),
                'heading_id'    => $headingId,
                'name'          => $name,
            ]);
        }

        return $this->subheadingIds[$cacheKey] = $subheading->subheading_id;
    }

    private function getOrCreateCategoryId(string $name): int
    {
        if (isset($this->categoryIds[$name])) {
            return $this->categoryIds[$name];
        }

        $category = CategoryIaudit::where('name', $name)->first();
        if (! $category) {
            $category = CategoryIaudit::create([
                'category_id' => $this->nextCategoryId(),
                'name'        => $name,
            ]);
        }

        return $this->categoryIds[$name] = $category->category_id;
    }

    /**
     * Resolves the sheet's `Departments` text to a real departments_iaudit
     * row rather than storing it verbatim (that was the bug — every
     * imported question used to carry its own copy of the department name
     * instead of a foreign key, fixed retroactively for existing rows by
     * the 2026_09_06 migration). Same get-or-create policy as
     * heading/subheading/category above: this table's collation is
     * case-insensitive, so a plain name match is enough to avoid creating
     * near-duplicate rows from casing drift across import runs.
     */
    private function getOrCreateDepartmentId(string $name): int
    {
        if (isset($this->departmentIds[$name])) {
            return $this->departmentIds[$name];
        }

        $department = DepartmentIaudit::where('name', $name)->first();
        if (! $department) {
            $department = DepartmentIaudit::create([
                'department_id' => $this->nextDepartmentId(),
                'name'          => $name,
            ]);
        }

        return $this->departmentIds[$name] = $department->department_id;
    }

    // --- Hand-assigned PK helpers -------------------------------------
    // All of questions_iaudit/criteria_iaudit/text_boxes_iaudit/headings_
    // iaudit/sub_headings_iaudit/categories_iaudit/departments_iaudit use
    // hand-assigned (non-auto-increment) PKs. max()+1 is computed once per import run
    // and incremented in PHP as rows are created within that run, to
    // avoid a query per row and avoid two new rows in the same run
    // colliding on the same next id. Not race-safe across concurrent
    // imports — acceptable for Week 1's single-admin, ad-hoc usage.

    private function nextQuestionId(): int
    {
        $this->nextQuestionId ??= (int) (QuestionIaudit::max('question_id') ?? 0);

        return ++$this->nextQuestionId;
    }

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

    private function nextHeadingId(): int
    {
        $this->nextHeadingId ??= (int) (HeadingIaudit::max('heading_id') ?? 0);

        return ++$this->nextHeadingId;
    }

    private function nextSubheadingId(): int
    {
        $this->nextSubheadingId ??= (int) (SubHeadingIaudit::max('subheading_id') ?? 0);

        return ++$this->nextSubheadingId;
    }

    private function nextCategoryId(): int
    {
        $this->nextCategoryId ??= (int) (CategoryIaudit::max('category_id') ?? 0);

        return ++$this->nextCategoryId;
    }

    private function nextDepartmentId(): int
    {
        $this->nextDepartmentId ??= (int) (DepartmentIaudit::max('department_id') ?? 0);

        return ++$this->nextDepartmentId;
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
