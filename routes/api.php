<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\FirebaseAuthController;
use App\Http\Controllers\Api\Auth\TwoFactorAuthController;
use App\Http\Controllers\Api\Items\ItemController as ApiItemController;
use App\Http\Controllers\Api\Catalog\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\Catalog\BrandController as ApiBrandController;
use App\Http\Controllers\Api\Messages\MessageController as ApiMessageController;
use App\Http\Controllers\Api\Reviews\ReviewController as ApiReviewController;
use App\Http\Controllers\Api\Users\UserController as ApiUserController;
use App\Http\Controllers\Api\Notifications\NotificationController as ApiNotificationController;
use App\Http\Controllers\Api\Support\SupportController as ApiSupportController;
use App\Http\Controllers\Api\Authenticity\AuthenticityController as ApiAuthenticityController;
use App\Http\Controllers\Api\VintPass\VintPassController as ApiVintPassController;
use App\Http\Controllers\Api\Affiliate\AffiliateController as ApiAffiliateController;
use App\Http\Controllers\Api\Orders\OrderController as ApiOrderController;
use App\Http\Controllers\Api\Cart\CartController as ApiCartController;
use App\Http\Controllers\Api\DeliveryAddress\DeliveryAddressController as ApiDeliveryAddressController;
use App\Http\Controllers\Api\Payments\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\Wallet\WalletController as ApiWalletController;
use App\Http\Controllers\Api\Webhooks\PaymentCallbackController as ApiPaymentCallbackController;
use App\Http\Controllers\Api\Webhooks\PawaPayCallbackController as ApiPawaPayCallbackController;
use App\Http\Controllers\Api\System\SystemController;
use App\Http\Controllers\Api\Notifications\FcmController;
use App\Http\Controllers\Api\NotificationController as LegacyFcmNotificationController;
use App\Http\Controllers\Api\Location\SellerLocationController as ApiSellerLocationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\LocationValidationController;
use App\Http\Controllers\WelcomeController;

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
| Routes chargées par le RouteServiceProvider avec le middleware "api".
|
*/

// Endpoint léger pour valider la position depuis le client (fallback manuel)
Route::post('/validate-location', [LocationValidationController::class, 'validateLocation']);

// ==================== Routes publiques ====================
// NOTE: Ne pas utiliser compress.response avec cache.response pour éviter la corruption
Route::middleware(['cache.response:60'])->group(function () {
    Route::get('/health', [SystemController::class, 'health']);

    // API publique: Liste des articles (lecture seule)
    Route::get('/v1/items', [ApiItemController::class, 'index']);
    Route::get('/v1/items/{id}', [ApiItemController::class, 'show']);

    // API publique: Catégories et marques
    Route::get('/v1/categories', [ApiCategoryController::class, 'index']);
    Route::get('/v1/brands', [ApiBrandController::class, 'index']);

    // API publique: Vérification VintPass (accessible sans authentification)
    Route::get('/v1/vintpass/verify/{shortCode}', [ApiVintPassController::class, 'verify']);

    // API publique: Devises supportées
    Route::get('/v1/currencies', [SystemController::class, 'currencies']);
});

// API publique: Page d'accueil (sans middleware de cache pour compatibilité)
Route::get('/v1/home', [WelcomeController::class, 'apiIndex']);

// Validation de code de parrainage (public pour l'inscription)
Route::middleware(['throttle:10,1'])->post('/validate-referral-code', [ApiAffiliateController::class, 'validateReferralCode']);

// ==================== Callbacks de paiement (publics) ====================
Route::prefix('payment-callbacks')->group(function () {
    // Callback universel pour chaque opérateur
    Route::post('/{provider}', [ApiPaymentCallbackController::class, 'handleCallback'])
        ->name('payment.callback')
        ->middleware('throttle:100,1')
        ->where('provider', 'mpesa|orange_money|airtel_money|africell|illicocash|maishapay');

    // Endpoint pour vérifier le statut (polling)
    Route::get('/status', [ApiPaymentCallbackController::class, 'checkStatus'])
        ->middleware('throttle:30,1')
        ->name('payment.status');

    // Force complétion manuelle (quand le callback n'arrive pas)
    Route::post('/{transaction}/force-complete', [ApiPaymentCallbackController::class, 'forceComplete'])
        ->middleware('throttle:5,1')
        ->where('transaction', '[0-9]+')
        ->name('payment.force-complete');
});

