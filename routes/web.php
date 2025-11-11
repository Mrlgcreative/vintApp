<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\LocalDeliveryController;
use App\Http\Controllers\AuthenticityController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PendingWalletController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\AffiliateController;

// Page de démarrage
Route::get('/splash', function() {
    return view('splash');
})->name('splash');

// Page d'accueil avec WelcomeController
Route::get('/home', [WelcomeController::class, 'index'])->name('home');

// Page d'accueil principale - gestion des redirections par rôle
Route::get('/', function() {
    if (Auth::check()) {
        $user = Auth::user();
        
        // Vérification simple via la table role_user pour les admins
        $isAdmin = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
            
        if ($isAdmin) {
            return redirect()->route('admin.dashboard');
        }

        // Vérification pour les experts
        if ($user->isExpert()) {
            return redirect()->route('expert.dashboard');
        }
        
        // Utilisateurs connectés non-admin non-expert → page d'accueil
        return app(WelcomeController::class)->index();
    }
    
    // Utilisateurs non connectés → page splash
    return view('splash');
});

// Routes Newsletter publiques
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/newsletter/preferences/{token}', [NewsletterController::class, 'preferences'])->name('newsletter.preferences');
Route::put('/newsletter/preferences/{token}', [NewsletterController::class, 'updatePreferences'])->name('newsletter.preferences.update');
Route::get('/newsletter/verify/{token}', [NewsletterController::class, 'verify'])->name('newsletter.verify');
Route::get('/newsletter/track/open/{token}', [NewsletterController::class, 'trackOpen'])->name('newsletter.track.open');
Route::get('/newsletter/track/click/{token}', [NewsletterController::class, 'trackClick'])->name('newsletter.track.click');
Route::get('/newsletter/track/click/{token}', [NewsletterController::class, 'trackClick'])->name('newsletter.track.click');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');

// Routes de test supprimées
Route::get('/test-create', function() {
    return 'Route de test accessible';
});

// Page de test pour les notifications temps réel
Route::get('/test-notifications', function() {
    return view('test-notifications');
})->middleware('auth')->name('test.notifications');

// Routes publiques pour les items
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

// Routes publiques pour le scan QR code des commandes
Route::get('/order/scan/{token}', [OrderController::class, 'scanOrder'])->name('orders.scan');
Route::post('/order/scan/{token}/confirm', [OrderController::class, 'confirmOrderDelivery'])->name('orders.scan.confirm');

