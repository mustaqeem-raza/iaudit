<?php

namespace App\Imports;

use App\Models\TextBoxIaudit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TextBoxImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Support both exact and slugged headings
        $id = $row['text_box_id'] ?? $row['Text_Box_ID'] ?? null;
        if (empty($id)) return null;

        TextBoxIaudit::updateOrCreate(
            ['text_box_id' => (int) $id],  // unique by PK
            [
                'reference_code' => $row['reference_code'] ?? $row['Reference_Code'] ?? null,
                'reference_id'   => isset($row['reference_id']) ? (int)$row['reference_id']
                                   : (isset($row['Reference_ID']) ? (int)$row['Reference_ID'] : null),
                'main_heading'   => $row['main_heading'] ?? $row['Main_Heading'] ?? null,
                'body'           => $row['body'] ?? $row['Body'] ?? null,
            ]
        );

        return null; // we already saved
    }
}
