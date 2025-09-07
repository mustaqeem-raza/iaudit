<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateIaudit extends Model
{
    protected $table = 'templates_iaudit';
    protected $primaryKey = 'template_id';
    protected $fillable = ['department_id','reference_id','report_title','template_code'];
    
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(DepartmentIaudit::class, 'department_id', 'department_id');
    }

    public function templateRef()
    {
        return $this->belongsTo(TemplateRefIaudit::class, 'reference_id', 'reference_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionIaudit::class, 'reference_id', 'reference_id');
    }

    public function textBoxes()
    {
        return $this->hasMany(TextBoxIaudit::class, 'reference_id', 'reference_id');
    }
}