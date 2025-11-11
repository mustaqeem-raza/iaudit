<?php

namespace App\Imports;

use App\Models\EfkIAudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EfkIAuditImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new EfkIAudit([
            'department_id'   => $row['department_id'] ?? null,
            'department_name' => $row['department_name'] ?? null,
            'deck'            => $row['deck'] ?? null,
            'area'            => $row['area'] ?? null,
            'location'        => $row['location'] ?? null,
            'type'            => $row['fly_killer_type'] ?? null,
        ]);
    }
}
