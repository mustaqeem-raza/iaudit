<?php

namespace App\Http\Controllers;

use App\Imports\QuestionDataSheetImport;
use App\Imports\QuestionWorkbookImport;
use App\Models\CriteriaIaudit;
use App\Models\QuestionIaudit;
use App\Models\TextBoxIaudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class ExcelImportController extends Controller
{
    /** The only signal that identifies the question-bank sheet — not its tab name, not the filename. */
    private const MARKER_TITLE = 'Audit Report';

    public function showImportForm()
    {
        return view('questions-import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'workbook' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        $file = $request->file('workbook');

        // Stored on the private 'local' disk (storage/app/private) as a
        // lightweight audit trail — not served to anyone, so it doesn't
        // depend on the public/storage symlink. Timestamped so re-uploads
        // never collide.
        $storedPath = $file->storeAs(
            'question-imports',
            now()->format('Ymd_His') . '_' . $file->getClientOriginalName(),
            'local'
        );

        $absolutePath = Storage::disk('local')->path($storedPath);

        try {
            $sheetName = $this->detectDataSheetName($absolutePath);
        } catch (Throwable $e) {
            // The mimes:xlsx,xls rule above only checks the file extension —
            // a corrupted or non-spreadsheet file with that extension still
            // passes it and reaches IOFactory::load() here, which throws.
            // Without this, that's an uncaught 500 instead of the same
            // friendly, actionable message every other import failure gets.
            Log::error('Question import: could not read uploaded file — ' . $e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'This file could not be read as a spreadsheet. It may be corrupted or not a real .xlsx/.xls file.');
        }

        if ($sheetName === null) {
            return back()->with(
                'error',
                'This doesn\'t look like a question workbook — no sheet has a row with '
                    . 'Criteria = "Template" and Title = "' . self::MARKER_TITLE . '". '
                    . 'The sheet\'s name and the file\'s name don\'t matter, only that marker row does.'
            );
        }

        $importer = new QuestionDataSheetImport();

        try {
            Excel::import(
                new QuestionWorkbookImport($importer, $sheetName),
                $absolutePath
            );
        } catch (Throwable $e) {
            Log::error('Question import failed: ' . $e->getMessage(), ['exception' => $e]);

            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }

        Log::info('Question import completed', [
            'file'               => $file->getClientOriginalName(),
            'template'           => $importer->templateName,
            'replaced'           => $importer->replaced,
            'created'            => $importer->created,
            'updated'            => $importer->updated,
            'skipped'            => count($importer->skipped),
            'blank_rows_ignored' => $importer->blankRowsIgnored,
        ]);

        // Live totals for this template right now, straight from the DB —
        // not just this run's counters — so the admin sees an authoritative
        // "here's what's actually in the database" figure, not only "here's
        // what this one run touched".
        $liveTotals = [
            'question' => QuestionIaudit::where('template', $importer->templateName)
                ->where('row_type', 'question')->count(),
            'text'     => TextBoxIaudit::where('template', $importer->templateName)->count(),
            'criteria' => CriteriaIaudit::where('template', $importer->templateName)->count(),
        ];

        return back()
            ->with('success', 'Import completed.')
            ->with('importSummary', [
                'template'         => $importer->templateName,
                'replaced'         => $importer->replaced,
                'created'          => $importer->created,
                'updated'          => $importer->updated,
                'liveTotals'       => $liveTotals,
                'skipped'          => $importer->skipped,
                'warnings'         => $importer->warnings,
                'blankRowsIgnored' => $importer->blankRowsIgnored,
            ]);
    }

    /**
     * Find which sheet in the uploaded workbook is the question-bank `Data`
     * sheet — by content, not by tab name or the uploaded filename. A sheet
     * qualifies only if it has both a `Criteria` and a `Title` header column
     * and at least one row where Criteria = "Template" and
     * Title = self::MARKER_TITLE ("Audit Report"). Returns null if no sheet
     * in the workbook matches.
     */
    private function detectDataSheetName(string $absolutePath): ?string
    {
        $spreadsheet = IOFactory::load($absolutePath);

        foreach ($spreadsheet->getSheetNames() as $name) {
            $sheet = $spreadsheet->getSheetByName($name);

            if ($this->sheetHasMarkerRow($sheet)) {
                return $name;
            }
        }

        return null;
    }

    private function sheetHasMarkerRow(Worksheet $sheet): bool
    {
        $headerRow = $sheet->rangeToArray('A1:' . $sheet->getHighestColumn() . '1')[0];

        $criteriaCol = null;
        $titleCol = null;

        foreach ($headerRow as $index => $header) {
            $header = strtolower(trim((string) $header));
            if ($header === 'criteria') {
                $criteriaCol = $index + 1; // 1-based column number
            } elseif ($header === 'title') {
                $titleCol = $index + 1;
            }
        }

        if ($criteriaCol === null || $titleCol === null) {
            return false;
        }

        $highestRow = $sheet->getHighestRow();

        for ($row = 2; $row <= $highestRow; $row++) {
            $criteriaValue = strtolower(trim((string) $sheet->getCellByColumnAndRow($criteriaCol, $row)->getValue()));
            $titleValue = trim((string) $sheet->getCellByColumnAndRow($titleCol, $row)->getValue());

            if ($criteriaValue === 'template' && strcasecmp($titleValue, self::MARKER_TITLE) === 0) {
                return true;
            }
        }

        return false;
    }
}