Route::middleware('auth')->group(function () {
    // Routes pour les items (CRUD) - Routes spécifiques AVANT les routes avec paramètres
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/my-items', [ItemController::class, 'myItems'])->name('items.my-items');
    Route::get('/items/personalization', [ItemController::class, 'personalization'])->name('items.personalization');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::patch('/items/{item}/status', [ItemController::class, 'updateStatus'])->name('items.update-status');
    Route::patch('/items/{item}/personalization', [ItemController::class, 'updatePersonalization'])->name('items.update-personalization');
    
    // Routes du profil
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/profile/stats', [ProfileController::class, 'stats'])->name('profile.stats');
    Route::get('/profile/security', [ProfileController::class, 'security'])->name('profile.security');
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    
    // Settings page
    Route::get('/settings', function() {
        return view('settings.index');
    })->name('settings.index');
    
    // Dashboard routes
    Route::get('/dashboard/analytics', [App\Http\Controllers\DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/notifications', [App\Http\Controllers\DashboardController::class, 'notifications'])->name('dashboard.notifications');
    Route::patch('/dashboard/notifications/{id}/read', [App\Http\Controllers\DashboardController::class, 'markNotificationAsRead'])->name('dashboard.notifications.read');
    Route::patch('/dashboard/notifications/read-all', [App\Http\Controllers\DashboardController::class, 'markAllNotificationsAsRead'])->name('dashboard.notifications.read-all');
    
    // Routes accessibles (lecture)
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    
    // Thème
    Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
    Route::post('/theme/set', [ThemeController::class, 'set'])->name('theme.set');
});


// Routes pour les commandes
// Routes des commandes
Route::middleware(['auth'])->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/my-sales', [OrderController::class, 'mySales'])->name('orders.my-sales');
    Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirm-payment');
    Route::post('/orders/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('orders.confirm-delivery');
    Route::post('/orders/{order}/mark-shipped', [OrderController::class, 'markAsShipped'])->name('orders.mark-shipped');
    Route::post('/orders/{order}/mark-delivered', [OrderController::class, 'markAsDelivered'])->name('orders.mark-delivered');
});

// Messagerie (type WhatsApp)
Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [App\Http\Controllers\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [App\Http\Controllers\MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{user}', [App\Http\Controllers\MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{message}/edit', [App\Http\Controllers\MessageController::class, 'edit'])->name('messages.edit');
    Route::put('/messages/{message}', [App\Http\Controllers\MessageController::class, 'update'])->name('messages.update');
    Route::delete('/messages/{message}', [App\Http\Controllers\MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::get('/messages-unread-count', [App\Http\Controllers\MessageController::class, 'unreadCount'])->name('messages.unread-count');
    
    // Routes pour les notifications en temps réel
    Route::get('/notifications', [App\Http\Controllers\MessageController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\MessageController::class, 'markNotificationAsRead'])->name('notifications.read');
    
    // Routes pour les réductions
    Route::post('/discounts/apply', [App\Http\Controllers\MessageController::class, 'applyDiscount'])->name('discounts.apply');
    Route::get('/discounts/rates', [App\Http\Controllers\MessageController::class, 'getPredefinedDiscountRates'])->name('discounts.rates');
    Route::get('/discounts/requests', [App\Http\Controllers\MessageController::class, 'getDiscountRequests'])->name('discounts.requests');
});

// Routes pour les catégories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create')->middleware('auth');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('auth');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('auth');
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('auth');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('auth');

// Routes pour les marques
Route::resource('brands', App\Http\Controllers\BrandController::class);
Route::resource('reviews', App\Http\Controllers\ReviewController::class);

// Route pour la notation post-paiement
Route::post('/reviews/post-payment', [App\Http\Controllers\ReviewController::class, 'storePostPayment'])
    ->name('reviews.post-payment')
    ->middleware('auth');

// Routes d'administration
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'throttle:60,1'])->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    
    // Gestion des utilisateurs (CRUD complet)
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'users'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminController::class, 'userCreate'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'userStore'])->name('store');
        
        // 🆕 Routes pour les utilisateurs connectés en temps réel (AVANT les routes avec {user})
        Route::get('/online', [App\Http\Controllers\Admin\AdminController::class, 'onlineUsers'])->name('online');
        Route::get('/online/data', [App\Http\Controllers\Admin\AdminController::class, 'getOnlineUsersData'])->name('online.data');
        Route::post('/sessions/{session}/logout', [App\Http\Controllers\Admin\AdminController::class, 'forceLogoutUser'])->name('sessions.logout');
        
        // Routes CRUD avec paramètre {user} (APRÈS les routes spécifiques)
        Route::get('/{user}', [App\Http\Controllers\Admin\AdminController::class, 'userShow'])->name('show');
        Route::get('/{user}/edit', [App\Http\Controllers\Admin\AdminController::class, 'userEdit'])->name('edit');
        Route::put('/{user}', [App\Http\Controllers\Admin\AdminController::class, 'userUpdate'])->name('update');
        Route::delete('/{user}', [App\Http\Controllers\Admin\AdminController::class, 'userDestroy'])->name('destroy');
        Route::patch('/{user}/status', [App\Http\Controllers\Admin\AdminController::class, 'userUpdateStatus'])->name('update-status');
        
        // Routes d'actions spécifiques pour les utilisateurs
        Route::post('/{user}/toggle-status', [App\Http\Controllers\Admin\AdminController::class, 'userToggleStatus'])->name('toggle-status');
        Route::post('/{user}/send-password-reset', [App\Http\Controllers\Admin\AdminController::class, 'userSendPasswordReset'])->name('send-password-reset');
        Route::post('/{user}/send-welcome', [App\Http\Controllers\Admin\AdminController::class, 'userSendWelcome'])->name('send-welcome');
        Route::post('/{user}/send-message', [App\Http\Controllers\Admin\AdminController::class, 'userSendMessage'])->name('send-message');
        Route::get('/{user}/export', [App\Http\Controllers\Admin\AdminController::class, 'userExport'])->name('export');
    });

    // Gestion des wallets
    Route::prefix('wallets')->name('wallets.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WalletController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Admin\WalletController::class, 'store'])->name('store');
        Route::get('/{wallet}', [App\Http\Controllers\Admin\WalletController::class, 'show'])->name('show');
        Route::get('/{wallet}/transactions', [App\Http\Controllers\Admin\WalletController::class, 'transactions'])->name('transactions');
        Route::post('/{wallet}/withdraw', [App\Http\Controllers\Admin\WalletController::class, 'withdraw'])->name('withdraw');
        Route::post('/add-commission', [App\Http\Controllers\Admin\WalletController::class, 'addCommission'])->name('add-commission');
        
        // Routes pour les wallets utilisateurs (ancienne gestion)
        Route::get('/pending', [App\Http\Controllers\Admin\AdminController::class, 'pendingWallets'])->name('pending');
        Route::post('/{wallet}/approve', [App\Http\Controllers\Admin\AdminController::class, 'approveWallet'])->name('approve');
        Route::post('/{wallet}/reject', [App\Http\Controllers\Admin\AdminController::class, 'rejectWallet'])->name('reject');
        Route::post('/bulk-approve', [App\Http\Controllers\Admin\AdminController::class, 'bulkApproveWallets'])->name('bulk-approve');
        Route::post('/bulk-reject', [App\Http\Controllers\Admin\AdminController::class, 'bulkRejectWallets'])->name('bulk-reject');
        
        // 🆕 Transfert de commission vers WalletEntreprise
        Route::post('/transfer-commission', [App\Http\Controllers\WalletController::class, 'transferCommission'])
            ->name('transfer-commission');
    });

    // Gestion des transactions
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'transactions'])->name('index');
        Route::get('/{transaction}', [App\Http\Controllers\Admin\AdminController::class, 'transactionShow'])->name('show');
    });

    // Gestion des commandes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'orders'])->name('index');
        Route::get('/{order}', [App\Http\Controllers\Admin\AdminController::class, 'orderShow'])->name('show');
        Route::patch('/{order}/status', [App\Http\Controllers\Admin\AdminController::class, 'orderUpdateStatus'])->name('update-status');
        
        // 🆕 Liste des commandes avec traçage GPS
        Route::get('/tracking/list', [App\Http\Controllers\Admin\AdminController::class, 'trackingList'])->name('tracking.list');
        
        // 🆕 Routes pour le traçage GPS des commandes
        Route::get('/{order}/tracking', [App\Http\Controllers\Admin\AdminController::class, 'orderTracking'])->name('tracking');
        Route::post('/{order}/tracking', [App\Http\Controllers\Admin\AdminController::class, 'updateOrderTracking'])->name('tracking.update');
        
        // 🆕 Routes pour la facture imprimable
        Route::get('/{order}/invoice', [App\Http\Controllers\Admin\AdminController::class, 'orderInvoice'])->name('invoice');
        Route::get('/{order}/invoice/download', [App\Http\Controllers\Admin\AdminController::class, 'downloadOrderInvoice'])->name('invoice.download');
    });

    // Gestion des marques (CRUD complet)
    Route::prefix('brands')->name('brands.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'brands'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminController::class, 'brandCreate'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'brandStore'])->name('store');
        Route::get('/{brand}', [App\Http\Controllers\Admin\AdminController::class, 'brandShow'])->name('show');
        Route::get('/{brand}/edit', [App\Http\Controllers\Admin\AdminController::class, 'brandEdit'])->name('edit');
        Route::put('/{brand}', [App\Http\Controllers\Admin\AdminController::class, 'brandUpdate'])->name('update');
        Route::delete('/{brand}', [App\Http\Controllers\Admin\AdminController::class, 'brandDestroy'])->name('destroy');
        Route::patch('/{brand}/status', [App\Http\Controllers\Admin\AdminController::class, 'brandUpdateStatus'])->name('update-status');
    });

    // Gestion des catégories (CRUD complet)
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'categories'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminController::class, 'categoryCreate'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'categoryStore'])->name('store');
        Route::get('/{category}', [App\Http\Controllers\Admin\AdminController::class, 'categoryShow'])->name('show');
        Route::get('/{category}/edit', [App\Http\Controllers\Admin\AdminController::class, 'categoryEdit'])->name('edit');
        Route::put('/{category}', [App\Http\Controllers\Admin\AdminController::class, 'categoryUpdate'])->name('update');
        Route::delete('/{category}', [App\Http\Controllers\Admin\AdminController::class, 'categoryDestroy'])->name('destroy');
        Route::patch('/{category}/status', [App\Http\Controllers\Admin\AdminController::class, 'categoryUpdateStatus'])->name('update-status');
    });

    // Gestion des articles/items (CRUD complet)
    Route::prefix('items')->name('items.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'items'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\AdminController::class, 'itemCreate'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'itemStore'])->name('store');
        Route::get('/{item}', [App\Http\Controllers\Admin\AdminController::class, 'itemShow'])->name('show');
        Route::get('/{item}/edit', [App\Http\Controllers\Admin\AdminController::class, 'itemEdit'])->name('edit');
        Route::put('/{item}', [App\Http\Controllers\Admin\AdminController::class, 'itemUpdate'])->name('update');
        Route::delete('/{item}', [App\Http\Controllers\Admin\AdminController::class, 'itemDestroy'])->name('destroy');
        Route::patch('/{item}/status', [App\Http\Controllers\Admin\AdminController::class, 'itemUpdateStatus'])->name('update-status');
        Route::patch('/{item}/approve', [App\Http\Controllers\Admin\AdminController::class, 'itemApprove'])->name('approve');
        Route::patch('/{item}/reject', [App\Http\Controllers\Admin\AdminController::class, 'itemReject'])->name('reject');
        Route::post('/bulk-action', [App\Http\Controllers\Admin\AdminController::class, 'itemsBulkAction'])->name('bulk-action');
    });

    // Rapports et statistiques
    Route::get('/reports', [App\Http\Controllers\Admin\AdminController::class, 'reports'])->name('reports');
    
    // Logs système
    Route::get('/logs', [App\Http\Controllers\Admin\AdminController::class, 'logs'])->name('logs');

    // Paramètres système
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'settings'])->name('index');
        Route::put('/update', [App\Http\Controllers\Admin\AdminController::class, 'settingsUpdate'])->name('update');
        
        // Paramètres de pré-inscription
        Route::get('/preregistration', [App\Http\Controllers\Admin\AdminController::class, 'preregistrationSettings'])->name('preregistration');
        Route::put('/preregistration', [App\Http\Controllers\Admin\AdminController::class, 'updatePreregistrationSettings'])->name('preregistration.update');
        Route::post('/preregistration/toggle', [App\Http\Controllers\Admin\AdminController::class, 'togglePreregistration'])->name('preregistration.toggle');
        
        // Anciennes routes (à garder pour compatibilité)
        Route::get('/test', function() { return view('admin.settings.test'); })->name('test');
        Route::post('/update', [App\Http\Controllers\Admin\AdminController::class, 'updateSettings'])->name('update.old');
        Route::post('/clear-cache', [App\Http\Controllers\Admin\AdminController::class, 'clearSettingsCache'])->name('clear-cache');
        
        // Routes pour le mode maintenance
        Route::post('/maintenance/enable', [App\Http\Controllers\Admin\AdminController::class, 'enableMaintenance'])->name('maintenance.enable');
        Route::post('/maintenance/disable', [App\Http\Controllers\Admin\AdminController::class, 'disableMaintenance'])->name('maintenance.disable');
        Route::get('/maintenance/status', [App\Http\Controllers\Admin\AdminController::class, 'maintenanceStatus'])->name('maintenance.status');
        
        // Routes pour les restrictions géographiques
        Route::post('/location-restrictions/toggle', [App\Http\Controllers\Admin\AdminController::class, 'toggleLocationRestrictions'])->name('location-restrictions.toggle');
        Route::get('/location-restrictions/status', [App\Http\Controllers\Admin\AdminController::class, 'getLocationRestrictionsStatus'])->name('location-restrictions.status');
        
        // Routes pour les Hero Slides (Carrousel)
        Route::prefix('hero-slides')->name('hero-slides.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'heroSlides'])->name('index');
            Route::post('/', [App\Http\Controllers\Admin\AdminController::class, 'storeHeroSlide'])->name('store');
            Route::put('/{slide}', [App\Http\Controllers\Admin\AdminController::class, 'updateHeroSlide'])->name('update');
            Route::delete('/{slide}', [App\Http\Controllers\Admin\AdminController::class, 'destroyHeroSlide'])->name('destroy');
            Route::post('/{slide}/toggle', [App\Http\Controllers\Admin\AdminController::class, 'toggleHeroSlide'])->name('toggle');
            Route::post('/reorder', [App\Http\Controllers\Admin\AdminController::class, 'reorderHeroSlides'])->name('reorder');
        });
        
        // Routes pour la Newsletter
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\AdminController::class, 'newsletterSubscribers'])->name('subscribers');
            Route::get('/send', [App\Http\Controllers\Admin\AdminController::class, 'sendNewsletter'])->name('send');
            Route::post('/send', [App\Http\Controllers\Admin\AdminController::class, 'processSendNewsletter'])->name('process');
            Route::delete('/{id}', [App\Http\Controllers\Admin\AdminController::class, 'deleteNewsletterSubscriber'])->name('delete');
            Route::post('/{id}/toggle', [App\Http\Controllers\Admin\AdminController::class, 'toggleNewsletterSubscriber'])->name('toggle');
            Route::get('/export', [App\Http\Controllers\Admin\AdminController::class, 'exportNewsletterSubscribers'])->name('export');
        });
    });

    // API pour notifications
    Route::get('/notifications', [App\Http\Controllers\Admin\AdminController::class, 'notifications'])->name('notifications');
    
    // Routes pour le support client
    Route::prefix('support')->name('support.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('index');
        Route::get('/stats', [App\Http\Controllers\Admin\SupportController::class, 'stats'])->name('stats');
        Route::get('/{supportChat}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('show');
        Route::post('/{supportChat}/reply', [App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('reply');
        Route::post('/{supportChat}/assign', [App\Http\Controllers\Admin\SupportController::class, 'assign'])->name('assign');
        Route::post('/{supportChat}/status', [App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('status');
        Route::post('/{supportChat}/priority', [App\Http\Controllers\Admin\SupportController::class, 'updatePriority'])->name('priority');
        Route::post('/{supportChat}/close', [App\Http\Controllers\Admin\SupportController::class, 'close'])->name('close');
        Route::post('/{supportChat}/reopen', [App\Http\Controllers\Admin\SupportController::class, 'reopen'])->name('reopen');
    });

    // Routes pour la gestion des zones géographiques autorisées
    Route::prefix('settings/locations')->name('locations.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\LocationAccessController::class, 'index'])->name('index');
        Route::post('/seed', [App\Http\Controllers\Admin\LocationAccessController::class, 'seedDefaultCities'])->name('seed');
        
        // Routes pour les villes
        Route::post('/cities', [App\Http\Controllers\Admin\LocationAccessController::class, 'storeCity'])->name('cities.store');
        Route::put('/cities/{city}', [App\Http\Controllers\Admin\LocationAccessController::class, 'updateCity'])->name('cities.update');
        Route::delete('/cities/{city}', [App\Http\Controllers\Admin\LocationAccessController::class, 'destroyCity'])->name('cities.destroy');
        Route::post('/cities/{city}/toggle-status', [App\Http\Controllers\Admin\LocationAccessController::class, 'toggleCityStatus'])->name('cities.toggle');
        
        // Routes pour les régions
        Route::post('/regions', [App\Http\Controllers\Admin\LocationAccessController::class, 'storeRegion'])->name('regions.store');
        Route::put('/regions/{region}', [App\Http\Controllers\Admin\LocationAccessController::class, 'updateRegion'])->name('regions.update');
        Route::delete('/regions/{region}', [App\Http\Controllers\Admin\LocationAccessController::class, 'destroyRegion'])->name('regions.destroy');
        Route::post('/regions/{region}/toggle-status', [App\Http\Controllers\Admin\LocationAccessController::class, 'toggleRegionStatus'])->name('regions.toggle');
        
        // 🆕 API Routes pour GPS et pays
        Route::get('/api/countries', [App\Http\Controllers\Admin\LocationAccessController::class, 'getCountries'])->name('api.countries');
        Route::get('/api/countries/{countryCode}/major-cities', [App\Http\Controllers\Admin\LocationAccessController::class, 'getMajorCitiesByCountry'])->name('api.major-cities');
        Route::get('/api/countries/{countryCode}/cities', [App\Http\Controllers\Admin\LocationAccessController::class, 'getCitiesByCountry'])->name('api.cities-by-country');
        Route::get('/api/cities/map', [App\Http\Controllers\Admin\LocationAccessController::class, 'getCitiesForMap'])->name('api.cities-map');
        Route::post('/api/cities/nearby', [App\Http\Controllers\Admin\LocationAccessController::class, 'searchCitiesNearby'])->name('api.cities-nearby');
        Route::post('/api/validate-coordinates', [App\Http\Controllers\Admin\LocationAccessController::class, 'validateCoordinatesForCountry'])->name('api.validate-coordinates');
        
        // 🌍 Nouvelles routes mondiales
        Route::get('/api/world/countries', [App\Http\Controllers\Admin\LocationAccessController::class, 'getAllCountries'])->name('api.world-countries');
        Route::get('/api/world/cities/search', [App\Http\Controllers\Admin\LocationAccessController::class, 'searchWorldCities'])->name('api.search-world-cities');
        Route::post('/api/world/cities/geocode', [App\Http\Controllers\Admin\LocationAccessController::class, 'geocodeCity'])->name('api.geocode-city');
    });

    // 🎯 Gestion des Affiliations et Récompenses
    Route::prefix('affiliate')->name('affiliate.')->group(function () {
        // Page principale de gestion
        Route::get('/', [App\Http\Controllers\Admin\AffiliateController::class, 'index'])->name('index');
        
        // API Routes pour les données
        Route::prefix('api')->name('api.')->group(function () {
            // Statistiques du dashboard
            Route::get('/stats', [App\Http\Controllers\Admin\AffiliateController::class, 'getDashboardStats'])->name('stats');
            
            // Top performers
            Route::get('/top-performers', [App\Http\Controllers\Admin\AffiliateController::class, 'getTopPerformers'])->name('top-performers');
            
            // Liste des parrains avec filtres et pagination
            Route::get('/referrers', [App\Http\Controllers\Admin\AffiliateController::class, 'getReferrers'])->name('referrers');
            
            // Options pour les selects
            Route::get('/referrer-options', [App\Http\Controllers\Admin\AffiliateController::class, 'getReferrersList'])->name('referrer-options');
            
            // Activités récentes
            Route::get('/recent-activity', [App\Http\Controllers\Admin\AffiliateController::class, 'getRecentActivity'])->name('recent-activity');
            
            // Statistiques par niveau (pour graphique)
            Route::get('/level-stats', [App\Http\Controllers\Admin\AffiliateController::class, 'getLevelStats'])->name('level-stats');
            
            // Statistiques des codes
            Route::get('/codes/stats', [App\Http\Controllers\Admin\AffiliateController::class, 'getCodesStats'])->name('codes-stats');
        });
        
        // Gestion des récompenses
        Route::prefix('rewards')->name('rewards.')->group(function () {
            Route::post('/', [App\Http\Controllers\Admin\AffiliateController::class, 'createReward'])->name('create');
            Route::get('/', [App\Http\Controllers\Admin\AffiliateController::class, 'getRewards'])->name('index');
            Route::get('/{reward}', [App\Http\Controllers\Admin\AffiliateController::class, 'getReward'])->name('show');
            Route::put('/{reward}', [App\Http\Controllers\Admin\AffiliateController::class, 'updateReward'])->name('update');
            Route::delete('/{reward}', [App\Http\Controllers\Admin\AffiliateController::class, 'revokeReward'])->name('revoke');
        });
        
        // Actions sur les parrains
        Route::prefix('referrers')->name('referrers.')->group(function () {
            Route::get('/{user}/details', [App\Http\Controllers\Admin\AffiliateController::class, 'getReferrerDetails'])->name('details');
            Route::post('/{user}/promote', [App\Http\Controllers\Admin\AffiliateController::class, 'promoteReferrer'])->name('promote');
            Route::post('/{user}/suspend', [App\Http\Controllers\Admin\AffiliateController::class, 'suspendReferrer'])->name('suspend');
            Route::post('/{user}/message', [App\Http\Controllers\Admin\AffiliateController::class, 'sendMessageToReferrer'])->name('message');
            Route::get('/{user}/export', [App\Http\Controllers\Admin\AffiliateController::class, 'exportReferrerData'])->name('export');
        });
        
        // Export et rapports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/export', [App\Http\Controllers\Admin\AffiliateController::class, 'exportReport'])->name('export');
            Route::get('/top-performers/export', [App\Http\Controllers\Admin\AffiliateController::class, 'exportTopPerformers'])->name('top-performers.export');
            Route::post('/bulk-reward', [App\Http\Controllers\Admin\AffiliateController::class, 'bulkReward'])->name('bulk-reward');
        });
    });
});

