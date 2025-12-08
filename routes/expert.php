<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpertController;

/*
|--------------------------------------------------------------------------
| Expert Routes
|--------------------------------------------------------------------------
|
| Routes spécifiques aux experts pour la vérification d'authenticité
| Toutes ces routes sont protégées par les middlewares 'auth' et 'expert'
|
*/

Route::middleware(['auth', 'expert'])->prefix('expert')->name('expert.')->group(function () {
    
    // Dashboard principal
    Route::get('/', [ExpertController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [ExpertController::class, 'dashboard'])->name('dashboard.main');
    
    // Gestion des vérifications
    Route::get('/verifications', [ExpertController::class, 'verifications'])->name('verifications.index');
    Route::get('/verifications/{check}', [ExpertController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{check}/start', [ExpertController::class, 'startReview'])->name('verifications.start');
    Route::post('/verifications/{check}/finalize', [ExpertController::class, 'finalize'])->name('verifications.finalize');
    
    // Vérification des articles en attente
    Route::get('/items/pending', [ExpertController::class, 'pendingItems'])->name('items.pending');
    Route::get('/items/{item}/verify', [ExpertController::class, 'showItemForVerification'])->name('items.show-for-verification');
    Route::post('/items/{item}/verify', [ExpertController::class, 'submitItemVerification'])->name('items.submit-verification');
    
    // Profil et paramètres
    Route::get('/profile', [ExpertController::class, 'profile'])->name('profile');
    Route::put('/profile', [ExpertController::class, 'updateProfile'])->name('profile.update');
    
});