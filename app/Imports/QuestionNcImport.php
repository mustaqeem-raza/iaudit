<?php

namespace App\Imports;

use App\Models\QuestionNcIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionNcImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['question_id'])) return null;

        QuestionNcIaudit::updateOrCreate(
            ['question_id' => (int)$row['question_id']],
            [
                'nc_heading'  => $row['nc_heading']  ?? null,
                'nc_text'     => $row['nc_text']     ?? null,
                'nc_rem_hd'   => $row['nc_rem_hd']   ?? null,
                'nc_con_hd'   => $row['nc_con_hd']   ?? null,
                'nc_usph_hd'  => $row['nc_usph_hd']  ?? null,
                'nc_ipm_hd'   => $row['nc_ipm_hd']   ?? null,
                'nc_rem_text'  => $row['nc_rem_text']  ?? null,
                'nc_con_text'  => $row['nc_con_text']  ?? null,
                'nc_usph_text' => $row['nc_usph_text'] ?? null,
                'nc_ipm_ref'   => $row['nc_ipm_ref']   ?? null,
            ]
        );

        return null;
    }
}
