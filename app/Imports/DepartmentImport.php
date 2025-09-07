<?php

namespace App\Imports;

use App\Models\DepartmentIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DepartmentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['department_id'])) return null;

        DepartmentIaudit::updateOrCreate(
            ['department_id' => (int)$row['department_id']],
            ['name' => $row['name'] ?? null]
        );

        return null;
    }
}
