<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionIaudit extends Model
{
    protected $table = 'questions_iaudit';
    protected $primaryKey = 'question_id';
    protected $fillable = [
        'question_id','reference_id','heading_id','subheading_id',
        'category_id','question_text','information_text'
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
}
