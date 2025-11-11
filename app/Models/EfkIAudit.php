<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EfkIAudit extends Model
{
    protected $table = 'efk_iaudit';

    protected $fillable = [
        'department_id',
        'department_name',
        'deck',
        'area',
        'location',
        'type',
    ];
}