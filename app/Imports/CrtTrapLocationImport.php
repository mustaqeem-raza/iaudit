<?php

namespace App\Imports;

use App\Models\CrtTrapLocationIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CrtTrapLocationImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip rows without department_id
        if (empty($row['department_id']) && empty($row['Department_ID'])) {
            return null;
        }

        return new CrtTrapLocationIaudit([
            'department_id'   => $row['department_id'] ?? null,
            'department_name' => $row['department_name'] ?? null,
            'deck'            => $row['deck'] ?? null,
            'main_section'    => $row['main_section'] ?? null,
            'sub_section'     => $row['sub_section'] ?? null,
            'trap_location'   => $row['trap_location'] ?? null,
            'trap_type'       => $row['trap_type'] ?? null,
        ]);
    }
}
