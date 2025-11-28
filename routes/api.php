<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentCallbackController;
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
Route::middleware(['cache.response:60', 'compress.response'])->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'VintApp API is running',
            'version' => '1.0.0'
        ]);
    });
});

// Validation de code de parrainage (public pour l'inscription)
Route::middleware(['throttle:10,1'])->post('/validate-referral-code', [App\Http\Controllers\AffiliateController::class, 'validateReferralCode']);

// Routes de callback pour les paiements (publiques car appelées par les opérateurs)
Route::prefix('payment-callbacks')->group(function () {
    // Callback universel pour chaque opérateur
    Route::post('/{provider}', [PaymentCallbackController::class, 'handleCallback'])
        ->name('payment.callback')
        ->middleware('throttle:100,1') // Max 100 callbacks par minute (protection DDoS)
        ->where('provider', 'mpesa|orange_money|airtel_money|africell|illicocash');
    
    // Endpoint pour vérifier le statut (polling)
    Route::get('/status', [PaymentCallbackController::class, 'checkStatus'])
        ->middleware('throttle:30,1') // Max 30 requêtes par minute
        ->name('payment.status');
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
Route::middleware(['auth:sanctum,web', 'compress.response'])->group(function () {
    
    // User routes (rate limit: 60/min)
    Route::middleware('throttle:60,1')->prefix('user')->group(function () {
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

    // Items routes (rate limit: 100/min pour GET, 20/min pour modifications)
    Route::prefix('items')->group(function () {
        Route::middleware(['throttle:100,1', 'cache.response:180'])->get('/', [ItemController::class, 'index']);
        Route::middleware('throttle:20,1')->post('/', [ItemController::class, 'store']);
        Route::middleware(['throttle:100,1', 'cache.response:120'])->get('/{item}', [ItemController::class, 'show']);
        Route::middleware('throttle:20,1')->put('/{item}', [ItemController::class, 'update']);
        Route::middleware('throttle:10,1')->delete('/{item}', [ItemController::class, 'destroy']);
        Route::middleware('throttle:30,1')->post('/{item}/favorite', [ItemController::class, 'toggleFavorite']);
        Route::middleware(['throttle:100,1', 'cache.response:120'])->get('/search', [ItemController::class, 'search']);
    });

    // Orders routes (rate limit: 40/min)
    Route::middleware('throttle:40,1')->prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{order}', [OrderController::class, 'show']);
        Route::put('/{order}', [OrderController::class, 'update']);
        Route::delete('/{order}', [OrderController::class, 'destroy']);
    });

    // Messages routes (rate limit: 50/min)
    Route::middleware('throttle:50,1')->prefix('messages')->group(function () {
        Route::get('/', [MessageController::class, 'index']);
        Route::post('/', [MessageController::class, 'store']);
        Route::get('/{message}', [MessageController::class, 'show']);
        Route::put('/{message}', [MessageController::class, 'update']);
        Route::delete('/{message}', [MessageController::class, 'destroy']);
        Route::get('/conversations', [MessageController::class, 'conversations']);
    });

    // Reviews routes (rate limit: 20/min)
    Route::middleware('throttle:20,1')->prefix('reviews')->group(function () {
        Route::get('/', [ReviewController::class, 'index']);
        Route::post('/', [ReviewController::class, 'store']);
        Route::get('/{review}', [ReviewController::class, 'show']);
        Route::put('/{review}', [ReviewController::class, 'update']);
        Route::delete('/{review}', [ReviewController::class, 'destroy']);
    });

    // Notifications routes (rate limit: 60/min)
    Route::middleware('throttle:60,1')->prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::put('/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::get('/stats', [NotificationController::class, 'getStats']);
        Route::delete('/{notification}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
    });

    // Dashboard routes (rate limit: 30/min)
    Route::middleware('throttle:30,1')->prefix('dashboard')->group(function () {
        Route::get('/analytics', [DashboardController::class, 'analytics']);
        Route::get('/user', [DashboardController::class, 'userDashboard']);
        Route::get('/data', [DashboardController::class, 'apiData']);
    });

    // Categories routes (rate limit: 100/min avec cache)
    Route::middleware(['throttle:100,1', 'cache.response:3600'])->prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::get('/{category}', [CategoryController::class, 'show']);
    });
    
    Route::middleware('throttle:10,1')->prefix('categories')->group(function () {
        Route::post('/', [CategoryController::class, 'store']);
        Route::put('/{category}', [CategoryController::class, 'update']);
        Route::delete('/{category}', [CategoryController::class, 'destroy']);
    });

    // Brands routes (rate limit: 100/min avec cache)
    Route::middleware(['throttle:100,1', 'cache.response:3600'])->prefix('brands')->group(function () {
        Route::get('/', [BrandController::class, 'index']);
        Route::get('/{brand}', [BrandController::class, 'show']);
    });
    
    Route::middleware('throttle:10,1')->prefix('brands')->group(function () {
        Route::post('/', [BrandController::class, 'store']);
        Route::put('/{brand}', [BrandController::class, 'update']);
        Route::delete('/{brand}', [BrandController::class, 'destroy']);
    });

    // Affiliate routes (rate limit: 30/min)
    Route::middleware('throttle:30,1')->prefix('affiliate')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\AffiliateController::class, 'dashboard']);
        Route::get('/referral-codes', [App\Http\Controllers\AffiliateController::class, 'getReferralCodes']);
        Route::post('/referral-codes', [App\Http\Controllers\AffiliateController::class, 'createReferralCode']);
        Route::post('/referral-codes/custom', [App\Http\Controllers\AffiliateController::class, 'createCustomReferralCode']);
        Route::get('/referrals', [App\Http\Controllers\AffiliateController::class, 'getReferrals']);
        Route::get('/points-history', [App\Http\Controllers\AffiliateController::class, 'getPointsHistory']);
        Route::post('/convert-points', [App\Http\Controllers\AffiliateController::class, 'convertPointsToCash']);
        Route::post('/calculate-conversion', [App\Http\Controllers\AffiliateController::class, 'calculateConversion']);
        Route::get('/redemptions', [App\Http\Controllers\AffiliateController::class, 'getRedemptions']);
        Route::post('/apply-referral-code', [App\Http\Controllers\AffiliateController::class, 'applyReferralCode']);
        Route::get('/generate-link', [App\Http\Controllers\AffiliateController::class, 'generateReferralLink']);
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

// Push Notifications (accessible avec session web)
Route::middleware(['web', 'auth:web'])->prefix('notifications')->group(function () {
    Route::post('/subscribe', [App\Http\Controllers\Api\NotificationController::class, 'subscribe']);
    Route::post('/unsubscribe', [App\Http\Controllers\Api\NotificationController::class, 'unsubscribe']);
    Route::post('/closed', [App\Http\Controllers\Api\NotificationController::class, 'closed']);
    Route::match(['get', 'post'], '/test', [App\Http\Controllers\Api\NotificationController::class, 'test']);
    Route::post('/broadcast-test', [App\Http\Controllers\Api\NotificationController::class, 'broadcastTest']);
});

Route::post('/bot', [BotController::class, 'ask']);