<?php

namespace App\Imports;

use App\Models\OtherCrtIAudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class OtherCrtIAuditImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        return new OtherCrtIAudit([
            'other_crt_ref'                 => $row['other_crt_ref'] ?? null,
            'other_crt_main_heading'        => $row['other_crt_main_heading'] ?? null,
            'other_crt_ordinal'             => $row['other_crt_ordinal'] ?? null,
            'other_crt_type_mnemonic'       => $row['other_crt_type_mnemonic'] ?? null,
            'other_crt_type'                => $row['other_crt_type'] ?? null,
            'other_crt_sub_heading'         => $row['other_crt_sub_heading'] ?? null,
            'other_crt_category'            => $row['other_crt_category'] ?? null,
            'other_crt_sub_category'        => $row['other_crt_sub_category'] ?? null,
            'other_crt_non_compliance_text' => $row['other_crt_non_compliance_text'] ?? null,
            'other_crt_i_info'              => $row['other_crt_i_info'] ?? null,
        ]);
    }
}
