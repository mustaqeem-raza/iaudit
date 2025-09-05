<?php
namespace App\Imports;

use App\Models\QuestionNcIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, Importable};

class QuestionNcIauditImport implements
    ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        return new QuestionNcIaudit([
            'question_id' => isset($row['question_id']) ? (int)$row['question_id'] : null,
            'nc_heading'  => $row['nc_heading'] ?? null,
            'nc_text'     => $row['nc_text'] ?? null,
            'nc_rem_hd'   => $row['nc_rem_hd'] ?? null,
            'nc_rem_text' => $row['nc_rem_text'] ?? null,
            'nc_con_hd'   => $row['nc_con_hd'] ?? null,
            'nc_con_text' => $row['nc_con_text'] ?? null,
            'nc_usph_hd'  => $row['nc_usph_hd'] ?? null,
            'nc_usph_text'=> $row['nc_usph_text'] ?? null,
            'nc_ipm_hd'   => $row['nc_ipm_hd'] ?? null,
            'nc_ipm_ref'  => $row['nc_ipm_ref'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.question_id' => 'required|integer|min:1', // FK → question_iaudit.question_id:contentReference[oaicite:13]{index=13}
        ];
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
