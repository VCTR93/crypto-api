<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CryptoCurrencyController;
use App\Http\Middleware\JwtMiddleware;

Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware([JwtMiddleware::class])->group(function () {
    Route::apiResource('moneda', CryptoCurrencyController::class);
    Route::apiResource('criptomoneda', CryptoCurrencyController::class)->except(['update', 'destroy']);
});