// ==================== Callbacks PawaPay (publics, sans auth) ====================
// À configurer dans le dashboard PawaPay (un callback URL par opération) :
//  Deposits  : https://votre-domaine.com/api/v1/pawapay/callback/deposit
//  Checkouts : https://votre-domaine.com/api/v1/pawapay/callback/checkout
//  Payouts   : https://votre-domaine.com/api/v1/pawapay/callback/payout
//  Refunds   : https://votre-domaine.com/api/v1/pawapay/callback/refund
Route::prefix('v1/pawapay/callback')->middleware('throttle:100,1')->group(function () {
    Route::post('/deposit', [ApiPawaPayCallbackController::class, 'deposit'])->name('pawapay.callback.deposit');
    Route::post('/checkout', [ApiPawaPayCallbackController::class, 'checkout'])->name('pawapay.callback.checkout');
    Route::post('/payout', [ApiPawaPayCallbackController::class, 'payout'])->name('pawapay.callback.payout');
    Route::post('/refund', [ApiPawaPayCallbackController::class, 'refund'])->name('pawapay.callback.refund');
    // Point d'entrée générique paramétré : /api/v1/pawapay/callback/{type}
    Route::post('/{type}', [ApiPawaPayCallbackController::class, 'handleTyped'])
        ->where('type', 'deposit|checkout|payout|refund')
        ->name('pawapay.callback.typed');
});

// ==================== Authentification API (Sanctum) ====================
Route::middleware('throttle:5,1')->post('/register', [AuthController::class, 'register']);
Route::middleware('throttle:5,1')->post('/login', [AuthController::class, 'login']);
Route::middleware('throttle:5,1')->post('/auth/firebase/login', [FirebaseAuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'me']);

// ==================== Broadcasting Pusher (realtime mobile) ====================
Route::middleware('auth:sanctum')->post('/broadcasting/auth', [\App\Http\Controllers\Api\BroadcastAuthController::class, 'auth']);

// ==================== Authentification à deux facteurs (API) ====================
Route::middleware(['auth:sanctum', 'ability:2fa:pending', 'throttle:10,1'])->post('/two-factor/verify', [TwoFactorAuthController::class, 'verify']);
Route::middleware(['auth:sanctum', 'throttle:10,1'])->group(function () {
    Route::post('/two-factor/enable', [TwoFactorAuthController::class, 'enable']);
    Route::post('/two-factor/confirm', [TwoFactorAuthController::class, 'confirm']);
    Route::post('/two-factor/disable', [TwoFactorAuthController::class, 'disable']);
    Route::post('/two-factor/regenerate-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes']);
});

// ==================== Réinitialisation de mot de passe (public) ====================
Route::middleware('throttle:5,1')->post('/password/email', [AuthController::class, 'forgotPassword']);
Route::middleware('throttle:5,1')->post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::middleware('throttle:5,1')->post('/password/reset', [AuthController::class, 'resetPassword']);

