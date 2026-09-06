<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

// Public
Route::post('/login', [ApiController::class, 'login']);

// Protected (requires Bearer token from /api/login)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout',  [ApiController::class, 'logout']);       //auditQuestions POST logout (delete token)
    Route::get('/companies', [ApiController::class, 'companies']);   // GET company → fleet → ship
    Route::get('/questions', [ApiController::class, 'questions']);   // GET all questions (v1 — unchanged, currently-deployed app)
    Route::get('/v2/questions', [ApiController::class, 'questionsV2']); // GET all questions, new per-template schema (see refactor-schema.md)
    Route::post('/answers', [ApiController::class, 'submitAudit']);  // POST answers (submit audit)
    Route::get('/trap-locations', [ApiController::class, 'trapLocations']);
    Route::get('/efk-locations', [ApiController::class, 'efkLocations']);
    Route::get('/other-crt-locations', [ApiController::class, 'otherCrtLocations']);
    Route::get('/other-efk-locations', [ApiController::class, 'otherEfkLocations']);
    Route::get('/ipm-efk-locations', [ApiController::class, 'ipmEfkLocations']);
    Route::get('/ipm-trap-locations', [ApiController::class, 'ipmTrapLocations']);
});
