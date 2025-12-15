<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherCrtIAudit extends Model
{
    protected $table = 'other_crt_iaudit';

    protected $fillable = [
        'other_crt_ref',
        'other_crt_main_heading',
        'other_crt_ordinal',
        'other_crt_type_mnemonic',
        'other_crt_type',
        'other_crt_sub_heading',
        'other_crt_category',
        'other_crt_sub_category',
        'other_crt_non_compliance_text',
        'other_crt_i_info',
    ];
}