// ==================== Routes protégées ====================
Route::middleware(['auth:sanctum,web'])->group(function () {

    // ---- API V1 : Items ----
    Route::middleware('throttle:60,1')->group(function () {
        Route::post('/v1/items', [ApiItemController::class, 'store']);
        Route::put('/v1/items/{id}', [ApiItemController::class, 'update']);
        Route::delete('/v1/items/{id}', [ApiItemController::class, 'destroy']);
    });

    // ---- API V1 : Categories ----
    Route::middleware('throttle:20,1')->group(function () {
        Route::post('/v1/categories', [ApiCategoryController::class, 'store']);
        Route::put('/v1/categories/{id}', [ApiCategoryController::class, 'update']);
        Route::delete('/v1/categories/{id}', [ApiCategoryController::class, 'destroy']);
    });
    Route::get('/v1/categories/{id}', [ApiCategoryController::class, 'show']);
    Route::get('/v1/categories/{id}/items', [ApiCategoryController::class, 'items']);

    // ---- API V1 : Brands ----
    Route::middleware('throttle:20,1')->group(function () {
        Route::post('/v1/brands', [ApiBrandController::class, 'store']);
        Route::put('/v1/brands/{id}', [ApiBrandController::class, 'update']);
        Route::delete('/v1/brands/{id}', [ApiBrandController::class, 'destroy']);
    });
    Route::get('/v1/brands/{id}', [ApiBrandController::class, 'show']);
    Route::get('/v1/brands/{id}/items', [ApiBrandController::class, 'items']);

    // ---- API V1 : Authenticity ----
    Route::prefix('v1/authenticity')->group(function () {
        Route::get('/dashboard', [ApiAuthenticityController::class, 'dashboard']);
        Route::post('/{check}/confirm-payment', [ApiAuthenticityController::class, 'confirmPayment']);
        Route::middleware('throttle:20,1')->group(function () {
            Route::put('/{check}/update-status', [ApiAuthenticityController::class, 'updateStatus']);
        });
    });
    Route::get('/v1/items/{item}/authenticity/can-verify', [ApiAuthenticityController::class, 'canVerify']);
    Route::get('/v1/items/{item}/authenticity/status', [ApiAuthenticityController::class, 'status']);
    Route::middleware('throttle:20,1')->group(function () {
        Route::post('/v1/items/{item}/authenticity/submit', [ApiAuthenticityController::class, 'submit']);
    });

    // ---- API V1 : User ----
    Route::prefix('v1/user')->group(function () {
        Route::get('/profile', [ApiUserController::class, 'profile']);
        Route::put('/profile', [ApiUserController::class, 'updateProfile']);
        Route::put('/password', [ApiUserController::class, 'updatePassword']);
        Route::post('/avatar', [ApiUserController::class, 'uploadAvatar']);
        Route::get('/stats', [ApiUserController::class, 'getStats']);
        Route::get('/items', [ApiUserController::class, 'getItems']);
        Route::get('/orders', [ApiUserController::class, 'getOrders']);
        Route::get('/sales', [ApiUserController::class, 'getSales']);
        Route::get('/reviews', [ApiUserController::class, 'getReviews']);
        Route::delete('/account', [ApiUserController::class, 'destroy']);
    });

    // ---- API V1 : Orders ----
    Route::prefix('v1/orders')->group(function () {
        Route::get('/', [ApiOrderController::class, 'index']);
        Route::post('/', [ApiOrderController::class, 'store']);
        Route::get('/sales', [ApiOrderController::class, 'mySales']);
        Route::get('/{id}', [ApiOrderController::class, 'show']);
        Route::post('/{id}/confirm-payment', [ApiOrderController::class, 'confirmPayment']);
        Route::post('/{id}/mark-shipped', [ApiOrderController::class, 'markAsShipped']);
        Route::post('/{id}/mark-delivered', [ApiOrderController::class, 'markAsDelivered']);
        Route::post('/{id}/confirm-delivery', [ApiOrderController::class, 'confirmDelivery']);
        Route::delete('/{id}', [ApiOrderController::class, 'destroy']);
    });

    // ---- API V1 : Cart ----
    Route::prefix('v1/cart')->group(function () {
        Route::get('/', [ApiCartController::class, 'index']);
        Route::get('/summary', [ApiCartController::class, 'summary']);
        Route::post('/', [ApiCartController::class, 'add']);
        Route::put('/{itemId}', [ApiCartController::class, 'update']);
        Route::delete('/', [ApiCartController::class, 'clear']);
        Route::delete('/{itemId}', [ApiCartController::class, 'remove']);
    });

    // ---- API V1 : Adresses de livraison ----
    Route::prefix('v1/delivery-addresses')->group(function () {
        Route::get('/', [ApiDeliveryAddressController::class, 'index']);
        Route::post('/', [ApiDeliveryAddressController::class, 'store']);
        Route::get('/{id}', [ApiDeliveryAddressController::class, 'show']);
        Route::put('/{id}', [ApiDeliveryAddressController::class, 'update']);
        Route::delete('/{id}', [ApiDeliveryAddressController::class, 'destroy']);
        Route::post('/{id}/default', [ApiDeliveryAddressController::class, 'setDefault']);
    });

    // ---- API V1 : Localisation vendeur (auth) ----
    Route::prefix('v1/seller-location')->group(function () {
        Route::get('/', [ApiSellerLocationController::class, 'index']);
        Route::put('/', [ApiSellerLocationController::class, 'update']);
        Route::delete('/', [ApiSellerLocationController::class, 'destroy']);
    });

    // ---- API V1 : Messages ----
    Route::prefix('v1/messages')->group(function () {
        Route::get('/', [ApiMessageController::class, 'index']);
        Route::post('/', [ApiMessageController::class, 'store']);
        Route::get('/{userId}', [ApiMessageController::class, 'show']);
        Route::put('/{messageId}/mark-read', [ApiMessageController::class, 'markAsRead']);
        Route::get('/unread/count', [ApiMessageController::class, 'unreadCount']);
        Route::post('/discount/apply', [ApiMessageController::class, 'applyDiscount']);
        Route::get('/discounts/{itemId}', [ApiMessageController::class, 'getAvailableDiscounts']);
    });

    // ---- API V1 : Reviews ----
    Route::prefix('v1/reviews')->group(function () {
        Route::get('/', [ApiReviewController::class, 'index']);
        Route::get('/item/{itemId}', [ApiReviewController::class, 'itemReviews']);
        Route::get('/seller/{sellerId}', [ApiReviewController::class, 'sellerReviews']);
        Route::post('/', [ApiReviewController::class, 'store']);
        Route::put('/{reviewId}', [ApiReviewController::class, 'update']);
        Route::delete('/{reviewId}', [ApiReviewController::class, 'destroy']);
    });

    // ---- API V1 : Wallet ----
    Route::prefix('v1/wallet')->group(function () {
        Route::get('/', [ApiWalletController::class, 'index']);
        Route::get('/transactions', [ApiWalletController::class, 'transactions']);
        Route::post('/add-funds', [ApiWalletController::class, 'addFunds']);
        Route::post('/withdraw', [ApiWalletController::class, 'withdraw']);
        Route::post('/convert', [ApiWalletController::class, 'convert']);

        // MaishaPay Payout routes
        Route::post('/withdraw/maishapay', [ApiWalletController::class, 'withdrawMaishaPay']);
        Route::get('/withdraw/maishapay/status/{transactionId}', [ApiWalletController::class, 'withdrawMaishaPayStatus']);
        Route::get('/withdraw/operators', [ApiWalletController::class, 'getPayoutOperators']);
    });

    // ---- API V1 : VintPass ----
    Route::prefix('v1/vintpass')->group(function () {
        Route::get('/', [ApiVintPassController::class, 'myPasses']);
        Route::get('/{vintPass}', [ApiVintPassController::class, 'show']);
        Route::post('/request/{item}', [ApiVintPassController::class, 'requestPass']);
    });

    // ==================== Routes API legacy (JSON) ====================

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
        // /search AVANT /{item} pour être atteignable
        Route::middleware(['throttle:100,1', 'cache.response:120'])->get('/search', [ApiItemController::class, 'search']);
        Route::middleware('throttle:30,1')->post('/{item}/favorite', [ApiItemController::class, 'toggleFavorite']);
    });

    // Items V1 routes (mobile app)
    Route::prefix('v1/items')->group(function () {
        Route::middleware('throttle:30,1')->post('/{item}/favorite', [ApiItemController::class, 'toggleFavorite']);
        Route::middleware('throttle:100,1', 'cache.response:120')->get('/search', [ApiItemController::class, 'search']);
        Route::middleware('throttle:200,1')->post('/{item}/views', [ApiItemController::class, 'incrementViews']);
    });

    // Dashboard routes (rate limit: 30/min)
    Route::middleware('throttle:30,1')->prefix('dashboard')->group(function () {
        Route::get('/data', [DashboardController::class, 'apiData']);
    });

    // Affiliate routes (rate limit: 30/min)
    Route::middleware('throttle:30,1')->prefix('affiliate')->group(function () {
        Route::get('/dashboard', [ApiAffiliateController::class, 'dashboard']);
        Route::get('/referral-codes', [ApiAffiliateController::class, 'getReferralCodes']);
        Route::post('/referral-codes', [ApiAffiliateController::class, 'createReferralCode']);
        Route::get('/codes/stats', [ApiAffiliateController::class, 'getCodesStats']);
        Route::get('/referrals', [ApiAffiliateController::class, 'getReferrals']);
        Route::get('/points-history', [ApiAffiliateController::class, 'getPointsHistory']);
        Route::post('/convert-points', [ApiAffiliateController::class, 'convertPointsToCash']);
        Route::post('/calculate-conversion', [ApiAffiliateController::class, 'calculateConversion']);
        Route::get('/redemptions', [ApiAffiliateController::class, 'getRedemptions']);
        Route::post('/apply-referral-code', [ApiAffiliateController::class, 'applyReferralCode']);
        Route::get('/generate-link', [ApiAffiliateController::class, 'generateReferralLink']);
    });
});