// Route publique pour la page de restriction géographique
Route::get('/city-restricted', function() {
    return view('errors.city_restricted');
})->name('city.restricted');

// Routes pour le support client (côté utilisateur)
Route::middleware('auth')->prefix('support')->name('support.')->group(function () {
    Route::get('/', [App\Http\Controllers\SupportController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\SupportController::class, 'create'])->name('create');
    Route::post('/store', [App\Http\Controllers\SupportController::class, 'store'])->name('store');
    Route::get('/{supportChat}', [App\Http\Controllers\SupportController::class, 'show'])->name('show');
    Route::post('/{supportChat}/reply', [App\Http\Controllers\SupportController::class, 'reply'])->name('reply');
    Route::post('/{supportChat}/close', [App\Http\Controllers\SupportController::class, 'close'])->name('close');
    Route::get('/widget/content', [App\Http\Controllers\SupportController::class, 'widget'])->name('widget');
    Route::post('/quick-chat', [App\Http\Controllers\SupportController::class, 'quickChat'])->name('quick-chat');
});

// Routes pour les paiements mobile money (Illicocash, Orange Money, Airtel Money, Mpesa, Africell), la simulation et le callback
Route::prefix('payments')->group(function () {
    // Page de test pour la simulation de paiement
    Route::get('/test', function () {
        return view('payments.test');
    })->name('payments.test');
    
    // Page de simulation d'achat complet
    Route::get('/simulate-purchase', function () {
        return view('payments.simulate-purchase');
    })->name('payments.simulate-purchase');
    
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('payments.process');
    Route::post('/illicocash', [PaymentController::class, 'payWithIllicocash'])->name('payments.illicocash');
    Route::post('/orange-money', [PaymentController::class, 'payWithOrangeMoney'])->name('payments.orange_money');
    Route::post('/airtel-money', [PaymentController::class, 'payWithAirtelMoney'])->name('payments.airtel_money');
    Route::post('/mpesa', [PaymentController::class, 'payWithMpesa'])->name('payments.mpesa');
    Route::post('/africell', [PaymentController::class, 'payWithAfricell'])->name('payments.africell');
    Route::post('/simulate', [PaymentController::class, 'simulatePayment'])->name('payments.simulate');
    Route::post('/callback', [PaymentController::class, 'handleCallback'])->name('payments.callback');
    
    // Page de suivi du paiement en temps réel
    Route::get('/status/{transaction}', function ($transactionId) {
        $transaction = \App\Models\Transaction::findOrFail($transactionId);
        return view('payment-status', compact('transaction'));
    })->name('payments.status');
    
    // Pages de callback pour simulation
    Route::get('/success/{transaction_id}', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('/error', [PaymentController::class, 'paymentError'])->name('payments.error');
    
    // Routes de remboursement
    Route::post('/orders/{order}/refund/request', [PaymentController::class, 'requestRefund'])->name('refund.request');
    Route::post('/refunds/{refund}/process', [PaymentController::class, 'processRefund'])->name('refund.process');
});

// Routes d'administration des remboursements
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/refunds', [App\Http\Controllers\Admin\RefundController::class, 'index'])->name('refunds.index');
    Route::get('/refunds/{refund}', [App\Http\Controllers\Admin\RefundController::class, 'show'])->name('refunds.show');
});

