<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateRefIaudit extends Model
{
    protected $table = 'template_refs_iaudit';
    protected $primaryKey = 'reference_id';
    protected $fillable = ['reference_code','reference_id','template_code'];
    
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function templates()
    {
        return $this->hasMany(TemplateIaudit::class, 'reference_id', 'reference_id');
    }

    public function questions()
    {
        return $this->hasMany(QuestionIaudit::class, 'reference_id', 'reference_id');
    }

    public function textBoxes()
    {
        return $this->hasMany(TextBoxIaudit::class, 'reference_id', 'reference_id');
    }

    public function criteriaTables()
    {
        return $this->hasMany(CriteriaTableIaudit::class, 'reference_id', 'reference_id');
    }
}