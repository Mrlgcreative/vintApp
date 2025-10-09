<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\WalletController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques
Route::get('/health', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'VintApp API is running',
        'version' => '1.0.0'
    ]);
});

// Routes d'authentification (si vous utilisez Sanctum)
Route::post('/register', function (Request $request) {
    // Logique d'inscription
    return response()->json(['message' => 'Register endpoint']);
});

Route::post('/login', function (Request $request) {
    // Logique de connexion
    return response()->json(['message' => 'Login endpoint']);
});

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    
    // User routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'profile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/avatar', [UserController::class, 'uploadAvatar']);
        Route::get('/stats', [UserController::class, 'getStats']);
        Route::get('/items', [UserController::class, 'getItems']);
        Route::get('/orders', [UserController::class, 'getOrders']);
        Route::get('/sales', [UserController::class, 'getSales']);
        Route::get('/reviews', [UserController::class, 'getReviews']);
        Route::delete('/account', [UserController::class, 'destroy']);
    });

    // Items routes
    Route::prefix('items')->group(function () {
        Route::get('/', [ItemController::class, 'index']);
        Route::post('/', [ItemController::class, 'store']);
        Route::get('/{item}', [ItemController::class, 'show']);
        Route::put('/{item}', [ItemController::class, 'update']);
        Route::delete('/{item}', [ItemController::class, 'destroy']);
        Route::post('/{item}/favorite', [ItemController::class, 'toggleFavorite']);
        Route::get('/search', [ItemController::class, 'search']);
    });

    // Orders routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });

    // Messages routes
    Route::prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::post('/', [MessageController::class, 'store']);
        Route::get('/{message}', [MessageController::class, 'show']);
        Route::put('/{message}', [MessageController::class, 'update']);
        Route::delete('/{message}', [MessageController::class, 'destroy']);
        Route::get('/conversations', [MessageController::class, 'conversations']);
    });

    // Reviews routes
    Route::prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::get('/{review}', [ReviewController::class, 'show']);
        Route::put('/{review}', [ReviewController::class, 'update']);
        Route::delete('/{review}', [ReviewController::class, 'destroy']);
    });

    // Notifications routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('/stats', [NotificationController::class, 'getStats']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
    });

    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/analytics', [DashboardController::class, 'analytics']);
        Route::get('/user', [DashboardController::class, 'userDashboard']);
        Route::get('/data', [DashboardController::class, 'apiData']);
    });

    // Categories routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{category}', [CategoryController::class, 'show']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    // Brands routes
    Route::prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::post('/', [BrandController::class, 'store']);
        Route::get('/{brand}', [BrandController::class, 'show']);
        Route::put('/{brand}', [BrandController::class, 'update']);
        Route::delete('/{brand}', [BrandController::class, 'destroy']);
    });
});

// Routes Admin protégées
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Routes API admin ici si nécessaire
});

// Route pour obtenir l'utilisateur authentifié
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/bot', [BotController::class, 'ask']);