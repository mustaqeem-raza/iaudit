<?php

namespace App\Imports;

use App\Models\SubheadingIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, Importable};

class SubheadingIauditImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading
{
    use Importable;

    public function model(array $row)
    {
        return new SubheadingIaudit([
            'heading_id' => (int)($row['heading_id'] ?? 0),
            'name'       => $row['name'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.heading_id' => 'required|integer|min:1',
            '*.name'       => 'required|string',
        ];
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
