<?php

use App\Http\Controllers\AuditReportController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Audit listing page
Route::get('/audits', [AuditReportController::class, 'index'])->name('audit.index');

// Preview PDF in browser
// Route::get('/audit-report/preview', [AuditReportController::class, 'previewPdf'])->name('audit.report.preview');

// Route::get('/audit-report/pdf', [AuditReportController::class, 'downloadPdf'])->name('audit.report.pdf');
// Route::get('/audit-report', [AuditReportController::class, 'showReport'])->name('audit.report');
// Route::get('/audit-pdf-report', [AuditReportController::class, 'showPDFReport'])->name('audit.pdf.report');

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Audit specific routes (with ID)
    Route::get('/static-report', [AuditReportController::class, 'staticReport'])->name('audit.report.static');
    Route::get('/audit/{id}/report', [AuditReportController::class, 'showReport'])->name('audit.report.show');
    Route::get('/audit/{id}/pdf', [AuditReportController::class, 'showPDFReport'])->name('audit.report.pdf.show');
    Route::get('/audit/{id}/download', [AuditReportController::class, 'downloadPdf'])->name('audit.download');

    Route::get('/dashboard', [AuditReportController::class, 'index'])->name('dashboard');

    // Admin: import the question bank workbook (Week 1 of the IPM schema update)
    Route::get('/questions/import', [ExcelImportController::class, 'showImportForm'])->name('questions.import.show');
    Route::post('/questions/import', [ExcelImportController::class, 'import'])->name('questions.import.store');

    // Database management sections not built yet: each shows a "Coming soon"
    // page (text in config/coming-soon.php) until its week of the delivery plan.
    Route::view('/questions', 'coming-soon', config('coming-soon.questions'))->name('questions.index');
    Route::view('/versions', 'coming-soon', config('coming-soon.versions'))->name('versions.index');
    Route::view('/audit-trail', 'coming-soon', config('coming-soon.audit-trail'))->name('audit-trail.index');
    Route::view('/access', 'coming-soon', config('coming-soon.access'))->name('access.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
