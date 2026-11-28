<?php

namespace App\Models;

use App\Imports\QuestionDataSheetImport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class QuestionIaudit extends Model
{
    // "Delete" on the admin screen hides the row (deleted_at) rather than
    // removing it: POST /api/answers checks every question_id exists, and
    // ids are assigned as max()+1, so a removed row would break cached
    // mobile audits and could hand its id to a new question.
    use SoftDeletes;

    /** Answer sets a question can offer: stored key => label. */
    public const ANSWER_OPTIONS = [
        'yes_no_na' => 'Yes/No/N/A',
        'yes_no'    => 'Yes/No',
    ];

    /** Question priority: stored key => label. */
    public const PRIORITIES = [
        'standard' => 'Standard',
        'critical' => 'Critical',
    ];

    /** short_code prefix for questions added on the admin screen (NEW001, NEW002…). */
    public const SCREEN_CODE_PREFIX = 'NEW';

    protected $table = 'questions_iaudit';
    protected $primaryKey = 'question_id';
    protected $fillable = [
        'question_id','reference_id','heading_id','subheading_id',
        'category_id','question_text','information_text',
        'department_id','short_code','template','row_type',
        'ordinal','text_icon','block_ref','is_active','imported_at',
        'answer_options','priority',
    ];
    
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function heading()
    {
        return $this->belongsTo(HeadingIaudit::class, 'heading_id', 'heading_id');
    }

    public function subHeading()
    {
        return $this->belongsTo(SubHeadingIaudit::class, 'subheading_id', 'subheading_id');
    }

    public function category()
    {
        return $this->belongsTo(CategoryIaudit::class, 'category_id', 'category_id');
    }

    public function reference()
    {
        return $this->belongsTo(TemplateRefIaudit::class, 'reference_id', 'reference_id');
    }

    /**
     * Real FK as of the 2026_09_06 migration — department_id now stores
     * departments_iaudit's own key, not the sheet's raw department text.
     */
    public function department()
    {
        return $this->belongsTo(DepartmentIaudit::class, 'department_id', 'department_id');
    }

    public function ncs()
    {
        return $this->hasMany(QuestionNcIaudit::class, 'question_id', 'question_id');
    }

    /** Submitted answers. A question that has any can be retired but not deleted. */
    public function answers()
    {
        return $this->hasMany(AnswerIaudit::class, 'question_id', 'question_id');
    }

    /** The code shown on screen: short_code, or the id for legacy rows that have none. */
    public function displayCode(): string
    {
        return $this->short_code ?? '#' . $this->question_id;
    }

    /**
     * Template codes in use, for the admin question form. Falls back to the
     * importer's default when no imported question exists yet.
     */
    public static function templateCodes(): Collection
    {
        $codes = static::withTrashed()->whereNotNull('template')->distinct()->orderBy('template')->pluck('template');

        return $codes->isEmpty() ? collect([QuestionDataSheetImport::DEFAULT_TEMPLATE]) : $codes;
    }

    public function criteria()
    {
        return $this->hasMany(CriteriaIaudit::class, 'reference_id', 'reference_id');
        // return $this->hasMany(CriteriaIaudit::class, 'question', 'question_id')
        //     ->whereColumn('criteria.reference_id', 'question.reference_id');
    }

    /**
     * Rows that represent an actual question (row_type is 'question', or
     * NULL for the ~150 legacy rows seeded before row_type existed).
     */
    public function scopeQuestionRows($query)
    {
        return $query->where(fn ($q) => $q->whereNull('row_type')->orWhere('row_type', 'question'));
    }

    /**
     * Excludes questions soft-retired by a later import — a question
     * dropped from the workbook is set is_active=false when submitted
     * answers still point at it (see QuestionDataSheetImport).
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
