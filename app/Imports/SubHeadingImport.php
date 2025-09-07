<?php

namespace App\Imports;

use App\Models\SubHeadingIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubHeadingImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['subheading_id'])) return null;

        SubHeadingIaudit::updateOrCreate(
            ['subheading_id' => (int)$row['subheading_id']],
            [
                'heading_id' => !empty($row['heading_id']) ? (int)$row['heading_id'] : null,
                'name'       => $row['name'] ?? null,
            ]
        );

        return null;
    }
}
