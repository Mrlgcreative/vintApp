<?php

use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Catalogue public
|--------------------------------------------------------------------------
|
| Lecture ouverte : liste, détail, articles par catégorie / marque. Seuls les
| articles actifs sont exposés.
|
*/
Route::get('items', [ItemController::class, 'index']);
Route::get('items/{item}', [ItemController::class, 'show']);

Route::get('categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
Route::get('categories/{category}/items', [CategoryController::class, 'items']);

Route::get('brands', [BrandController::class, 'index']);
Route::get('brands/{brand}', [BrandController::class, 'show']);
Route::get('brands/{brand}/items', [BrandController::class, 'items']);

/*
|--------------------------------------------------------------------------
| Écriture
|--------------------------------------------------------------------------
|
| Identité résolue via auth-service. Le vendeur est déduit de l'identité, il
| n'est jamais accepté depuis le client. Catégories et marques sont réservées
| aux administrateurs.
|
*/
Route::middleware('service.identity')->group(function () {
    Route::get('me/items', [ItemController::class, 'mine']);

    Route::post('items', [ItemController::class, 'store']);
    Route::match(['put', 'patch'], 'items/{item}', [ItemController::class, 'update']);
    Route::delete('items/{item}', [ItemController::class, 'destroy']);

    Route::post('categories', [CategoryController::class, 'store']);
    Route::match(['put', 'patch'], 'categories/{category}', [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

    Route::post('brands', [BrandController::class, 'store']);
    Route::match(['put', 'patch'], 'brands/{brand}', [BrandController::class, 'update']);
    Route::delete('brands/{brand}', [BrandController::class, 'destroy']);
});
