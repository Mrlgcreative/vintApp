<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SellerController;

Route::middleware(['auth', 'verified', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::get('/items', [SellerController::class, 'items'])->name('items');
    Route::get('/sales', [SellerController::class, 'sales'])->name('sales');
    Route::get('/wallet', [SellerController::class, 'wallet'])->name('wallet');
    Route::get('/categories', [SellerController::class, 'categories'])->name('categories');
    Route::get('/brands', [SellerController::class, 'brands'])->name('brands');
    Route::get('/reviews', [SellerController::class, 'reviews'])->name('reviews');

    // Offres / promotions du vendeur (sur ses propres produits)
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::get('/', [App\Http\Controllers\Marketing\OfferController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Marketing\OfferController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Marketing\OfferController::class, 'store'])->name('store');
        Route::get('/{offer}/edit', [App\Http\Controllers\Marketing\OfferController::class, 'edit'])->name('edit');
        Route::put('/{offer}', [App\Http\Controllers\Marketing\OfferController::class, 'update'])->name('update');
        Route::delete('/{offer}', [App\Http\Controllers\Marketing\OfferController::class, 'destroy'])->name('destroy');
        Route::patch('/{offer}/status', [App\Http\Controllers\Marketing\OfferController::class, 'toggleStatus'])->name('status');
    });
});
