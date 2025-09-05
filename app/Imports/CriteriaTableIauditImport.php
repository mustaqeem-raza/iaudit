<?php
namespace App\Imports;

use App\Models\CriteriaTableIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, Importable};

class CriteriaTableIauditImport implements
    ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        return new CriteriaTableIaudit([
            'reference_code' => $row['reference_code'] ?? null,
            'main_heading'   => $row['main_heading'] ?? null,
            'table_heading'  => $row['table_heading'] ?? null,
            'question'       => $row['question'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.reference_code' => 'required|string',
            '*.main_heading'   => 'required|string',
            '*.table_heading'  => 'required|string',
            '*.question'       => 'required|string',
        ];
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
