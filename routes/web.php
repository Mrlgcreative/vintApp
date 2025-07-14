<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ThemeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CartController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Route de test temporaire
Route::get('/test-create', function() {
    return 'Route de test accessible';
});

// Routes publiques pour les items
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');

Route::middleware('auth')->group(function () {
    // Routes pour les items (CRUD) - Routes spécifiques AVANT les routes avec paramètres
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/my-items', [ItemController::class, 'myItems'])->name('items.my-items');
    Route::get('/items/personalization', [ItemController::class, 'personalization'])->name('items.personalization');
    
    // Routes avec paramètres
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    
    // Routes spéciales pour les items
    Route::post('/items/{item}/favorite', [ItemController::class, 'toggleFavorite'])->name('items.favorite');
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
    
    // Dashboard routes
    Route::get('/dashboard/analytics', [App\Http\Controllers\DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/notifications', [App\Http\Controllers\DashboardController::class, 'notifications'])->name('dashboard.notifications');
    Route::patch('/dashboard/notifications/{id}/read', [App\Http\Controllers\DashboardController::class, 'markNotificationAsRead'])->name('dashboard.notifications.read');
    Route::patch('/dashboard/notifications/read-all', [App\Http\Controllers\DashboardController::class, 'markAllNotificationsAsRead'])->name('dashboard.notifications.read-all');
});

// Routes pour les commandes
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
});

// Routes pour le thème
Route::middleware(['auth'])->group(function () {
    Route::post('/theme/toggle', [ThemeController::class, 'toggle'])->name('theme.toggle');
    Route::post('/theme/set', [ThemeController::class, 'set'])->name('theme.set');
    Route::get('/theme/get', [ThemeController::class, 'get'])->name('theme.get');
});

// Routes pour les catégories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

// Routes pour les marques
Route::resource('brands', App\Http\Controllers\BrandController::class);
Route::resource('reviews', App\Http\Controllers\ReviewController::class);

// Routes pour les paiements mobile money (Illicocash, Orange Money, Airtel Money, Mpesa, Africell), la simulation et le callback
Route::prefix('payments')->group(function () {
    Route::post('/illicocash', [PaymentController::class, 'payWithIllicocash'])->name('payments.illicocash');
    Route::post('/orange-money', [PaymentController::class, 'payWithOrangeMoney'])->name('payments.orange_money');
    Route::post('/airtel-money', [PaymentController::class, 'payWithAirtelMoney'])->name('payments.airtel_money');
    Route::post('/mpesa', [PaymentController::class, 'payWithMpesa'])->name('payments.mpesa');
    Route::post('/africell', [PaymentController::class, 'payWithAfricell'])->name('payments.africell');
    Route::post('/simulate', [PaymentController::class, 'simulatePayment'])->name('payments.simulate');
    Route::post('/callback', [PaymentController::class, 'handleCallback'])->name('payments.callback');
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

require __DIR__.'/auth.php';
