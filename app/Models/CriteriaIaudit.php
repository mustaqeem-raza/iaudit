<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CriteriaIaudit extends Model
{
    protected $table = 'criteria_iaudit';
    protected $primaryKey = 'criteria_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function reference()
    {
        return $this->belongsTo(TemplateRefIaudit::class, 'reference_id', 'reference_id');
    }
}
