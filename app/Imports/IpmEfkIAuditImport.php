<?php

namespace App\Imports;

use App\Models\IpmEfkIAudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IpmEfkIAuditImport implements ToModel, WithHeadingRow
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

        return new IpmEfkIAudit([
            'ship_name'      => $shipName,
            'mnemonic_both'  => $this->clean($row['mnemonic_both'] ?? null),
            // CSV header has a typo "Mnemmonic_Fleet" -> mnemmonic_fleet
            'mnemonic_fleet' => $this->clean($row['mnemonic_fleet'] ?? $row['mnemmonic_fleet'] ?? null),
            'mnemonic_ship'  => $this->clean($row['mnemonic_ship'] ?? null),
            'efk_type'       => $this->clean($row['efk_type'] ?? null),
            'deck_no'        => $this->clean($row['deck_no'] ?? null),
            'department'     => $this->clean($row['department'] ?? null),
            'area'           => $this->clean($row['area'] ?? null),
            'location'       => $this->clean($row['location'] ?? null),
            'install_date'   => $this->clean($row['install_date'] ?? null),
            'type_uvt'       => $this->clean($row['type_uvt'] ?? null),
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
