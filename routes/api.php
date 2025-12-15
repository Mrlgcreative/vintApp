<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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
use App\Http\Controllers\AuthenticityController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SupportController;

// Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\Admin\RefundController as AdminRefundController;
use App\Http\Controllers\Admin\WaitingUsersController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Admin\MonitoringController;

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

use App\Http\Controllers\LocationValidationController;

// Endpoint léger pour valider la position depuis le client (fallback manuel)
Route::post('/validate-location', [LocationValidationController::class, 'validateLocation']);

// Routes publiques
// NOTE: Ne pas utiliser compress.response avec cache.response pour éviter la corruption
// La compression peut être gérée au niveau du serveur web (nginx/apache)
Route::middleware(['cache.response:60'])->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'VintApp API is running',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String()
        ]);
    });
    
    // API publique: Liste des articles (lecture seule)
    Route::get('/v1/items', [ItemController::class, 'apiIndex']);
    Route::get('/v1/items/{id}', [ItemController::class, 'apiShow']);
    
    // API publique: Catégories et marques
    Route::get('/v1/categories', [CategoryController::class, 'apiIndex']);
    Route::get('/v1/brands', [BrandController::class, 'apiIndex']);
});

// API publique: Page d'accueil (sans middleware de cache pour compatibilité)
Route::get('/v1/home', [\App\Http\Controllers\WelcomeController::class, 'apiIndex']);

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

// Routes d'authentification API (Sanctum)
Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = \App\Models\User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Inscription réussie',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ],
        'token' => $token,
        'token_type' => 'Bearer',
    ], 201);
});

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (!$user || !\Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
        return response()->json([
            'success' => false,
            'message' => 'Les informations de connexion fournies sont incorrectes.',
        ], 401);
    }

    // Supprimer les anciens tokens si souhaité (optionnel)
    // $user->tokens()->delete();

    $token = $user->createToken('auth_token')->plainTextToken;

    Log::info('API Login réussi', ['user_id' => $user->id, 'email' => $user->email]);

    return response()->json([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at,
            'role' => $user->role ?? 'user',
        ],
        'token' => $token,
        'token_type' => 'Bearer',
    ]);
});

// Route pour déconnexion API
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'Déconnexion réussie'
    ]);
});

// Route pour récupérer l'utilisateur authentifié
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    $user = $request->user();
    return response()->json([
        'success' => true,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'email_verified_at' => $user->email_verified_at,
            'role' => $user->role ?? 'user',
        ]
    ]);
});

