<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PessoaController;
use App\Http\Controllers\Api\FilmeController;
use App\Http\Controllers\Api\LocacaoController;
use App\Http\Controllers\Api\PerfilController;

// Rotas públicas
Route::post('/login', [AuthController::class, 'login']);
Route::get('/filmes', [FilmeController::class, 'index']);
Route::get('/filmes/{id}', [FilmeController::class, 'show']);

// Rotas protegidas por JWT
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/me', [AuthController::class, 'me']);

    // Perfil
    Route::prefix('perfil')->group(function () {
        Route::get('/', [PerfilController::class, 'show']);
        Route::put('/', [PerfilController::class, 'update']);
        Route::put('/senha', [PerfilController::class, 'updatePassword']);
        Route::post('/foto', [PerfilController::class, 'uploadFoto']);
    });

    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('pessoas', PessoaController::class)->except(['store']);
        Route::apiResource('filmes', FilmeController::class)->except(['index', 'show']);
    });

    // Admin e Atendente
    Route::middleware('role:admin,attendant')->group(function () {
        Route::post('/pessoas/cliente', [PessoaController::class, 'storeCliente']);
        Route::apiResource('locacoes', LocacaoController::class)->except(['update', 'destroy']);
        Route::put('/locacoes/{id}/devolver', [LocacaoController::class, 'devolver']);
    });

    // Cliente
    Route::middleware('role:customer')->group(function () {
        Route::get('/minhas-locacoes', [LocacaoController::class, 'minhasLocacoes']);
    });
});