// Routes pour le panier
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add/{itemId}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::get('/pay', [CartController::class, 'pay'])->name('cart.pay');
    Route::get('/buy/{id}', [App\Http\Controllers\ItemController::class, 'buy'])->name('cart.buy');
});

// Routes pour les adresses de livraison
Route::middleware(['auth'])->prefix('delivery-address')->name('delivery.')->group(function () {
    Route::post('/', [App\Http\Controllers\UserController::class, 'saveDeliveryAddress'])->name('save');
    Route::get('/', [App\Http\Controllers\UserController::class, 'getDeliveryAddresses'])->name('list');
    Route::get('/default', [App\Http\Controllers\UserController::class, 'getDefaultDeliveryAddress'])->name('default');
    Route::put('/{id}', [App\Http\Controllers\UserController::class, 'updateDeliveryAddress'])->name('update');
    Route::post('/{id}/set-default', [App\Http\Controllers\UserController::class, 'setDefaultDeliveryAddress'])->name('set-default');
    Route::delete('/{id}', [App\Http\Controllers\UserController::class, 'deleteDeliveryAddress'])->name('delete');
});

// Routes pour les livraisons locales
Route::middleware(['auth'])->prefix('local-delivery')->name('local-delivery.')->group(function () {
    // Afficher le formulaire de création
    Route::get('/create', [LocalDeliveryController::class, 'create'])->name('create');
    
    // Proposer une livraison locale
    Route::post('/propose', [LocalDeliveryController::class, 'proposeDelivery'])->name('propose');
    
    // API pour géocodage d'adresse
    Route::post('/geocode', [LocalDeliveryController::class, 'geocodeAddress'])->name('geocode');
    
    // Accepter une livraison locale
    Route::post('/{localDelivery}/accept', [LocalDeliveryController::class, 'acceptDelivery'])->name('accept');
    
    // Marquer comme en transit (vendeur uniquement)
    Route::post('/{localDelivery}/in-transit', [LocalDeliveryController::class, 'markInTransit'])->name('in-transit');
    
    // Marquer comme livré (acheteur uniquement - avec code de vérification)
    Route::post('/{localDelivery}/delivered', [LocalDeliveryController::class, 'markDelivered'])->name('delivered');
    
    // Annuler une livraison locale
    Route::post('/{localDelivery}/cancel', [LocalDeliveryController::class, 'cancelDelivery'])->name('cancel');
    
    // Voir les détails d'une livraison locale
    Route::get('/{localDelivery}', [LocalDeliveryController::class, 'show'])->name('show');
    
    // API pour récupérer les livraisons d'un utilisateur
    Route::get('/user/{type}', [LocalDeliveryController::class, 'getUserDeliveries'])->name('user')
        ->where('type', 'seller|buyer');
});

