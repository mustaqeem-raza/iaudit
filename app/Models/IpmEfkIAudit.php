<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpmEfkIAudit extends Model
{
    protected $table = 'ipm_efk_iaudit';

    protected $fillable = [
        'ship_name',
        'mnemonic_both',
        'mnemonic_fleet',
        'mnemonic_ship',
        'efk_type',
        'deck_no',
        'department',
        'area',
        'location',
        'install_date',
        'type_uvt',
        'count_type',
    ];
}
