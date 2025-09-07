<?php

namespace App\Imports;

use App\Models\TemplateRefIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TemplateRefImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['reference_id'])) return null;

        TemplateRefIaudit::updateOrCreate(
            ['reference_id' => (int)$row['reference_id']],
            [
                'template_code'  => $row['template_code']  ?? null,
                'reference_code' => $row['reference_code'] ?? null,
            ]
        );

        return null;
    }
}