// Routes Taux de change (PUBLIC - pas d'auth nécessaire pour consulter le taux)
Route::prefix('exchange')->name('exchange.')->group(function () {
    Route::get('/rate', [ExchangeRateController::class, 'getRate'])->name('rate');
    Route::get('/history', [ExchangeRateController::class, 'history'])->name('history');
    
    // Routes nécessitant une authentification
    Route::middleware('auth')->group(function () {
        Route::post('/convert', [ExchangeRateController::class, 'convert'])->name('convert');
    });
    
    // Route admin uniquement
    Route::post('/refresh-rate', [ExchangeRateController::class, 'refreshRate'])
        ->name('refresh-rate')
        ->middleware(['auth', 'admin']);
});

// Routes pour le wallet et les transactions
Route::middleware(['auth'])->group(function () {
    // Routes Wallet
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [App\Http\Controllers\WalletController::class, 'index'])->name('index');
        Route::get('/{wallet}/transactions', [App\Http\Controllers\WalletController::class, 'transactions'])->name('transactions');
        Route::get('/{wallet}/add-funds', [App\Http\Controllers\WalletController::class, 'addFunds'])->name('add-funds');
        Route::post('/{wallet}/add-funds', [App\Http\Controllers\WalletController::class, 'storeAddFunds'])->name('store-add-funds');
        Route::get('/{wallet}/withdraw-funds', [App\Http\Controllers\WalletController::class, 'withdrawFunds'])->name('withdraw-funds');
        Route::post('/{wallet}/withdraw-funds', [App\Http\Controllers\WalletController::class, 'storeWithdrawFunds'])->name('store-withdraw-funds');
        Route::get('/{wallet}/balance', [App\Http\Controllers\WalletController::class, 'getBalance'])->name('balance');
        Route::post('/{wallet}/recharge/mobile', [App\Http\Controllers\WalletController::class, 'rechargeWithMobilePayment'])->name('recharge-mobile');
        Route::post('/convert', [App\Http\Controllers\WalletController::class, 'convertCurrency'])->name('convert');
        
        // Routes pour les retraits (admin uniquement)
        Route::post('/withdrawals/{withdrawalRequest}/retry', [App\Http\Controllers\WalletController::class, 'retryFailedWithdrawal'])
            ->name('withdrawals.retry')
            ->middleware('admin');
    });

    // Routes Webhook pour les décaissements (PUBLIC - pas d'auth)
    Route::prefix('wallet/withdrawals/webhook')->name('withdrawals.webhook.')->group(function () {
        Route::post('/{provider}', [App\Http\Controllers\WalletController::class, 'handleWithdrawalWebhook'])
            ->name('provider')
            ->withoutMiddleware(['auth']);
    });

    // Routes Transactions
    Route::prefix('admin/transactions')->name('admin.transactions.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::get('/{transaction}', [TransactionController::class, 'show'])->name('show');
        Route::patch('/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('update-status');
        Route::get('/statistics', [TransactionController::class, 'statistics'])->name('statistics');
    });

    // Routes Pending Wallet
    Route::prefix('admin/pending-wallets')->name('admin.pending-wallets.')->middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [PendingWalletController::class, 'index'])->name('index');
        Route::get('/{wallet}', [PendingWalletController::class, 'show'])->name('show');
        Route::post('/{wallet}/confirm-transfer', [PendingWalletController::class, 'confirmTransfer'])->name('confirm-transfer');
        Route::post('/{wallet}/cancel-transaction', [PendingWalletController::class, 'cancelTransaction'])->name('cancel-transaction');
    });

    // Routes Contact et Réductions
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::post('/seller/{item}', [App\Http\Controllers\ContactController::class, 'contactSeller'])->name('seller');
    });

    Route::prefix('discounts')->name('discounts.')->group(function () {
        Route::get('/{discount}', [App\Http\Controllers\ContactController::class, 'show'])->name('show');
        Route::post('/{discount}/approve', [App\Http\Controllers\ContactController::class, 'proposeDiscount'])->name('approve');
        Route::post('/{discount}/reject', [App\Http\Controllers\ContactController::class, 'rejectDiscount'])->name('reject');
        Route::post('/{discount}/apply', [App\Http\Controllers\ContactController::class, 'applyDiscount'])->name('apply');
        Route::get('/item/{item}/available', [App\Http\Controllers\MessageController::class, 'getAvailableDiscounts'])->name('available');
    });
});

