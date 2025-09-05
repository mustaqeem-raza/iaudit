<?php

namespace App\Imports;

use App\Models\HeadingIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, Importable, WithUpserts};

class HeadingIauditImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithUpserts
{
    use Importable;

    public function model(array $row)
    {
        return new HeadingIaudit([
            'name' => $row['name'] ?? null,
        ]);
    }

    public function uniqueBy() { return 'name'; }
    public function rules(): array { return ['*.name' => 'required|string']; }
    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
