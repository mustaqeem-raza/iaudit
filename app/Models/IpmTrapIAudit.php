<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpmTrapIAudit extends Model
{
    protected $table = 'ipm_traps_iaudit';

    protected $fillable = [
        'ship_name',
        'mnemonic_all',
        'mnemonic_fleet',
        'mnemonic_ship',
        'dept_name',
        'trap_type',
        'deck_no',
        'area',
        'location',
        'location_trap',
        'count_type',
    ];
}