// Routes protégées par authentification
Route::middleware(['auth:sanctum,web', 'compress.response'])->group(function () {
    
    // ==================== API V1 Routes ====================
    Route::prefix('v1')->group(function () {
        
        // Items API
        Route::middleware('throttle:60,1')->group(function () {
            Route::post('/items', [ItemController::class, 'apiStore']);
            Route::put('/items/{id}', [ItemController::class, 'apiUpdate']);
            Route::delete('/items/{id}', [ItemController::class, 'apiDestroy']);
        });
        
        // Categories API
        Route::middleware('throttle:20,1')->group(function () {
            Route::post('/categories', [CategoryController::class, 'apiStore']);
            Route::put('/categories/{id}', [CategoryController::class, 'apiUpdate']);
            Route::delete('/categories/{id}', [CategoryController::class, 'apiDestroy']);
        });
        Route::get('/categories/{id}', [CategoryController::class, 'apiShow']);
        Route::get('/categories/{id}/items', [CategoryController::class, 'apiItems']);
        
        // Brands API
        Route::middleware('throttle:20,1')->group(function () {
            Route::post('/brands', [BrandController::class, 'apiStore']);
            Route::put('/brands/{id}', [BrandController::class, 'apiUpdate']);
            Route::delete('/brands/{id}', [BrandController::class, 'apiDestroy']);
        });
        Route::get('/brands/{id}', [BrandController::class, 'apiShow']);
        Route::get('/brands/{id}/items', [BrandController::class, 'apiItems']);
        
        // Authenticity Verification API
        Route::prefix('authenticity')->group(function () {
            Route::get('/dashboard', [AuthenticityController::class, 'apiDashboard']);
            Route::post('/{check}/confirm-payment', [AuthenticityController::class, 'apiConfirmPayment']);
            Route::middleware('throttle:20,1')->group(function () {
                Route::put('/{check}/update-status', [AuthenticityController::class, 'apiUpdateStatus']);
            });
        });
        Route::get('/items/{item}/authenticity/can-verify', [AuthenticityController::class, 'apiCanVerify']);
        Route::get('/items/{item}/authenticity/status', [AuthenticityController::class, 'apiStatus']);
        Route::middleware('throttle:20,1')->group(function () {
            Route::post('/items/{item}/authenticity/submit', [AuthenticityController::class, 'apiSubmit']);
        });
        
        // User API
        Route::prefix('user')->group(function () {
            Route::get('/profile', [UserController::class, 'apiProfile']);
            Route::put('/profile', [UserController::class, 'apiUpdateProfile']);
            Route::put('/password', [UserController::class, 'apiUpdatePassword']);
            Route::post('/avatar', [UserController::class, 'apiUploadAvatar']);
            Route::get('/stats', [UserController::class, 'apiGetStats']);
            Route::get('/items', [UserController::class, 'apiGetItems']);
            Route::get('/orders', [UserController::class, 'apiGetOrders']);
            Route::get('/sales', [UserController::class, 'apiGetSales']);
            Route::get('/reviews', [UserController::class, 'apiGetReviews']);
            Route::delete('/account', [UserController::class, 'apiDestroy']);
        });
        
        // Orders API
        Route::prefix('orders')->group(function () {
            Route::get('/', [OrderController::class, 'apiIndex']);
            Route::post('/', [OrderController::class, 'apiStore']);
            Route::get('/sales', [OrderController::class, 'apiMySales']);
            Route::get('/{id}', [OrderController::class, 'apiShow']);
            Route::post('/{id}/confirm-payment', [OrderController::class, 'apiConfirmPayment']);
            Route::post('/{id}/mark-shipped', [OrderController::class, 'apiMarkAsShipped']);
            Route::post('/{id}/mark-delivered', [OrderController::class, 'apiMarkAsDelivered']);
            Route::post('/{id}/confirm-delivery', [OrderController::class, 'apiConfirmDelivery']);
        });
        
        // Messages API
        Route::prefix('messages')->group(function () {
            Route::get('/', [MessageController::class, 'apiIndex']);
            Route::post('/', [MessageController::class, 'apiStore']);
            Route::get('/{userId}', [MessageController::class, 'apiShow']);
            Route::put('/{messageId}/mark-read', [MessageController::class, 'apiMarkAsRead']);
            Route::get('/unread/count', [MessageController::class, 'apiUnreadCount']);
            Route::post('/discount/apply', [MessageController::class, 'apiApplyDiscount']);
            Route::get('/discounts/{itemId}', [MessageController::class, 'apiGetAvailableDiscounts']);
        });
        
        // Reviews API
        Route::prefix('reviews')->group(function () {
            Route::get('/', [ReviewController::class, 'apiIndex']);
            Route::get('/item/{itemId}', [ReviewController::class, 'apiItemReviews']);
            Route::get('/seller/{sellerId}', [ReviewController::class, 'apiSellerReviews']);
            Route::post('/', [ReviewController::class, 'apiStore']);
            Route::put('/{reviewId}', [ReviewController::class, 'apiUpdate']);
            Route::delete('/{reviewId}', [ReviewController::class, 'apiDestroy']);
        });

        // Wallet API
        Route::prefix('wallet')->group(function () {
            Route::get('/', [WalletController::class, 'apiShow']);
            Route::get('/transactions', [WalletController::class, 'apiTransactions']);
            Route::post('/add-funds', [WalletController::class, 'apiAddFunds']);
            Route::post('/withdraw', [WalletController::class, 'apiWithdraw']);
            Route::post('/convert', [WalletController::class, 'apiConvert']);
        });
    });
    
    // ==================== Legacy API Routes ====================
    
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
        Route::get('/codes/stats', [App\Http\Controllers\AffiliateController::class, 'getCodesStats']);
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

// FCM Token Registration
Route::middleware(['web'])->post('/fcm-token', function (Request $request) {
    try {
        $validated = $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string|max:20'
        ]);

        // Vérifier si l'utilisateur est authentifié ou en challenge 2FA
        $user = Auth::user();
        
        // Si pas d'utilisateur Auth, vérifier si en challenge 2FA
        if (!$user && session('2fa_user_id')) {
            $user = \App\Models\User::find(session('2fa_user_id'));
        }
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }

        $user->fcm_token = $validated['token'];
        $user->device_type = $validated['device_type'] ?? 'unknown';
        $user->browser = $request->userAgent();
        $user->fcm_token_updated_at = now();
        $user->save();

        Log::info('Token FCM enregistré', [
            'user_id' => $user->id,
            'device_type' => $user->device_type
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Token FCM enregistré avec succès'
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur enregistrement token FCM', [
            'error' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'enregistrement du token'
        ], 500);
    }
});

