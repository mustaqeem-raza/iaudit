<?php

use App\Http\Controllers\AuditReportController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Audit listing page
Route::get('/audits', [AuditReportController::class, 'index'])->name('audit.index');

// Preview PDF in browser
Route::get('/audit-report/preview', [AuditReportController::class, 'previewPdf'])->name('audit.report.preview');

Route::get('/audit-report', [AuditReportController::class, 'showReport'])->name('audit.report');
Route::get('/audit-report/pdf', [AuditReportController::class, 'downloadPdf'])->name('audit.report.pdf');
Route::get('/audit-pdf-report', [AuditReportController::class, 'showPDFReport'])->name('audit.report');

// Audit specific routes (with ID)
Route::get('/audit/{id}/report', [AuditReportController::class, 'showReport'])->name('audit.report.show');
Route::get('/audit/{id}/pdf', [AuditReportController::class, 'downloadPdf'])->name('audit.report.pdf.show');
Route::get('/audit/{id}/download', [AuditReportController::class, 'downloadPdf'])->name('audit.download');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
