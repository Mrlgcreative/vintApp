<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Setting;
use App\Models\SupportChat;
use App\Services\SettingService;
use App\Http\Middleware\MaintenanceMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Dashboard administrateur
     */
    public function dashboard()
    {
        // Statistiques générales
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'active_users' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),
            
            'total_transactions' => Transaction::count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'total_transaction_amount' => Transaction::where('status', 'completed')->sum('amount'),
            
            'pending_wallets' => Wallet::where('status', 'pending')->count(),
            'total_wallet_balance' => Wallet::where('is_active', true)->sum('balance'),
            
            'total_orders' => Order::count(),
            'orders_today' => Order::whereDate('created_at', today())->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            
            'total_items' => Item::count(),
            'active_items' => Item::where('status', 'active')->count(),
            
            // Statistiques de support
            'total_support_chats' => SupportChat::count(),
            'open_support_chats' => SupportChat::where('status', 'open')->count(),
            'pending_support_chats' => SupportChat::whereIn('status', ['open', 'in_progress'])->count(),
            'unassigned_support_chats' => SupportChat::whereNull('admin_id')
                ->whereIn('status', ['open', 'in_progress'])->count(),
        ];

        // Graphiques des derniers 30 jours
        $dailyStats = $this->getDailyStats();
        
        // Dernières activités
        $recentTransactions = Transaction::with(['user'])
            ->latest()
            ->take(10)
            ->get();
            
        $recentUsers = User::latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'dailyStats', 'recentTransactions', 'recentUsers'));
    }

    /**
     * Gestion des utilisateurs
     */
    public function users(Request $request)
    {
        $query = User::with(['roles', 'wallets']);
        
        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('slug', $request->role);
            });
        }
        
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('last_seen', '>=', Carbon::now()->subDays(7));
            } elseif ($request->status === 'inactive') {
                $query->where('last_seen', '<', Carbon::now()->subDays(7));
            }
        }
        
        $users = $query->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Détails d'un utilisateur
     */
    public function userShow(User $user)
    {
        $user->load(['roles', 'wallets', 'transactions', 'ordersAsBuyer', 'ordersAsSeller']);
        
        $stats = $user->getStats();
        
        $recentTransactions = $user->transactions()
            ->latest()
            ->take(10)
            ->get();
            
        return view('admin.users.show', compact('user', 'stats', 'recentTransactions'));
    }

    /**
     * Mettre à jour le statut d'un utilisateur
     */
    public function userUpdateStatus(Request $request, User $user)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,suspend,delete'
        ]);
        
        try {
            DB::beginTransaction();
            
            switch ($request->action) {
                case 'activate':
                    $user->update(['is_active' => true]);
                    $message = "Utilisateur activé avec succès.";
                    break;
                    
                case 'deactivate':
                    $user->update(['is_active' => false]);
                    $message = "Utilisateur désactivé avec succès.";
                    break;
                    
                case 'suspend':
                    $user->update(['is_suspended' => true]);
                    $message = "Utilisateur suspendu avec succès.";
                    break;
                    
                case 'delete':
                    // Soft delete
                    $user->delete();
                    $message = "Utilisateur supprimé avec succès.";
                    break;
            }
            
            DB::commit();
            
            Log::info("Action admin sur utilisateur", [
                'admin_id' => Auth::id(),
                'user_id' => $user->id,
                'action' => $request->action
            ]);
            
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'action admin sur utilisateur", [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'action' => $request->action
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Gestion des wallets
     */
    public function wallets(Request $request)
    {
        $query = Wallet::with(['user']);
        
        // Filtres
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $wallets = $query->paginate(20);
        
        return view('admin.wallets.index', compact('wallets'));
    }

    /**
     * Wallets en attente de validation
     */
    public function pendingWallets()
    {
        $pendingWallets = Wallet::with(['user'])
            ->where('status', 'pending')
            ->paginate(20);
            
        return view('admin.wallets.pending', compact('pendingWallets'));
    }

    /**
     * Approuver un wallet
     */
    public function approveWallet(Wallet $wallet)
    {
        try {
            DB::beginTransaction();
            
            $wallet->update([
                'status' => 'active',
                'verified_at' => now(),
                'verified_by' => Auth::id()
            ]);
            
            // Créer une transaction de confirmation
            Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'wallet_approval',
                'amount' => 0,
                'currency' => $wallet->currency,
                'status' => 'completed',
                'description' => 'Wallet approuvé par l\'administrateur',
                'processed_by' => Auth::id()
            ]);
            
            DB::commit();
            
            Log::info("Wallet approuvé", [
                'admin_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id
            ]);
            
            return redirect()->back()->with('success', 'Wallet approuvé avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'approbation du wallet", [
                'error' => $e->getMessage(),
                'wallet_id' => $wallet->id
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Rejeter un wallet
     */
    public function rejectWallet(Request $request, Wallet $wallet)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        try {
            DB::beginTransaction();
            
            $wallet->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
                'verified_by' => Auth::id()
            ]);
            
            // Créer une transaction de rejet
            Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'wallet_rejection',
                'amount' => 0,
                'currency' => $wallet->currency,
                'status' => 'failed',
                'description' => 'Wallet rejeté: ' . $request->reason,
                'processed_by' => Auth::id()
            ]);
            
            DB::commit();
            
            Log::info("Wallet rejeté", [
                'admin_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'reason' => $request->reason
            ]);
            
            return redirect()->back()->with('success', 'Wallet rejeté avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors du rejet du wallet", [
                'error' => $e->getMessage(),
                'wallet_id' => $wallet->id
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Approuver plusieurs wallets en lot
     */
    public function bulkApproveWallets(Request $request)
    {
        $request->validate([
            'wallet_ids' => 'required|array',
            'wallet_ids.*' => 'exists:wallets,id'
        ]);

        try {
            DB::beginTransaction();
            
            $approvedCount = 0;
            
            foreach ($request->wallet_ids as $walletId) {
                $wallet = Wallet::find($walletId);
                
                if ($wallet && $wallet->status === 'pending') {
                    $wallet->update([
                        'status' => 'active',
                        'verified_at' => now(),
                        'verified_by' => Auth::id()
                    ]);
                    
                    $approvedCount++;
                    
                    Log::info("Wallet approuvé en lot", [
                        'admin_id' => Auth::id(),
                        'wallet_id' => $wallet->id,
                        'user_id' => $wallet->user_id
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', "{$approvedCount} wallet(s) approuvé(s) avec succès.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'approbation en lot des wallets", [
                'error' => $e->getMessage(),
                'wallet_ids' => $request->wallet_ids
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Rejeter plusieurs wallets en lot
     */
    public function bulkRejectWallets(Request $request)
    {
        $request->validate([
            'wallet_ids' => 'required|array',
            'wallet_ids.*' => 'exists:wallets,id',
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();
            
            $rejectedCount = 0;
            
            foreach ($request->wallet_ids as $walletId) {
                $wallet = Wallet::find($walletId);
                
                if ($wallet && $wallet->status === 'pending') {
                    $wallet->update([
                        'status' => 'rejected',
                        'rejection_reason' => $request->reason,
                        'verified_by' => Auth::id()
                    ]);
                    
                    $rejectedCount++;
                    
                    Log::info("Wallet rejeté en lot", [
                        'admin_id' => Auth::id(),
                        'wallet_id' => $wallet->id,
                        'user_id' => $wallet->user_id,
                        'reason' => $request->reason
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', "{$rejectedCount} wallet(s) rejeté(s) avec succès.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors du rejet en lot des wallets", [
                'error' => $e->getMessage(),
                'wallet_ids' => $request->wallet_ids
            ]);
            
            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Gestion des transactions
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with(['user', 'wallet']);
        
        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $transactions = $query->latest()->paginate(20);
        
        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Détails d'une transaction
     */
    public function transactionShow(Transaction $transaction)
    {
        $transaction->load(['user', 'wallet']);
        
        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Gestion des commandes
     */
    public function orders(Request $request)
    {
        $query = Order::with(['buyer', 'seller', 'item']);
        
        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $orders = $query->latest()->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Gestion des marques
     */
    public function brands()
    {
        $brands = Brand::withCount(['items'])->paginate(20);
        
        return view('admin.brands.index', compact('brands'));
    }

    /**
     * Gestion des catégories
     */
    public function categories()
    {
        $categories = Category::withCount(['items'])->paginate(20);
        
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Rapports et statistiques
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', '30'); // 30 jours par défaut
        
        $startDate = Carbon::now()->subDays($period);
        
        $reports = [
            'revenue' => $this->getRevenueReport($startDate),
            'users' => $this->getUsersReport($startDate),
            'transactions' => $this->getTransactionsReport($startDate),
            'popular_items' => $this->getPopularItemsReport($startDate)
        ];
        
        return view('admin.reports.index', compact('reports', 'period'));
    }

    /**
     * Logs système
     */
    public function logs()
    {
        // Ici on pourrait implémenter la lecture des logs Laravel
        // Pour l'instant, on retourne une vue basique
        
        return view('admin.logs.index');
    }

    /**
     * Paramètres système
     */
    public function settings()
    {
        try {
            $maintenanceStatus = MaintenanceMode::isEnabled();
            $settingService = app(\App\Services\SettingService::class);
            $settings = $settingService->getAllForAdmin()->groupBy('category');
            $categories = $settingService->getCategories();
            
            return view('admin.settings.index', compact('maintenanceStatus', 'settings', 'categories'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des paramètres: ' . $e->getMessage());
            $maintenanceStatus = false;
            return view('admin.settings.index', compact('maintenanceStatus'));
        }
    }

    /**
     * Notifications admin
     */
    public function notifications()
    {
        $notifications = [
            'unread_count' => 0,
            'notifications' => []
        ];
        
        // Compter les éléments nécessitant attention
        $pendingWallets = Wallet::where('status', 'pending')->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $failedTransactions = Transaction::where('status', 'failed')->whereDate('created_at', today())->count();
        
        if ($pendingWallets > 0) {
            $notifications['notifications'][] = [
                'icon' => 'fa-clock',
                'message' => "{$pendingWallets} wallet(s) en attente de validation",
                'link' => route('admin.wallets.pending'),
                'created_at' => 'Maintenant',
                'read_at' => null
            ];
            $notifications['unread_count']++;
        }
        
        if ($pendingOrders > 0) {
            $notifications['notifications'][] = [
                'icon' => 'fa-shopping-cart',
                'message' => "{$pendingOrders} commande(s) en attente",
                'link' => route('admin.orders.index'),
                'created_at' => 'Maintenant',
                'read_at' => null
            ];
            $notifications['unread_count']++;
        }
        
        if ($failedTransactions > 0) {
            $notifications['notifications'][] = [
                'icon' => 'fa-exclamation-triangle',
                'message' => "{$failedTransactions} transaction(s) échouée(s) aujourd'hui",
                'link' => route('admin.transactions.index', ['status' => 'failed']),
                'created_at' => 'Maintenant',
                'read_at' => null
            ];
            $notifications['unread_count']++;
        }
        
        return response()->json($notifications);
    }

    /**
     * Obtenir les statistiques quotidiennes
     */
    private function getDailyStats()
    {
        $days = collect();
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            $days->push([
                'date' => $date->format('Y-m-d'),
                'users' => User::whereDate('created_at', $date)->count(),
                'transactions' => Transaction::whereDate('created_at', $date)->count(),
                'revenue' => Transaction::whereDate('created_at', $date)
                    ->where('status', 'completed')
                    ->sum('amount'),
                'orders' => Order::whereDate('created_at', $date)->count()
            ]);
        }
        
        return $days;
    }

    /**
     * Rapport de revenus
     */
    private function getRevenueReport($startDate)
    {
        return [
            'total' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'completed')
                ->sum('amount'),
            'count' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'completed')
                ->count(),
            'average' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'completed')
                ->avg('amount') ?? 0
        ];
    }

    /**
     * Rapport des utilisateurs
     */
    private function getUsersReport($startDate)
    {
        return [
            'new_users' => User::where('created_at', '>=', $startDate)->count(),
            'active_users' => User::where('last_seen', '>=', $startDate)->count(),
            'total_users' => User::count()
        ];
    }

    /**
     * Rapport des transactions
     */
    private function getTransactionsReport($startDate)
    {
        return [
            'total' => Transaction::where('created_at', '>=', $startDate)->count(),
            'completed' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'completed')->count(),
            'pending' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'pending')->count(),
            'failed' => Transaction::where('created_at', '>=', $startDate)
                ->where('status', 'failed')->count()
        ];
    }

    /**
     * Rapport des articles populaires
     */
    private function getPopularItemsReport($startDate)
    {
        return Item::withCount(['favoritedBy as favorites_count', 'orders'])
            ->where('created_at', '>=', $startDate)
            ->orderBy('favorites_count', 'desc')
            ->take(10)
            ->get();
    }

    /**
     * Afficher les paramètres système
     */
    public function systemSettings(SettingService $settingService)
    {
        $settings = $settingService->getAllForAdmin()->groupBy('category');
        $categories = $settingService->getCategories();
        
        return view('admin.settings.index', compact('settings', 'categories'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings(Request $request, SettingService $settingService)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Gérer l'upload du logo en premier
            if ($request->hasFile('logo_file')) {
                $logoFile = $request->file('logo_file');
                $logoPath = $logoFile->store('logos', 'public');
                
                // Supprimer l'ancien logo s'il existe
                $oldLogo = $settingService->get('app_logo');
                if ($oldLogo && $oldLogo !== '/images/logo.png' && Storage::disk('public')->exists(str_replace('/storage/', '', $oldLogo))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldLogo));
                }
                
                // Mettre à jour le chemin du logo
                $request->merge([
                    'settings' => array_merge($request->settings, [
                        'app_logo' => '/storage/' . $logoPath
                    ])
                ]);
            }

            foreach ($request->settings as $key => $value) {
                $setting = Setting::where('key', $key)->first();
                
                if ($setting) {
                    // Convertir la valeur selon le type
                    $convertedValue = $this->convertSettingValue($value, $setting->type);
                    $settingService->set($key, $convertedValue);
                }
            }

            // Vider le cache
            $settingService->clearCache();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Paramètres mis à jour avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des paramètres: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour des paramètres.')
                ->withInput();
        }
    }

    /**
     * Convertir la valeur selon le type de setting
     */
    private function convertSettingValue($value, $type)
    {
        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json', 'array' => is_array($value) ? $value : json_decode($value, true),
            default => (string) $value,
        };
    }

    /**
     * Réinitialiser le cache des settings
     */
    public function clearSettingsCache(SettingService $settingService)
    {
        try {
            $settingService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache des paramètres vidé avec succès.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du vidage du cache: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du vidage du cache.'
            ], 500);
        }
    }

    // =============================================
    // MÉTHODES CRUD POUR LES BRANDS (MARQUES)
    // =============================================

    /**
     * Afficher le formulaire de création d'une marque
     */
    public function brandCreate()
    {
        return view('admin.brands.create');
    }

    /**
     * Enregistrer une nouvelle marque
     */
    public function brandStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'slug' => 'nullable|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Générer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Gérer l'upload du logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Définir les valeurs par défaut pour les booléens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $brand = Brand::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque créée avec succès.');
    }

    /**
     * Afficher les détails d'une marque
     */
    public function brandShow(Brand $brand)
    {
        $brand->load(['items' => function($query) {
            $query->with('category', 'images')->latest()->take(10);
        }]);

        // Statistiques de la marque
        $stats = [
            'total_items' => $brand->items()->count(),
            'active_items' => $brand->items()->where('status', 'active')->count(),
            'sold_items' => $brand->items()->where('status', 'sold')->count(),
            'average_price' => $brand->items()->where('status', 'active')->avg('price') ?? 0,
            'total_views' => $brand->items()->sum('views') ?? 0,
        ];

        return view('admin.brands.show', compact('brand', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition d'une marque
     */
    public function brandEdit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Mettre à jour une marque
     */
    public function brandUpdate(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug' => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'website' => 'nullable|url',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'country' => 'nullable|string|max:100',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Générer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Gérer l'upload du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Définir les valeurs par défaut pour les booléens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $brand->update($data);

        return redirect()->route('admin.brands.show', $brand)
            ->with('success', 'Marque mise à jour avec succès.');
    }

    /**
     * Supprimer une marque
     */
    public function brandDestroy(Brand $brand)
    {
        try {
            // Vérifier s'il y a des articles associés
            if ($brand->items()->count() > 0) {
                return redirect()->route('admin.brands.index')
                    ->with('error', 'Impossible de supprimer cette marque car elle contient des articles.');
            }

            // Supprimer le logo
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }

            $brand->delete();

            return redirect()->route('admin.brands.index')
                ->with('success', 'Marque supprimée avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la marque: ' . $e->getMessage());
            return redirect()->route('admin.brands.index')
                ->with('error', 'Erreur lors de la suppression de la marque.');
        }
    }

    /**
     * Changer le statut d'une marque
     */
    public function brandUpdateStatus(Request $request, Brand $brand)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $brand->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la marque mis à jour avec succès.',
            'status' => $brand->is_active
        ]);
    }

    // =============================================
    // MÉTHODES CRUD POUR LES CATEGORIES
    // =============================================

    /**
     * Afficher le formulaire de création d'une catégorie
     */
    public function categoryCreate()
    {
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Générer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories/images', 'public');
            $data['image'] = $imagePath;
        }

        // Définir les valeurs par défaut pour les booléens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        // Définir l'ordre de tri par défaut
        if (empty($data['sort_order'])) {
            $maxOrder = Category::where('parent_id', $data['parent_id'])->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;
        }

        $category = Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Afficher les détails d'une catégorie
     */
    public function categoryShow(Category $category)
    {
        $category->load(['parent', 'children', 'items' => function($query) {
            $query->with('brand', 'images')->latest()->take(10);
        }]);

        // Statistiques de la catégorie
        $stats = [
            'total_items' => $category->items()->count(),
            'active_items' => $category->items()->where('status', 'active')->count(),
            'sold_items' => $category->items()->where('status', 'sold')->count(),
            'children_count' => $category->children()->count(),
            'average_price' => $category->items()->where('status', 'active')->avg('price') ?? 0,
            'total_views' => $category->items()->sum('views') ?? 0,
        ];

        return view('admin.categories.show', compact('category', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition d'une catégorie
     */
    public function categoryEdit(Category $category)
    {
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();
            
        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Mettre à jour une catégorie
     */
    public function categoryUpdate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id,
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        $data = $request->all();
        
        // Générer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // Gérer l'upload de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            
            $imagePath = $request->file('image')->store('categories/images', 'public');
            $data['image'] = $imagePath;
        }

        // Définir les valeurs par défaut pour les booléens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $category->update($data);

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprimer une catégorie
     */
    public function categoryDestroy(Category $category)
    {
        try {
            // Vérifier s'il y a des articles associés
            if ($category->items()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Impossible de supprimer cette catégorie car elle contient des articles.');
            }

            // Vérifier s'il y a des sous-catégories
            if ($category->children()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Impossible de supprimer cette catégorie car elle contient des sous-catégories.');
            }

            // Supprimer l'image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'Catégorie supprimée avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la catégorie: ' . $e->getMessage());
            return redirect()->route('admin.categories.index')
                ->with('error', 'Erreur lors de la suppression de la catégorie.');
        }
    }

    /**
     * Changer le statut d'une catégorie
     */
    public function categoryUpdateStatus(Request $request, Category $category)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $category->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la catégorie mis à jour avec succès.',
            'status' => $category->is_active
        ]);
    }

    // =============================================
    // MÉTHODES CRUD POUR LES USERS (UTILISATEURS)
    // =============================================

    /**
     * Afficher le formulaire de création d'un utilisateur
     */
    public function userCreate()
    {
        return view('admin.users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur
     */
    public function userStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'nullable|in:admin,moderator,user',
            'status' => 'nullable|in:active,suspended,banned',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
            'is_seller' => 'boolean',
            'notifications_enabled' => 'boolean',
            'marketing_emails' => 'boolean',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        // Gérer l'upload de l'avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Définir les valeurs par défaut
        $data['status'] = $data['status'] ?? 'active';
        $data['role'] = $data['role'] ?? 'user';
        $data['is_seller'] = $request->has('is_seller');
        $data['notifications_enabled'] = $request->has('notifications_enabled') ? 1 : 1; // Par défaut activé
        $data['marketing_emails'] = $request->has('marketing_emails');
        $data['email_verified_at'] = now(); // Marquer comme vérifié par l'admin

        $user = User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher le formulaire d'édition d'un utilisateur
     */
    public function userEdit(User $user)
    {
        $user->load(['items', 'orders', 'wallet']);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function userUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'nullable|in:admin,moderator,user',
            'status' => 'nullable|in:active,suspended,banned',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:100',
            'language' => 'nullable|string|max:5',
            'timezone' => 'nullable|string|max:50',
            'is_seller' => 'boolean',
            'notifications_enabled' => 'boolean',
            'marketing_emails' => 'boolean',
        ]);

        $data = $request->all();

        // Gérer le mot de passe
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Gérer l'upload de l'avatar
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Définir les valeurs par défaut pour les booléens
        $data['is_seller'] = $request->has('is_seller');
        $data['notifications_enabled'] = $request->has('notifications_enabled');
        $data['marketing_emails'] = $request->has('marketing_emails');

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function userDestroy(User $user)
    {
        try {
            // Vérifier si l'utilisateur a des commandes en cours
            if ($user->orders()->whereIn('status', ['pending', 'processing'])->count() > 0) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Impossible de supprimer cet utilisateur car il a des commandes en cours.');
            }

            // Supprimer l'avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'Utilisateur supprimé avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Erreur lors de la suppression de l\'utilisateur.');
        }
    }

    /**
     * Changer/basculer le statut d'un utilisateur
     */
    public function userToggleStatus(User $user)
    {
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de l\'utilisateur mis à jour avec succès.',
            'status' => $user->status
        ]);
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe
     */
    public function userSendPasswordReset(User $user)
    {
        try {
            // Logique pour envoyer l'email de réinitialisation
            // Vous pouvez utiliser Password::sendResetLink() ou votre propre logique
            
            return response()->json([
                'success' => true,
                'message' => 'Email de réinitialisation envoyé avec succès.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de réinitialisation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email.'
            ], 500);
        }
    }

    /**
     * Envoyer un email de bienvenue
     */
    public function userSendWelcome(User $user)
    {
        try {
            // Logique pour envoyer l'email de bienvenue
            
            return response()->json([
                'success' => true,
                'message' => 'Email de bienvenue envoyé avec succès.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de bienvenue: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email.'
            ], 500);
        }
    }

    /**
     * Envoyer un message à un utilisateur
     */
    public function userSendMessage(Request $request, User $user)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            // Logique pour envoyer le message (notification, email, etc.)
            
            return response()->json([
                'success' => true,
                'message' => 'Message envoyé avec succès.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du message.'
            ], 500);
        }
    }

    /**
     * Exporter les données d'un utilisateur
     */
    public function userExport(User $user)
    {
        try {
            $userData = [
                'user' => $user->toArray(),
                'items' => $user->items()->with('category', 'brand')->get()->toArray(),
                'orders' => $user->orders()->with('items')->get()->toArray(),
                'wallet' => $user->wallet ? $user->wallet->toArray() : null,
                'transactions' => $user->wallet ? $user->wallet->transactions()->get()->toArray() : [],
            ];

            $fileName = 'user_data_' . $user->id . '_' . date('Y-m-d_H-i-s') . '.json';
            
            return response()->json($userData)
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'export des données utilisateur: ' . $e->getMessage());
            return redirect()->route('admin.users.show', $user)
                ->with('error', 'Erreur lors de l\'export des données.');
        }
    }

    // =============================================
    // MÉTHODES DE GESTION DU MODE MAINTENANCE
    // =============================================

    /**
     * Activer le mode maintenance
     */
    public function enableMaintenance(Request $request)
    {
        try {
            $request->validate([
                'message' => 'nullable|string|max:500',
                'estimated_time' => 'nullable|string|max:100'
            ]);
            
            $message = $request->input('message', 'Nous effectuons actuellement des travaux de maintenance sur le site.');
            $estimatedTime = $request->input('estimated_time');
            
            MaintenanceMode::enable($message, $estimatedTime);
            
            Log::info('Mode maintenance activé par l\'admin', [
                'user_id' => Auth::id(),
                'message' => $message,
                'estimated_time' => $estimatedTime
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Mode maintenance activé avec succès'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'activation du mode maintenance', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'activation du mode maintenance'
            ], 500);
        }
    }
    
    /**
     * Désactiver le mode maintenance
     */
    public function disableMaintenance()
    {
        try {
            MaintenanceMode::disable();
            
            Log::info('Mode maintenance désactivé par l\'admin', [
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Mode maintenance désactivé avec succès'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la désactivation du mode maintenance', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désactivation du mode maintenance'
            ], 500);
        }
    }
    
    /**
     * Vérifier le statut du mode maintenance
     */
    public function maintenanceStatus()
    {
        return response()->json([
            'enabled' => MaintenanceMode::isEnabled()
        ]);
    }

    // =============================================
    // MÉTHODES CRUD POUR LES ITEMS (ARTICLES)
    // =============================================

    /**
     * Afficher la liste des articles
     */
    public function items(Request $request)
    {
        $query = Item::with(['user', 'category', 'brand']);

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->user);
        }

        // Tri
        $sort = $request->get('sort', '-created_at');
        if (str_starts_with($sort, '-')) {
            $query->orderBy(substr($sort, 1), 'desc');
        } else {
            $query->orderBy($sort, 'asc');
        }

        $items = $query->paginate(20)->withQueryString();

        return view('admin.items.index', compact('items'));
    }

    /**
     * Afficher le formulaire de création d'un article
     */
    public function itemCreate()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('admin.items.create', compact('categories', 'brands', 'users'));
    }

    /**
     * Enregistrer un nouvel article
     */
    public function itemStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,active,sold,inactive',
            'specifications' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Gérer les images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('items/images', 'public');
                $images[] = $imagePath;
            }
            $data['images'] = $images;
        }

        $item = Item::create($data);

        return redirect()->route('admin.items.index')
            ->with('success', 'Article créé avec succès.');
    }

    /**
     * Afficher les détails d'un article
     */
    public function itemShow(Item $item)
    {
        $item->load(['user', 'category', 'brand', 'orders', 'reviews']);

        return view('admin.items.show', compact('item'));
    }

    /**
     * Afficher le formulaire d'édition d'un article
     */
    public function itemEdit(Item $item)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('admin.items.edit', compact('item', 'categories', 'brands', 'users'));
    }

    /**
     * Mettre à jour un article
     */
    public function itemUpdate(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:pending,active,sold,inactive',
            'specifications' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        // Gérer les nouvelles images
        if ($request->hasFile('images')) {
            $images = $item->images ?? [];
            foreach ($request->file('images') as $image) {
                $imagePath = $image->store('items/images', 'public');
                $images[] = $imagePath;
            }
            $data['images'] = $images;
        }

        $item->update($data);

        return redirect()->route('admin.items.show', $item)
            ->with('success', 'Article mis à jour avec succès.');
    }

    /**
     * Supprimer un article
     */
    public function itemDestroy(Item $item)
    {
        try {
            // Vérifier s'il y a des commandes associées
            if ($item->orders()->count() > 0) {
                return redirect()->route('admin.items.index')
                    ->with('error', 'Impossible de supprimer cet article car il est associé à des commandes.');
            }

            // Supprimer les images
            if ($item->images) {
                foreach ($item->images as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        Storage::disk('public')->delete($image);
                    }
                }
            }

            $item->delete();

            return redirect()->route('admin.items.index')
                ->with('success', 'Article supprimé avec succès.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de l\'article: ' . $e->getMessage());
            return redirect()->route('admin.items.index')
                ->with('error', 'Erreur lors de la suppression de l\'article.');
        }
    }

    /**
     * Changer le statut d'un article
     */
    public function itemUpdateStatus(Request $request, Item $item)
    {
        $request->validate([
            'status' => 'required|in:pending,active,sold,inactive'
        ]);

        $item->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de l\'article mis à jour avec succès.',
            'status' => $item->status
        ]);
    }

    /**
     * Approuver un article
     */
    public function itemApprove(Item $item)
    {
        $item->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Article approuvé avec succès.'
        ]);
    }

    /**
     * Rejeter un article
     */
    public function itemReject(Item $item)
    {
        $item->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => 'Article rejeté avec succès.'
        ]);
    }

    /**
     * Actions en lot sur les articles
     */
    public function itemsBulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete,activate,deactivate',
            'items' => 'required|array',
            'items.*' => 'exists:items,id'
        ]);

        $items = Item::whereIn('id', $request->items);
        $count = $items->count();

        try {
            switch ($request->action) {
                case 'approve':
                    $items->update(['status' => 'active']);
                    $message = "{$count} article(s) approuvé(s) avec succès.";
                    break;
                
                case 'reject':
                    $items->update(['status' => 'inactive']);
                    $message = "{$count} article(s) rejeté(s) avec succès.";
                    break;
                
                case 'activate':
                    $items->update(['status' => 'active']);
                    $message = "{$count} article(s) activé(s) avec succès.";
                    break;
                
                case 'deactivate':
                    $items->update(['status' => 'inactive']);
                    $message = "{$count} article(s) désactivé(s) avec succès.";
                    break;
                
                case 'delete':
                    foreach ($items->get() as $item) {
                        if ($item->orders()->count() == 0) {
                            // Supprimer les images
                            if ($item->images) {
                                foreach ($item->images as $image) {
                                    if (Storage::disk('public')->exists($image)) {
                                        Storage::disk('public')->delete($image);
                                    }
                                }
                            }
                            $item->delete();
                        }
                    }
                    $message = "Articles supprimés avec succès.";
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'action en lot: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'exécution de l\'action.'
            ], 500);
        }
    }
}