// Test FCM Notification
Route::middleware(['web', 'auth:web'])->post('/test-fcm-notification', function (Request $request) {
    try {
        $user = Auth::user();
        
        if (!$user->fcm_token) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun token FCM enregistré. Autorisez d\'abord les notifications.'
            ], 400);
        }

        $type = $request->input('type', 'approved');
        $fcmService = app(\App\Services\FirebasePushService::class);

        if ($type === 'approved') {
            $result = $fcmService->sendItemApprovedNotification($user->fcm_token, [
                'item_id' => 999,
                'item_name' => 'Article de Test',
                'item_image' => 'items/test-image.jpg',
                'verification_score' => 95
            ]);
        } else {
            $result = $fcmService->sendItemRejectedNotification($user->fcm_token, [
                'item_id' => 999,
                'item_name' => 'Article de Test',
                'item_image' => 'items/test-image.jpg',
                'reason' => 'Ceci est un test de notification de rejet'
            ]);
        }

        if ($result) {
            Log::info('Notification FCM test envoyée', [
                'user_id' => $user->id,
                'type' => $type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification envoyée avec succès ! Vérifiez votre téléphone.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Échec de l\'envoi de la notification. Vérifiez les logs.'
            ], 500);
        }

    } catch (\Exception $e) {
        Log::error('Erreur test notification FCM', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
});

// Admin Broadcast FCM Test
Route::middleware(['web', 'auth:web'])->post('/admin/broadcast-fcm-test', function (Request $request) {
    try {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin via role_user
        $isAdmin = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
        
        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Administrateur requis.'
            ], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500'
        ]);

        // Récupérer tous les utilisateurs avec un token FCM
        $usersWithTokens = \App\Models\User::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->get();

        if ($usersWithTokens->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun appareil avec notifications activées trouvé.'
            ]);
        }

        $fcmService = app(\App\Services\FirebasePushService::class);
        $tokens = $usersWithTokens->pluck('fcm_token')->toArray();

        // Envoyer notification multicast
        $result = $fcmService->sendMulticast(
            $tokens,
            $validated['title'],
            $validated['message'],
            [
                'type' => 'admin_broadcast',
                'timestamp' => now()->toIso8601String()
            ],
            null // Pas d'image pour le test
        );

        Log::info('Broadcast FCM admin envoyé', [
            'admin_id' => $user->id,
            'total_devices' => count($tokens),
            'success' => $result['success'],
            'failure' => $result['failure']
        ]);

        return response()->json([
            'success' => true,
            'message' => "Notification envoyée à {$result['success']} appareil(s) sur " . count($tokens),
            'stats' => [
                'total' => count($tokens),
                'success' => $result['success'],
                'failure' => $result['failure'],
                'failed_tokens' => $result['failed_tokens']
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur broadcast FCM admin', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
});

// ⚠️ Route de validation GPS déplacée vers routes/web.php (ligne ~207)
// Raison: Les routes API n'ont pas accès aux sessions web

// Admin FCM Stats
Route::middleware(['web', 'auth:web'])->get('/admin/fcm-stats', function (Request $request) {
    try {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est admin via role_user
        $isAdmin = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
        
        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé'
            ], 403);
        }

        $totalUsers = \App\Models\User::count();
        $devicesWithFCM = \App\Models\User::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->count();

        return response()->json([
            'success' => true,
            'stats' => [
                'total_users' => $totalUsers,
                'devices_with_fcm' => $devicesWithFCM
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur: ' . $e->getMessage()
        ], 500);
    }
});

Route::post('/bot', [BotController::class, 'ask']);

// ==================== Notification Routes ====================
Route::prefix('v1/notifications')->middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/', [NotificationController::class, 'apiIndex']);
    Route::get('/unread', [NotificationController::class, 'apiUnread']);
    Route::get('/unread/count', [NotificationController::class, 'apiUnreadCount']);
    Route::post('/mark-all-read', [NotificationController::class, 'apiMarkAllAsRead']);
    Route::post('/{id}/mark-read', [NotificationController::class, 'apiMarkAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'apiDestroy']);
    Route::delete('/read/all', [NotificationController::class, 'apiDeleteRead']);
});

// ==================== Support Routes ====================
Route::prefix('v1/support')->middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/', [SupportController::class, 'apiIndex']);
    Route::post('/', [SupportController::class, 'apiStore']);
    Route::get('/stats', [SupportController::class, 'apiStats']);
    Route::get('/{id}', [SupportController::class, 'apiShow']);
    Route::post('/{id}/reply', [SupportController::class, 'apiReply']);
    Route::post('/{id}/close', [SupportController::class, 'apiClose']);
});