// ========================================
// Routes de pré-inscription (PUBLIC)
// ========================================
Route::prefix('preregistration')->name('preregistration.')->group(function () {
    Route::get('/', [App\Http\Controllers\PreRegistrationController::class, 'index'])->name('index');
    Route::post('/', [App\Http\Controllers\PreRegistrationController::class, 'store'])->name('store');
    Route::get('/success', [App\Http\Controllers\PreRegistrationController::class, 'success'])->name('success');
    Route::get('/confirm/{token}', [App\Http\Controllers\PreRegistrationController::class, 'confirm'])->name('confirm');
    Route::get('/already-confirmed', [App\Http\Controllers\PreRegistrationController::class, 'alreadyConfirmed'])->name('already-confirmed');
    Route::get('/stats', [App\Http\Controllers\PreRegistrationController::class, 'stats'])->name('stats');
});

// ========================================
// Routes admin pour gérer les pré-inscriptions
// ========================================
Route::middleware(['auth', 'admin'])->prefix('admin/waiting-users')->name('admin.waiting-users.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\WaitingUsersController::class, 'index'])->name('index');
    Route::get('/{waitingUser}', [App\Http\Controllers\Admin\WaitingUsersController::class, 'show'])->name('show');
    Route::post('/{waitingUser}/approve', [App\Http\Controllers\Admin\WaitingUsersController::class, 'approve'])->name('approve');
    Route::post('/{waitingUser}/reject', [App\Http\Controllers\Admin\WaitingUsersController::class, 'reject'])->name('reject');
    Route::post('/bulk-action', [App\Http\Controllers\Admin\WaitingUsersController::class, 'bulkAction'])->name('bulk-action');
    Route::post('/{waitingUser}/resend-confirmation', [App\Http\Controllers\Admin\WaitingUsersController::class, 'resendConfirmation'])->name('resend-confirmation');
    Route::delete('/{waitingUser}', [App\Http\Controllers\Admin\WaitingUsersController::class, 'destroy'])->name('destroy');
    Route::get('/export/csv', [App\Http\Controllers\Admin\WaitingUsersController::class, 'export'])->name('export');
});

