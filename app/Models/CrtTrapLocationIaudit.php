<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrtTrapLocationIaudit extends Model
{
    protected $table = 'crt_trap_location_iaudit';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'department_id',
        'department_name',
        'deck',
        'main_section',
        'sub_section',
        'trap_location',
        'trap_type',
    ];
}