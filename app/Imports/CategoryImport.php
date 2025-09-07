<?php

namespace App\Imports;

use App\Models\CategoryIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['category_id'])) return null;

        CategoryIaudit::updateOrCreate(
            ['category_id' => (int)$row['category_id']],
            ['name' => $row['name'] ?? null]
        );

        return null;
    }
}