// ========================================
// Routes publiques pour définir le mot de passe après approbation
// ========================================
Route::get('/set-password', [App\Http\Controllers\Admin\AdminController::class, 'showSetPasswordForm'])->name('password.setup');
Route::post('/set-password', [App\Http\Controllers\Admin\AdminController::class, 'setPassword'])->name('password.setup.store');

// Firebase Authentication Routes
Route::prefix('firebase')->name('firebase.')->group(function () {
    // Routes de connexion Firebase
    Route::post('/login', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'loginWithFirebase'])->name('login');
    Route::post('/register', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'registerWithFirebase'])->name('register');
    Route::post('/logout', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'logout'])->name('logout')->middleware('auth');
    
    // Vérification de l'état d'authentification
    Route::get('/check-auth', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'checkAuthStatus'])->name('check-auth');
    
    // Gestion du token FCM pour les notifications push
    Route::post('/fcm-token', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'saveFcmToken'])->name('fcm-token')->middleware('auth');
    Route::delete('/fcm-token', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'removeFcmToken'])->name('fcm-token.remove')->middleware('auth');
    
    // Routes de test Firebase (à supprimer en production)
    if (app()->environment(['local', 'testing'])) {
        Route::get('/test-config', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'testConfig'])->name('test-config');
        Route::get('/test-notification/{userId}', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'testNotification'])->name('test-notification');
    }
});

