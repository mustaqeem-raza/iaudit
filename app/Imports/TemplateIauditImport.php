<?php

namespace App\Imports;

use App\Models\TemplateIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, Importable, WithUpserts};

class TemplateIauditImport implements ToModel, WithHeadingRow, WithValidation, WithBatchInserts, WithChunkReading, WithUpserts
{
    use Importable;

    public function model(array $row)
    {
        return new TemplateIaudit([
            'template_code' => $row['template_code'] ?? null,
            'report_title'  => $row['report_title'] ?? null,
            'department'    => $row['department'] ?? null,
        ]);
    }

    public function uniqueBy() { return 'template_code'; }
    public function rules(): array { return ['*.template_code' => 'required|string']; }
    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
