<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\AppleAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Routes d'inscription
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('register', [RegisterController::class, 'register'])
        ->middleware(['throttle:auth.register', 'security.log.logins']); // email+IP : max 5/min + journalisation

    // Routes de connexion
    Route::get('login', [LoginController::class, 'showLoginForm'])
        ->name('login');

    Route::post('login', [LoginController::class, 'login'])
        ->middleware(['throttle:auth.login', 'security.log.logins']); // email+IP : max 5/min (protection brute force) + journalisation

    // Routes Google OAuth
    Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])
        ->name('auth.google');

    Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback'])
        ->name('auth.google.callback');

    // Routes Apple OAuth
    Route::get('auth/apple', [AppleAuthController::class, 'redirectToApple'])
        ->name('auth.apple');

    Route::post('auth/apple/callback', [AppleAuthController::class, 'handleAppleCallback'])
        ->name('auth.apple.callback');

    // Routes de réinitialisation de mot de passe
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware(['throttle:auth.password', 'security.log.logins'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware(['throttle:auth.password', 'security.log.logins'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Routes de vérification d'email (ancien système avec lien)
    Route::get('verify-email', [EmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Routes de vérification par code (nouveau système)
    Route::get('verify-code', [EmailVerificationController::class, 'showCodeForm'])
        ->name('verification.code');

    Route::post('verify-code', [EmailVerificationController::class, 'verifyCode'])
        ->middleware('throttle:5,1')
        ->name('verification.code.verify');

    Route::post('verify-code/resend', [EmailVerificationController::class, 'resendCode'])
        ->middleware('throttle:3,1')
        ->name('verification.code.resend');

    // Routes de confirmation de mot de passe
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Routes de gestion du mot de passe
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Route de déconnexion
    Route::post('logout', [LoginController::class, 'logout'])
        ->name('logout');
});
