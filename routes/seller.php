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
});
