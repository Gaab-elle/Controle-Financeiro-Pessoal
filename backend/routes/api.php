<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContaController;
use App\Http\Controllers\LancamentoController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'resumo']);

    // Contas
    Route::apiResource('contas', ContaController::class);

    // Lançamentos
    Route::apiResource('lancamentos', LancamentoController::class);
});

