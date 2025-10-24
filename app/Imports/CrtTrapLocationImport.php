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
            'department_id'   => $row['department_id'] ?? $row['Department_ID'] ?? null,
            'department_name' => $row['department_name'] ?? $row['Department_Name'] ?? null,
            'deck'            => $row['deck'] ?? $row['Deck'] ?? null,
            'main_section'    => $row['main_section'] ?? $row['Main Section'] ?? null,
            'sub_section'     => $row['sub_section'] ?? $row['Sub Section'] ?? null,
            'trap_location'   => $row['trap_location'] ?? $row['Trap Location'] ?? null,
            'trap_type'       => $row['trap_type'] ?? $row['Trap Type'] ?? null,
        ]);
    }
}
