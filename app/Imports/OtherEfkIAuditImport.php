<?php

namespace App\Imports;

use App\Models\OtherEfkIAudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class OtherEfkIAuditImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    public function model(array $row)
    {
        return new OtherEfkIAudit([

            'other_efk_ref'           => $row['other_efk_ref'] ?? null,
            'other_efk_main_heading'  => $row['other_efk_main_heading'] ?? null,
            'other_efk_ordinal'       => $row['other_efk_ordinal'] ?? null,
            'other_efk_type_mnemonic' => $row['other_efk_type_mnemonic'] ?? null,
            'other_efk_type'          => $row['other_efk_type'] ?? null,

            'other_efk_compliance'    => $row['other_efk_compliance'] ?? null,
            'other_efk_logic'         => $row['other_efk_logic'] ?? null,

            // handle possible double underscore issue
            'other_efk_sub_heading'   => $row['other_efk__sub_heading']
                ?? $row['other_efk_sub_heading']
                ?? null,

            'other_efk_category'      => $row['other_efk_category'] ?? null,
            'other_efk_sub_category'  => $row['other_efk_sub_category'] ?? null,

            'other_efk_non_compliance_text' => $row['other_efk_non_compliance_text'] ?? null,
            'other_efk_i_info'              => $row['other_efk_i_info'] ?? null,

            'other_efk_usph_ref'     => $row['other_efk_usph_ref'] ?? null,
            'other_efk_ship_san_ref' => $row['other_efk_shipsan_ref']
                ?? $row['other_efk_ship_san_ref']
                ?? null,
            'other_efk_anvia_ref'    => $row['other_efk_anvia_ref'] ?? null,
            'other_efk_mpi_ref'      => $row['other_efk_mpi_ref'] ?? null,
        ]);
    }
}