require __DIR__.'/auth.php';

// === ROUTES SYSTEME D'AFFILIATION ===
Route::middleware(['auth'])->prefix('affiliate')->name('affiliate.')->group(function () {
    // Dashboard principal
    Route::get('/dashboard', [AffiliateController::class, 'showDashboard'])->name('dashboard');
    Route::get('/dashboard-data', [AffiliateController::class, 'dashboard'])->name('dashboard.data');
    
    // Gestion des codes de parrainage
    Route::get('/referral-codes', [AffiliateController::class, 'getReferralCodes'])->name('referral-codes.index');
    Route::post('/referral-codes', [AffiliateController::class, 'createReferralCode'])->name('referral-codes.create');
    
    // Parrainages
    Route::get('/referrals', [AffiliateController::class, 'getReferrals'])->name('referrals.index');
    Route::post('/apply-referral', [AffiliateController::class, 'applyReferralCode'])->name('referrals.apply');
    
    // Points et transactions
    Route::get('/points/history', [AffiliateController::class, 'getPointsHistory'])->name('points.history');
    Route::post('/points/convert-cash', [AffiliateController::class, 'convertPointsToCash'])->name('points.convert-cash');
    Route::post('/points/generate-discount', [AffiliateController::class, 'generateDiscountCode'])->name('points.generate-discount');
    Route::post('/points/calculate-conversion', [AffiliateController::class, 'calculateConversion'])->name('points.calculate');
    
    // Rachats
    Route::get('/redemptions', [AffiliateController::class, 'getRedemptions'])->name('redemptions.index');
    
    // Classement
    Route::get('/leaderboard', [AffiliateController::class, 'getLeaderboard'])->name('leaderboard');
});

// Route publique pour l'inscription avec support des codes de parrainage
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');

// Route courte pour les liens de parrainage (ex: /r/MONCODE)
Route::get('/r/{code}', function ($code) {
    return redirect()->route('register', ['ref' => $code]);
})->name('referral.link');

// Route pour le centre d'aide
Route::get('/help', function() {
    return view('help.index');
})->name('help.index');

// Route pour les conditions d'utilisation
Route::get('/terms', function() {
    return view('legal.terms');
})->name('terms');

// Route pour la politique de confidentialité
Route::get('/privacy', function() {
    return view('legal.privacy');
})->name('privacy');

// Routes Firebase Authentication
Route::post('/auth/firebase/login', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'loginWithFirebase'])->name('auth.firebase.login');
Route::post('/auth/firebase/register', [App\Http\Controllers\Auth\FirebaseAuthController::class, 'registerWithFirebase'])->name('auth.firebase.register');

// ==========================================
// Routes de vérification d'authenticité
// ==========================================
Route::middleware(['auth'])->prefix('authenticity')->name('authenticity.')->group(function () {
    // Dashboard des vérifications pour l'utilisateur
    Route::get('/dashboard', [AuthenticityController::class, 'dashboard'])->name('dashboard');
    
    // Demande de vérification pour un produit
    Route::get('/request/{item}', [AuthenticityController::class, 'requestForm'])->name('request');
    Route::post('/request/{item}', [AuthenticityController::class, 'submit'])->name('submit');
    
    // Paiement de la vérification
    Route::get('/payment/{check}', [AuthenticityController::class, 'payment'])->name('payment');
    Route::post('/payment/{check}/confirm', [AuthenticityController::class, 'confirmPayment'])->name('payment.confirm');
    
    // Statut de vérification
    Route::get('/status/{item}', [AuthenticityController::class, 'status'])->name('status');
    
    // API pour les experts (mise à jour du statut)
    Route::post('/update-status/{check}', [AuthenticityController::class, 'updateStatus'])->name('update.status');
});

// Routes temporaires pour tester le mode maintenance (à supprimer en production)
if (app()->environment(['local', 'testing'])) {
    require __DIR__.'/test-maintenance.php';
    require __DIR__.'/test-location.php'; // Test géolocalisation
    require __DIR__.'/test-location-simulate.php'; // Simulation IPs
}

// Route de test pour debug expert
Route::get('/test-expert-view/{id}', function ($id) {
    try {
        $check = App\Models\ProductAuthenticityCheck::findOrFail($id);
        
        // Charger les relations comme dans le contrôleur
        $check->load([
            'item.category',
            'item.brand', 
            'item.user',
            'vendor',
            'verificationImages',
            'auditLogs.performer'
        ]);
        
        return view('expert.test-show', compact('check'));
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
    }
});

// Routes des experts
require __DIR__.'/expert.php';
