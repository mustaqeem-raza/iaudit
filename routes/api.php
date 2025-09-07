<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Public
Route::post('/login', [ApiController::class, 'login']);

// Protected (requires Bearer token from /api/login)
Route::middleware('auth:sanctum')->group(function () {
    // logout by deleting current token
    Route::post('/logout',  [ApiController::class, 'logout']);       // POST logout (delete token)
    Route::get('/departments', [ApiController::class, 'departments']);          // GET all departments
    Route::get('/companies', [ApiController::class, 'companies']);          // GET company → fleet → ship
    
    Route::get('/audit-questions', [ApiController::class, 'auditQuestions']); // GET questions?ship_id=123
    Route::post('/audits', [ApiController::class, 'storeAudit']);             // POST audit submit
});