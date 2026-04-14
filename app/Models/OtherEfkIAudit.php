<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherEfkIAudit extends Model
{
    protected $table = 'other_efk_iaudit';

    protected $fillable = [
        'other_efk_ref',
        'other_efk_main_heading',
        'other_efk_ordinal',
        'other_efk_type_mnemonic',
        'other_efk_type',
        'other_efk_compliance',
        'other_efk_logic',
        'other_efk_sub_heading',
        'other_efk_category',
        'other_efk_sub_category',
        'other_efk_non_compliance_text',
        'other_efk_i_info',
        'other_efk_usph_ref',
        'other_efk_ship_san_ref',
        'other_efk_anvia_ref',
        'other_efk_mpi_ref',
    ];
}
