<?php

use App\Http\Controllers\AuditReportController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\OverviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use Illuminate\Support\Facades\Route;

// Audit listing page
Route::get('/audits', [AuditReportController::class, 'index'])->name('audit.index');

// Preview PDF in browser
// Route::get('/audit-report/preview', [AuditReportController::class, 'previewPdf'])->name('audit.report.preview');

// Route::get('/audit-report/pdf', [AuditReportController::class, 'downloadPdf'])->name('audit.report.pdf');
// Route::get('/audit-report', [AuditReportController::class, 'showReport'])->name('audit.report');
// Route::get('/audit-pdf-report', [AuditReportController::class, 'showPDFReport'])->name('audit.pdf.report');

// The site root is the admin panel: guests are sent on to the login page by the auth middleware.
Route::redirect('/', '/dashboard');

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

    // Database management, Week 1: the question bank. {id} is digits only,
    // so it can never capture /questions/import.
    Route::get('/questions', [QuestionController::class, 'index'])->name('questions.index');
    Route::get('/questions/create', [QuestionController::class, 'create'])->name('questions.create');
    Route::post('/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::get('/questions/{id}/edit', [QuestionController::class, 'edit'])->whereNumber('id')->name('questions.edit');
    Route::put('/questions/{id}', [QuestionController::class, 'update'])->whereNumber('id')->name('questions.update');
    Route::patch('/questions/{id}/retire', [QuestionController::class, 'retire'])->whereNumber('id')->name('questions.retire');
    Route::patch('/questions/{id}/reactivate', [QuestionController::class, 'reactivate'])->whereNumber('id')->name('questions.reactivate');
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy'])->whereNumber('id')->name('questions.destroy');

    // Database management overview: live numbers and each capability's progress.
    Route::get('/overview', [OverviewController::class, 'index'])->name('overview.index');

    // Database management sections not built yet: each shows a "Coming soon" page
    // (text in config/database-management.php) until its week of the delivery plan.
    Route::view('/versions', 'coming-soon', config('database-management.coming_soon.versions'))->name('versions.index');
    Route::view('/audit-trail', 'coming-soon', config('database-management.coming_soon.audit-trail'))->name('audit-trail.index');
    Route::view('/access', 'coming-soon', config('database-management.coming_soon.access'))->name('access.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
