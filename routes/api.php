<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Public
Route::post('/login', [ApiController::class, 'login']);

// Protected (requires Bearer token from /api/login)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout',  [ApiController::class, 'logout']);       //auditQuestions POST logout (delete token)
    Route::get('/companies', [ApiController::class, 'companies']);   // GET company → fleet → ship
    Route::get('/questions', [ApiController::class, 'questions']);   // GET all questions
    Route::post('/answers', [ApiController::class, 'submitAudit']);  // POST answers (submit audit)

});
