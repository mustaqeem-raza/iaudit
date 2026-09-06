<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Thin sheet-selection wrapper around QuestionDataSheetImport.
 *
 * The workbook's tabs can be named anything — the target sheet is identified
 * by its *content* (a row with Criteria=Template and Title="Audit Report",
 * see ExcelImportController::detectDataSheetName()), not by a hardcoded tab
 * name or the uploaded filename. The caller resolves that sheet name once
 * up front and passes it in here.
 */
class QuestionWorkbookImport implements WithMultipleSheets
{
    public function __construct(
        private QuestionDataSheetImport $dataSheet,
        private string $sheetName
    ) {
    }

    public function sheets(): array
    {
        return [
            $this->sheetName => $this->dataSheet,
        ];
    }
}
