<?php

namespace App\Imports;

use App\Models\QuestionIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['question_id'])) return null;

        QuestionIaudit::updateOrCreate(
            ['question_id' => (int)$row['question_id']],
            [
                'reference_id'     => !empty($row['reference_id'])  ? (int)$row['reference_id']  : null,
                'heading_id'       => !empty($row['heading_id'])    ? (int)$row['heading_id']    : null,
                'subheading_id'    => !empty($row['subheading_id']) ? (int)$row['subheading_id'] : null,
                'category_id'      => !empty($row['category_id'])   ? (int)$row['category_id']   : null,
                'question_text'    => $row['question_text']    ?? null,
                'information_text' => $row['information_text'] ?? null,
            ]
        );

        return null;
    }
}
