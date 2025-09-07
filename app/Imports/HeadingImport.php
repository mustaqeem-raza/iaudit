<?php

namespace App\Imports;

use App\Models\HeadingIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class HeadingImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['heading_id'])) return null;

        HeadingIaudit::updateOrCreate(
            ['heading_id' => (int)$row['heading_id']],
            ['name' => $row['name'] ?? null]
        );

        return null;
    }
}
