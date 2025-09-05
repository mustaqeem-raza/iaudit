<?php
namespace App\Imports;

use App\Models\TemplateReferenceIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, Importable, WithUpserts};

class TemplateReferenceIauditImport implements
    ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, WithUpserts
{
    use Importable;

    public function model(array $row)
    {
        return new TemplateReferenceIaudit([
            'template_code'  => $row['template_code'] ?? null, // FK → template.template_code:contentReference[oaicite:10]{index=10}
            'reference_code' => $row['reference_code'] ?? null,
        ]);
    }

    public function uniqueBy() { return ['template_code', 'reference_code']; }
    public function rules(): array
    {
        return [
            '*.template_code'  => 'required|string',
            '*.reference_code' => 'required|string',
        ];
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
