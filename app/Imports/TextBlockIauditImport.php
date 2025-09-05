<?php
namespace App\Imports;

use App\Models\TextBlockIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, Importable};

class TextBlockIauditImport implements
    ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        return new TextBlockIaudit([
            'reference_code' => $row['reference_code'] ?? null,
            'main_heading'   => $row['main_heading'] ?? null,
            'body'           => $row['body'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.reference_code' => 'required|string',
            '*.main_heading'   => 'required|string',
        ];
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