// ==================== Payment Routes ====================
Route::prefix('v1/payments')->middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/', [PaymentController::class, 'apiIndex']);
    Route::get('/stats', [PaymentController::class, 'apiStats']);
    Route::get('/{transactionId}', [PaymentController::class, 'apiShow']);
    Route::post('/initiate', [PaymentController::class, 'apiInitiate']);
    Route::post('/refund/{orderId}', [PaymentController::class, 'apiRequestRefund']);
    Route::get('/refund/{refundId}/status', [PaymentController::class, 'apiRefundStatus']);
});

// ==================== Admin Routes ====================
Route::prefix('v1/admin')->middleware(['auth:sanctum,web', 'role:admin'])->group(function () {
    // Dashboard & Stats
    Route::get('/dashboard', [AdminController::class, 'apiDashboard']);
    Route::get('/stats/summary', [AdminController::class, 'apiStatsSummary']);
    Route::get('/notifications', [AdminController::class, 'apiNotifications']);
    Route::get('/reports', [AdminController::class, 'apiReports']);
    Route::get('/online-users', [AdminController::class, 'apiOnlineUsers']);
    
    // Users Management
    Route::get('/users', [AdminController::class, 'apiUsers']);
    Route::get('/users/{userId}', [AdminController::class, 'apiUserShow']);
    Route::post('/users/{userId}/status', [AdminController::class, 'apiUserUpdateStatus']);
    
    // Wallets Management
    Route::get('/wallets', [AdminController::class, 'apiWallets']);
    Route::get('/wallets/pending', [AdminController::class, 'apiPendingWallets']);
    Route::post('/wallets/{walletId}/approve', [AdminController::class, 'apiApproveWallet']);
    Route::post('/wallets/{walletId}/reject', [AdminController::class, 'apiRejectWallet']);
    Route::post('/wallets/bulk-approve', [AdminController::class, 'apiBulkApproveWallets']);
    
    // Transactions
    Route::get('/transactions', [AdminController::class, 'apiTransactions']);
    
    // Orders
    Route::get('/orders', [AdminController::class, 'apiOrders']);
    
    // Items Management
    Route::get('/items', [AdminController::class, 'apiItems']);
    Route::post('/items/{itemId}/status', [AdminController::class, 'apiItemUpdateStatus']);
    
    // Brands & Categories
    Route::get('/brands', [AdminController::class, 'apiBrands']);
    Route::get('/categories', [AdminController::class, 'apiCategories']);
    
    // Support Management
    Route::get('/support-chats', [AdminController::class, 'apiSupportChats']);
    
    // Verification Management
    Route::get('/verification-checks', [AdminController::class, 'apiVerificationChecks']);
    
    // Settings Management
    Route::get('/settings', [AdminController::class, 'apiSettings']);
    Route::put('/settings/{key}', [AdminController::class, 'apiUpdateSetting']);
    
    // Enterprise Wallets
    Route::get('/enterprise-wallets', [AdminWalletController::class, 'apiIndex']);
    Route::get('/enterprise-wallets/{wallet}', [AdminWalletController::class, 'apiShow']);
    
    // Support Admin
    Route::get('/support', [AdminSupportController::class, 'apiIndex']);
    Route::get('/support/stats', [AdminSupportController::class, 'apiStats']);
    Route::get('/support/{supportChat}', [AdminSupportController::class, 'apiShow']);
    
    // Affiliate Management
    Route::get('/affiliate/stats', [AffiliateController::class, 'getDashboardStats']);
    Route::get('/affiliate/top-performers', [AffiliateController::class, 'getTopPerformers']);
    Route::get('/affiliate/referrers', [AffiliateController::class, 'getReferrers']);
    Route::get('/affiliate/activity', [AffiliateController::class, 'getRecentActivity']);
    
    // Refunds Management
    Route::get('/refunds', [AdminRefundController::class, 'apiIndex']);
    Route::get('/refunds/{refund}', [AdminRefundController::class, 'apiShow']);
    
    // Waiting Users
    Route::get('/waiting-users', [WaitingUsersController::class, 'apiIndex']);
    Route::get('/waiting-users/stats', [WaitingUsersController::class, 'apiStats']);
    Route::post('/waiting-users/{waitingUser}/approve', [WaitingUsersController::class, 'apiApprove']);
    
    // Monitoring
    Route::get('/monitoring/stats', [MonitoringController::class, 'stats']);
    Route::get('/monitoring/health', [MonitoringController::class, 'health']);
});