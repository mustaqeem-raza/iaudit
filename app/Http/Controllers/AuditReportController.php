<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditReportController extends Controller
{
    public function showReport()
    {
        $data = [
            'company' => 'Oceanic Cruises',
            'fleet' => 'Pacific Fleet',
            'ship' => 'Sea Explorer',
            'audit_date' => '2025-08-14',
            'auditor' => 'John Doe',
            'questions' => [
                ['code' => 'Q1', 'question' => 'Is the kitchen clean?', 'answer' => 'Yes'],
                ['code' => 'Q2', 'question' => 'Are safety protocols followed?', 'answer' => 'No'],
                ['code' => 'Q3', 'question' => 'Is documentation up to date?', 'answer' => 'Yes'],
            ]
        ];

        return view('audit-report', compact('data'));
        // return view('audit-report-bootstrap', compact('data'));
    }

    public function showPDFReport()
    {
        return view('audit-pdf-report');
    }

    public function downloadPdf()
    {
        $data = [
            'company' => 'Oceanic Cruises',
            'fleet' => 'Pacific Fleet',
            'ship' => 'Sea Explorer',
            'audit_date' => '2025-08-14',
            'auditor' => 'John Doe',
            'questions' => [
                ['code' => 'Q1', 'question' => 'Is the kitchen clean?', 'answer' => 'Yes'],
                ['code' => 'Q2', 'question' => 'Are safety protocols followed?', 'answer' => 'No'],
                ['code' => 'Q3', 'question' => 'Is documentation up to date?', 'answer' => 'Yes'],
            ]
        ];

        $pdf = downloadPdf::loadView('audit-report', compact('data'));
        return $pdf->download('audit-report.pdf');
    }

    public function previewPdf()
    {
        $data = [
            'company' => 'Oceanic Cruises',
            'fleet' => 'Pacific Fleet',
            'ship' => 'Sea Explorer',
            'audit_date' => '2025-08-14',
            'auditor' => 'John Doe',
            'questions' => [
                ['code' => 'Q1', 'question' => 'Is the kitchen clean?', 'answer' => 'Yes'],
                ['code' => 'Q2', 'question' => 'Are safety protocols followed?', 'answer' => 'No'],
                ['code' => 'Q3', 'question' => 'Is documentation up to date?', 'answer' => 'Yes'],
            ]
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('audit-report-bootstrap', compact('data'));
        return $pdf->stream('audit-report-bootstrap.pdf'); // Opens in browser
    }
}
