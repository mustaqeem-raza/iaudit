<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentIaudit extends Model
{
    protected $table = 'departments_iaudit';
    protected $primaryKey = 'department_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    public function templates()
    {
        return $this->hasMany(TemplateIaudit::class, 'department_id', 'department_id');
    }
}