// ==================== Notifications FCM (session web) ====================
Route::middleware(['web', 'auth:web'])->prefix('notifications')->group(function () {
    Route::post('/subscribe', [LegacyFcmNotificationController::class, 'subscribe']);
    Route::post('/unsubscribe', [LegacyFcmNotificationController::class, 'unsubscribe']);
    Route::post('/closed', [LegacyFcmNotificationController::class, 'closed']);
    Route::match(['get', 'post'], '/test', [LegacyFcmNotificationController::class, 'test']);
    Route::post('/broadcast-test', [LegacyFcmNotificationController::class, 'broadcastTest']);
});

// ==================== FCM Token & Tests ====================
Route::middleware(['web'])->post('/fcm-token', [FcmController::class, 'registerToken']);

Route::middleware(['web', 'auth:web'])->group(function () {
    Route::post('/test-fcm-notification', [FcmController::class, 'testNotification']);
    Route::post('/admin/broadcast-fcm-test', [FcmController::class, 'adminBroadcast']);
    Route::get('/admin/fcm-stats', [FcmController::class, 'adminStats']);
});

// ⚠️ Route de validation GPS déplacée vers routes/web.php (ligne ~207)

Route::post('/bot', [BotController::class, 'ask']);

// ==================== Notification Routes (API v1) ====================
Route::prefix('v1/notifications')->middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/', [ApiNotificationController::class, 'index']);
    Route::get('/unread', [ApiNotificationController::class, 'unread']);
    Route::get('/unread/count', [ApiNotificationController::class, 'unreadCount']);
    Route::post('/mark-all-read', [ApiNotificationController::class, 'markAllAsRead']);
    Route::post('/{id}/mark-read', [ApiNotificationController::class, 'markAsRead']);
    Route::delete('/{id}', [ApiNotificationController::class, 'destroy']);
    Route::delete('/read/all', [ApiNotificationController::class, 'deleteRead']);
});

