<?php

namespace App\Imports;

use App\Models\TemplateIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TemplateImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['template_id'])) return null;

        TemplateIaudit::updateOrCreate(
            ['template_id' => (int)$row['template_id']],
            [
                'template_code' => $row['template_code'] ?? null,
                'report_title'  => $row['report_title']  ?? null,
                'department_id' => !empty($row['department_id']) ? (int)$row['department_id'] : null,
                'reference_id'  => !empty($row['reference_id'])  ? (int)$row['reference_id']  : null,
            ]
        );

        return null;
    }
}
