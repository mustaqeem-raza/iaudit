<?php
namespace App\Imports;

use App\Models\QuestionIaudit;
use Maatwebsite\Excel\Concerns\{ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation, Importable};

class QuestionIauditImport implements
    ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, WithValidation
{
    use Importable;

    public function model(array $row)
    {
        return new QuestionIaudit([
            'topic'            => $row['topic'] ?? null, // enum: IPM Docs,PCL,Pest Obs,EFK,RG,CRT:contentReference[oaicite:11]{index=11}
            'reference_code'   => $row['reference_code'] ?? null,
            'heading_id'       => isset($row['heading_id']) ? (int)$row['heading_id'] : null,
            'subheading_id'    => isset($row['subheading_id']) ? (int)$row['subheading_id'] : null,
            'category_id'      => isset($row['category_id']) ? (int)$row['category_id'] : null,
            'question_text'    => $row['question_text'] ?? null,
            'information_text' => $row['information_text'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            '*.topic' => 'required|in:IPM Docs,PCL,Pest Obs,EFK,RG,CRT', // schema enum:contentReference[oaicite:12]{index=12}
        ];
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}
