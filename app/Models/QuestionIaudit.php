<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionIaudit extends Model
{
    protected $table = 'questions_iaudit';
    protected $primaryKey = 'question_id';
    protected $fillable = [
        'question_id','reference_id','heading_id','subheading_id',
        'category_id','question_text','information_text',
        'department_id','short_code','template','row_type',
        'ordinal','text_icon','block_ref','is_active','imported_at',
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
     * Excludes soft-retired questions. A no-op today (every row is written
     * with is_active=true and nothing yet sets it false — see
     * refactor-schema.md §3.2) but kept so the query is correct the moment
     * that changes, without having to remember to add it later.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
