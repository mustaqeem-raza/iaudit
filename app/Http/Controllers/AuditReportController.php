<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

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
        $html = view('audit-pdf-report-bkp')->render();

        file_put_contents(
            storage_path('app/debug-audit-report.html'),
            $html
        );

        $pdf = Browsershot::html($html)
            ->setNodeBinary('/usr/bin/node')
            ->setNpmBinary('/usr/bin/npm')
            ->setChromePath('/usr/bin/google-chrome')
            ->noSandbox()
            ->disableJavascript()
            ->setOption('waitUntil', 'load')
            ->timeout(120)
            ->format('A4')
            ->pdf();

        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header(
                'Content-Disposition',
                'inline; filename="audit-report.pdf"'
            );
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

        $pdf = Pdf::loadView('audit-report', compact('data'));
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

        $pdf = Pdf::loadView('audit-report-bootstrap', compact('data'));
        return $pdf->stream('audit-report-bootstrap.pdf'); // Opens in browser
    }
}
