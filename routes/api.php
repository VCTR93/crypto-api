<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CryptoCurrencyController;
use App\Http\Middleware\JwtMiddleware;

Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware([JwtMiddleware::class])->group(function () {
    Route::apiResource('moneda', CryptoCurrencyController::class);
    Route::apiResource('criptomoneda', CryptoCurrencyController::class)->except(['update', 'destroy']);

    Route::apiResource('cryptos', CryptoCurrencyController::class)->only([
        'index', 'store', 'show'
    ]);
    
    // Opcional: Ruta para actualizar precios
    Route::post('cryptos/{id}/prices', [CryptoCurrencyController::class, 'addPrice']);
});