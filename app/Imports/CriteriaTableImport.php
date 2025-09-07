<?php

namespace App\Imports;

use App\Models\CriteriaIaudit;
use App\Models\CriteriaTableIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CriteriaTableImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['criteria_id'])) return null;

        CriteriaIaudit::updateOrCreate(
            ['criteria_id' => (int)$row['criteria_id']],
            [
                'reference_id' => !empty($row['reference_id']) ? (int)$row['reference_id'] : null,
                'main_heading' => $row['main_heading']  ?? null,
                'table_heading'=> $row['table_heading'] ?? null,
                'question'     => $row['question']      ?? null,
            ]
        );

        return null;
    }
}