// ==================== Support Routes ====================
Route::prefix('v1/support')->middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/', [ApiSupportController::class, 'index']);
    Route::post('/', [ApiSupportController::class, 'store']);
    Route::get('/stats', [ApiSupportController::class, 'stats']);
    Route::get('/{id}', [ApiSupportController::class, 'show']);
    Route::post('/{id}/reply', [ApiSupportController::class, 'reply']);
    Route::post('/{id}/close', [ApiSupportController::class, 'close']);
});

// ==================== Payment Routes ====================
Route::prefix('v1/payments')->middleware(['auth:sanctum,web'])->group(function () {
    Route::get('/', [ApiPaymentController::class, 'index']);
    Route::get('/stats', [ApiPaymentController::class, 'stats']);
    Route::get('/{transactionId}', [ApiPaymentController::class, 'show']);
    Route::post('/initiate', [ApiPaymentController::class, 'initiate']);
    Route::post('/refund/{orderId}', [ApiPaymentController::class, 'requestRefund']);
    Route::get('/refund/{refundId}/status', [ApiPaymentController::class, 'refundStatus']);

    // MaishaPay routes
    Route::post('/maishapay', [ApiPaymentController::class, 'initiateMaishaPayment'])
        ->name('api.v1.payments.maishapay.initiate');
    Route::get('/maishapay/status/{transactionId}', [ApiPaymentController::class, 'checkMaishaStatus'])
        ->name('api.v1.payments.maishapay.status');
});

// ---- API V1 : Localisation vendeur (public) ----
Route::get('/v1/seller-location/{userId}', [ApiSellerLocationController::class, 'show']);
Route::get('/v1/sellers/nearby', [ApiSellerLocationController::class, 'nearby']);

// MaishaPay payout webhook (public - no auth)
Route::post('v1/wallet/withdrawals/maishapay/callback', [WalletController::class, 'handleWithdrawalWebhook'])
    ->defaults('provider', 'maishapay')
    ->withoutMiddleware(['auth:sanctum', 'web']);

// ==================== Admin Routes ====================
Route::prefix('v1/admin')->middleware(['auth:sanctum,web', 'admin'])->group(function () {
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
