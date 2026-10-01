<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenIntrospectionController;
use App\Http\Controllers\TwoFactorAuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me'])->middleware('introspect');

    // Le token 2FA en attente doit passer avant l'introspection complète.
    Route::post('two-factor/verify', [TwoFactorAuthController::class, 'verify']);

    Route::middleware('introspect')->group(function () {
        Route::post('two-factor/enable', [TwoFactorAuthController::class, 'enable']);
        Route::post('two-factor/confirm', [TwoFactorAuthController::class, 'confirm']);
        Route::post('two-factor/disable', [TwoFactorAuthController::class, 'disable']);
        Route::post('two-factor/regenerate-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes']);
    });
});

/**
 * Appelé par les autres microservices pour valider un token sans partager
 * la table users. Exige la signature de service + un Bearer token.
 */
Route::post('token/introspect', [TokenIntrospectionController::class, 'introspect'])
    ->middleware('service');
