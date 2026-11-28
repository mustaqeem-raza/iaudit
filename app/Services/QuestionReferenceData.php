<?php

namespace App\Services;

use App\Models\CategoryIaudit;
use App\Models\DepartmentIaudit;
use App\Models\HeadingIaudit;
use App\Models\QuestionIaudit;
use App\Models\SubHeadingIaudit;

/**
 * Resolves question-bank lookup names (department, heading, subheading,
 * category) to ids, creating a row when the name is new, and hands out new
 * question ids.
 *
 * Shared by the workbook import (QuestionDataSheetImport) and the admin
 * question form, so both resolve names and assign ids the same way.
 *
 * These tables use hand-assigned keys. max()+1 is read once per instance and
 * then incremented in PHP, so an import run does not query once per row and
 * two rows created in the same run cannot collide. It is not safe across two
 * runs at the same moment, which is acceptable for single-admin use. Use one
 * instance per import run or per request.
 */
class QuestionReferenceData
{
    private ?int $nextQuestionId = null;
    private ?int $nextDepartmentId = null;
    private ?int $nextHeadingId = null;
    private ?int $nextSubheadingId = null;
    private ?int $nextCategoryId = null;

    /** @var array<string,int> name => id caches, filled lazily */
    private array $departmentIds = [];
    private array $headingIds = [];
    private array $subheadingIds = [];
    private array $categoryIds = [];

    /**
     * A new question_id. Counts deleted (hidden) questions too, so an id is
     * never handed out twice: cached mobile apps may still send answers for it.
     */
    public function nextQuestionId(): int
    {
        $this->nextQuestionId ??= (int) (QuestionIaudit::withTrashed()->max('question_id') ?? 0);

        return ++$this->nextQuestionId;
    }

    /**
     * departments_iaudit's collation is case-insensitive, so a plain name
     * match is enough to avoid near-duplicate rows from casing drift.
     */
    public function departmentId(string $name): int
    {
        if (isset($this->departmentIds[$name])) {
            return $this->departmentIds[$name];
        }

        $department = DepartmentIaudit::where('name', $name)->first();
        if (! $department) {
            $this->nextDepartmentId ??= (int) (DepartmentIaudit::max('department_id') ?? 0);
            $department = DepartmentIaudit::create([
                'department_id' => ++$this->nextDepartmentId,
                'name'          => $name,
            ]);
        }

        return $this->departmentIds[$name] = $department->department_id;
    }

    public function headingId(string $name): int
    {
        if (isset($this->headingIds[$name])) {
            return $this->headingIds[$name];
        }

        $heading = HeadingIaudit::where('name', $name)->first();
        if (! $heading) {
            $this->nextHeadingId ??= (int) (HeadingIaudit::max('heading_id') ?? 0);
            $heading = HeadingIaudit::create([
                'heading_id' => ++$this->nextHeadingId,
                'name'       => $name,
            ]);
        }

        return $this->headingIds[$name] = $heading->heading_id;
    }

    /** Subheading names repeat under different headings, so the heading is part of the key. */
    public function subheadingId(string $name, ?int $headingId): int
    {
        $cacheKey = $headingId . '|' . $name;
        if (isset($this->subheadingIds[$cacheKey])) {
            return $this->subheadingIds[$cacheKey];
        }

        $subheading = SubHeadingIaudit::where('name', $name)
            ->where('heading_id', $headingId)
            ->first();

        if (! $subheading) {
            $this->nextSubheadingId ??= (int) (SubHeadingIaudit::max('subheading_id') ?? 0);
            $subheading = SubHeadingIaudit::create([
                'subheading_id' => ++$this->nextSubheadingId,
                'heading_id'    => $headingId,
                'name'          => $name,
            ]);
        }

        return $this->subheadingIds[$cacheKey] = $subheading->subheading_id;
    }

    public function categoryId(string $name): int
    {
        if (isset($this->categoryIds[$name])) {
            return $this->categoryIds[$name];
        }

        $category = CategoryIaudit::where('name', $name)->first();
        if (! $category) {
            $this->nextCategoryId ??= (int) (CategoryIaudit::max('category_id') ?? 0);
            $category = CategoryIaudit::create([
                'category_id' => ++$this->nextCategoryId,
                'name'        => $name,
            ]);
        }

        return $this->categoryIds[$name] = $category->category_id;
    }
}
