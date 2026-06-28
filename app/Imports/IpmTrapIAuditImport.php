<?php

namespace App\Imports;

use App\Models\IpmTrapIAudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IpmTrapIAuditImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip rows without a ship name (blank/trailing lines)
        $shipName = $this->clean($row['ship_name'] ?? null);
        if ($shipName === null) {
            return null;
        }

        return new IpmTrapIAudit([
            'ship_name'      => $shipName,
            'mnemonic_all'   => $this->clean($row['mnemonic_all'] ?? null),
            'mnemonic_fleet' => $this->clean($row['mnemonic_fleet'] ?? null),
            'mnemonic_ship'  => $this->clean($row['mnemonic_ship'] ?? null),
            'dept_name'      => $this->clean($row['dept_name'] ?? null),
            'trap_type'      => $this->clean($row['trap_type'] ?? null),
            'deck_no'        => $this->clean($row['deck_no'] ?? null),
            'area'           => $this->clean($row['area'] ?? null),
            'location'       => $this->clean($row['location'] ?? null),
            'location_trap'  => $this->clean($row['location_trap'] ?? null),
            'count_type'     => $this->clean($row['count_type'] ?? null),
        ]);
    }

    /**
     * Trim whitespace and normalise empty values to null.
     */
    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
