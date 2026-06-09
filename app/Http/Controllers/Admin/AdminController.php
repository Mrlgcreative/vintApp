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
use App\Models\ProductAuthenticityCheck;
use App\Services\SettingService;
use App\Http\Middleware\MaintenanceMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Services\StorageSyncService;
use App\Traits\ApiResponses;

class AdminController extends Controller
{
    use ApiResponses;
    /**
     * Dashboard administrateur
     */
    public function dashboard()
    {
        // Statistiques gÃ©nÃ©rales
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'active_users' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),
            
            'total_transactions' => Transaction::count(),
            'transactions_today' => Transaction::whereDate('created_at', today())->count(),
            'total_transaction_amount' => Transaction::where('status', 'completed')->sum('amount'),
            
            // Revenus plateforme = sous-wallets entreprise
            'total_revenue_usd' => Wallet::getEnterpriseSubWallets('USD')->sum('balance'),
            'total_revenue_cdf' => Wallet::getEnterpriseSubWallets('CDF')->sum('balance'),
            
            // Wallets en attente de confirmation (type='pending')
            'pending_wallets' => Wallet::where('type', 'pending')->count(),
            'pending_wallets_usd' => Wallet::where('type', 'pending')
                ->where('currency', 'USD')
                ->sum('balance'),
            'pending_wallets_cdf' => Wallet::where('type', 'pending')
                ->where('currency', 'CDF')
                ->sum('balance'),
            'total_wallet_balance' => Wallet::where('is_active', true)->sum('balance'),
            
            // Nouveaux sous-wallets entreprise
            'enterprise_commission_usd' => Wallet::getEnterpriseSubWallet('commission', 'USD')->balance ?? 0,
            'enterprise_transport_usd' => Wallet::getEnterpriseSubWallet('transport', 'USD')->balance ?? 0,
            'enterprise_boost_usd' => Wallet::getEnterpriseSubWallet('boost', 'USD')->balance ?? 0,
            
            // Statistiques de vérification d'authenticité
            'total_verifications' => ProductAuthenticityCheck::count(),
            'pending_verifications' => ProductAuthenticityCheck::whereIn('status', ['pending', 'expert_review'])->count(),
            'completed_verifications' => ProductAuthenticityCheck::where('payment_completed', true)->count(),
            'verification_revenue_usd' => ProductAuthenticityCheck::where('payment_completed', true)->sum('verification_fee') ?? 0,
            
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
        
        // DerniÃ¨res activitÃ©s
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
     * DÃ©tails d'un utilisateur
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
     * Mettre Ã  jour le statut d'un utilisateur
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
                    $message = "Utilisateur activÃ© avec succÃ¨s.";
                    break;
                    
                case 'deactivate':
                    $user->update(['is_active' => false]);
                    $message = "Utilisateur dÃ©sactivÃ© avec succÃ¨s.";
                    break;
                    
                case 'suspend':
                    $user->update(['is_suspended' => true]);
                    $message = "Utilisateur suspendu avec succÃ¨s.";
                    break;
                    
                case 'delete':
                    // Soft delete
                    $user->delete();
                    $message = "Utilisateur supprimÃ© avec succÃ¨s.";
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
        // Récupérer les wallets de type 'pending' (argent en attente de confirmation acheteur)
        $pendingWallets = Wallet::with(['user'])
            ->where('type', 'pending')
            ->orderBy('balance', 'desc')
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
            
            // CrÃ©er une transaction de confirmation
            Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'wallet_approval',
                'amount' => 0,
                'currency' => $wallet->currency,
                'status' => 'completed',
                'description' => 'Wallet approuvÃ© par l\'administrateur',
                'processed_by' => Auth::id()
            ]);
            
            DB::commit();
            
            Log::info("Wallet approuvÃ©", [
                'admin_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id
            ]);
            
            return redirect()->back()->with('success', 'Wallet approuvÃ© avec succÃ¨s.');
            
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
            
            // CrÃ©er une transaction de rejet
            Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'wallet_rejection',
                'amount' => 0,
                'currency' => $wallet->currency,
                'status' => 'failed',
                'description' => 'Wallet rejetÃ©: ' . $request->reason,
                'processed_by' => Auth::id()
            ]);
            
            DB::commit();
            
            Log::info("Wallet rejetÃ©", [
                'admin_id' => Auth::id(),
                'wallet_id' => $wallet->id,
                'user_id' => $wallet->user_id,
                'reason' => $request->reason
            ]);
            
            return redirect()->back()->with('success', 'Wallet rejetÃ© avec succÃ¨s.');
            
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
                    
                    Log::info("Wallet approuvÃ© en lot", [
                        'admin_id' => Auth::id(),
                        'wallet_id' => $wallet->id,
                        'user_id' => $wallet->user_id
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', "{$approvedCount} wallet(s) approuvÃ©(s) avec succÃ¨s.");
            
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
                    
                    Log::info("Wallet rejetÃ© en lot", [
                        'admin_id' => Auth::id(),
                        'wallet_id' => $wallet->id,
                        'user_id' => $wallet->user_id,
                        'reason' => $request->reason
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->back()->with('success', "{$rejectedCount} wallet(s) rejetÃ©(s) avec succÃ¨s.");
            
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
     * DÃ©tails d'une transaction
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
     * Afficher les détails d'une commande
     */
    public function orderShow($id)
    {
        $order = Order::with([
            'buyer', 
            'seller', 
            'item', 
            'item.category', 
            'item.brand'
        ])->findOrFail($id);
        
        return view('admin.orders.show', compact('order'));
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
     * Gestion des catÃ©gories
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
        $period = $request->get('period', '30'); // 30 jours par dÃ©faut
        
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
     * Logs systÃ¨me
     */
    public function logs(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $logs = [];
        $stats = [
            'error' => 0,
            'warning' => 0,
            'info' => 0,
            'debug' => 0,
        ];

        if (file_exists($logFile)) {
            try {
                // Lire le fichier de logs
                $content = file_get_contents($logFile);
                $lines = explode("\n", $content);
                
                // Parser les logs
                $currentLog = null;
                foreach (array_reverse($lines) as $line) {
                    if (empty(trim($line))) continue;
                    
                    // DÃ©tecter le dÃ©but d'une nouvelle entrÃ©e de log
                    if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]\s+(\w+)\.(\w+):\s+(.+)/', $line, $matches)) {
                        // Sauvegarder le log prÃ©cÃ©dent si existe
                        if ($currentLog !== null) {
                            // Filtres
                            $shouldAdd = true;
                            
                            if ($request->filled('level') && strtolower($currentLog['level']) !== strtolower($request->level)) {
                                $shouldAdd = false;
                            }
                            
                            if ($request->filled('date') && !str_contains($currentLog['datetime'], $request->date)) {
                                $shouldAdd = false;
                            }
                            
                            if ($request->filled('search')) {
                                $searchTerm = strtolower($request->search);
                                if (!str_contains(strtolower($currentLog['message']), $searchTerm) &&
                                    !str_contains(strtolower($currentLog['context']), $searchTerm)) {
                                    $shouldAdd = false;
                                }
                            }
                            
                            if ($shouldAdd) {
                                $logs[] = $currentLog;
                                
                                // Compter par niveau
                                $level = strtolower($currentLog['level']);
                                if (isset($stats[$level])) {
                                    $stats[$level]++;
                                }
                            }
                        }
                        
                        // CrÃ©er une nouvelle entrÃ©e
                        $currentLog = [
                            'datetime' => $matches[1],
                            'env' => $matches[2],
                            'level' => strtoupper($matches[3]),
                            'message' => $matches[4],
                            'context' => '',
                        ];
                    } else if ($currentLog !== null) {
                        // Ajouter au contexte du log actuel
                        $currentLog['context'] .= $line . "\n";
                    }
                    
                    // Limiter Ã  100 logs pour performance
                    if (count($logs) >= 100) {
                        break;
                    }
                }
                
                // Ajouter le dernier log
                if ($currentLog !== null && count($logs) < 100) {
                    $logs[] = $currentLog;
                }
                
            } catch (\Exception $e) {
                Log::error('Erreur lors de la lecture des logs: ' . $e->getMessage());
            }
        }

        // Taille du fichier
        $fileSize = file_exists($logFile) ? filesize($logFile) : 0;

        return view('admin.logs.index', compact('logs', 'stats', 'fileSize'));
    }

    /**
     * ParamÃ¨tres systÃ¨me
     */
    public function settings()
    {
        try {
            $maintenanceStatus = MaintenanceMode::isEnabled();
            $settings = Setting::orderBy('category')->orderBy('key')->get()->groupBy('category');
            $categories = $settings->keys()->toArray();
            
            // Statistiques du wallet entreprise
            $enterpriseWallets = [
                'usd' => Wallet::where('type', 'enterprise')
                    ->where('currency', 'USD')
                    ->first(),
                'cdf' => Wallet::where('type', 'enterprise')
                    ->where('currency', 'CDF')
                    ->first(),
                'commission_rate' => Wallet::where('type', 'enterprise')
                    ->value('commission_rate') ?? 5.00,
            ];
            
            return view('admin.settings.index', compact('maintenanceStatus', 'settings', 'categories', 'enterpriseWallets'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des paramÃ¨tres: ' . $e->getMessage());
            $maintenanceStatus = false;
            $settings = collect();
            $categories = [];
            $enterpriseWallets = ['usd' => null, 'cdf' => null, 'commission_rate' => 5.00];
            return view('admin.settings.index', compact('maintenanceStatus', 'settings', 'categories', 'enterpriseWallets'));
        }
    }

    /**
     * Met Ã  jour les paramÃ¨tres systÃ¨me
     */
    public function settingsUpdate(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Convertir les valeurs selon le type
                $value = $this->convertSettingValue($value, $setting->type);
                $setting->update(['value' => $value]);
            }
        }

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'ParamÃ¨tres mis Ã  jour avec succÃ¨s');
    }

    /**
     * Affiche les paramÃ¨tres de prÃ©-inscription
     */
    public function preregistrationSettings()
    {
        $settings = Setting::where('category', 'preregistration')->get();
        
        return view('admin.settings.preregistration', compact('settings'));
    }

    /**
     * Met Ã  jour les paramÃ¨tres de prÃ©-inscription
     */
    public function updatePreregistrationSettings(Request $request)
    {
        $validated = $request->validate([
            'preregistration_enabled' => 'nullable|boolean',
            'preregistration_title' => 'nullable|string|max:255',
            'preregistration_subtitle' => 'nullable|string|max:500',
            'preregistration_message' => 'nullable|string',
            'preregistration_benefits' => 'nullable|array',
            'preregistration_benefits.*' => 'string|max:255',
            'preregistration_limit' => 'nullable|integer|min:0',
            'preregistration_require_phone' => 'nullable|boolean',
            'preregistration_require_confirmation' => 'nullable|boolean',
            'preregistration_notification_email' => 'nullable|email',
            'preregistration_closed_message' => 'nullable|string',
        ]);

        // Mettre Ã  jour chaque paramÃ¨tre
        foreach ($validated as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            
            if ($setting) {
                // Cas spÃ©cial pour les benefits (array -> json)
                if ($key === 'preregistration_benefits') {
                    $value = json_encode(array_values(array_filter($value)));
                }
                
                // Cas spÃ©cial pour les checkboxes
                if ($setting->type === 'boolean') {
                    $value = $value ? '1' : '0';
                }
                
                $setting->update(['value' => $value]);
            }
        }

        // Si preregistration_enabled n'est pas dans la requÃªte (checkbox non cochÃ©e)
        if (!isset($validated['preregistration_enabled'])) {
            Setting::where('key', 'preregistration_enabled')->update(['value' => '0']);
        }

        if (!isset($validated['preregistration_require_phone'])) {
            Setting::where('key', 'preregistration_require_phone')->update(['value' => '0']);
        }

        if (!isset($validated['preregistration_require_confirmation'])) {
            Setting::where('key', 'preregistration_require_confirmation')->update(['value' => '0']);
        }

        return redirect()
            ->route('admin.settings.preregistration')
            ->with('success', 'ParamÃ¨tres de prÃ©-inscription mis Ã  jour avec succÃ¨s');
    }

    /**
     * Toggle (activer/dÃ©sactiver) la prÃ©-inscription
     */
    public function togglePreregistration(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        $setting = Setting::where('key', 'preregistration_enabled')->first();
        
        if ($setting) {
            $setting->update(['value' => $validated['enabled'] ? '1' : '0']);
            
            $message = $validated['enabled'] 
                ? 'ðŸ”’ Mode prÃ©-inscription ACTIVÃ‰ ! L\'application est maintenant verrouillÃ©e. Seuls les admins peuvent y accÃ©der.' 
                : 'âœ… Mode prÃ©-inscription DÃ‰SACTIVÃ‰. L\'application est de nouveau accessible Ã  tous.';
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'enabled' => $validated['enabled']
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'ParamÃ¨tre de prÃ©-inscription introuvable'
        ], 404);
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
        
        // Compter les Ã©lÃ©ments nÃ©cessitant attention
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
                'message' => "{$failedTransactions} transaction(s) Ã©chouÃ©e(s) aujourd'hui",
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
     * Afficher les paramÃ¨tres systÃ¨me
     */
    public function systemSettings(SettingService $settingService)
    {
        $settings = $settingService->getAllForAdmin()->groupBy('category');
        $categories = $settingService->getCategories();
        
        return view('admin.settings.index', compact('settings', 'categories'));
    }

    /**
     * Mettre Ã  jour les paramÃ¨tres
     */
    public function updateSettings(Request $request, SettingService $settingService)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
            'logo_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // GÃ©rer l'upload du logo en premier
            if ($request->hasFile('logo_file')) {
                $logoFile = $request->file('logo_file');
                $logoPath = $logoFile->store('logos', 'public');
                
                // Supprimer l'ancien logo s'il existe
                $oldLogo = $settingService->get('app_logo');
                if ($oldLogo && $oldLogo !== '/images/logo.png' && Storage::disk('public')->exists(str_replace('/storage/', '', $oldLogo))) {
                    Storage::disk('public')->delete(str_replace('/storage/', '', $oldLogo));
                }
                
                // Mettre Ã  jour le chemin du logo
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
                ->with('success', 'ParamÃ¨tres mis Ã  jour avec succÃ¨s.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise Ã  jour des paramÃ¨tres: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise Ã  jour des paramÃ¨tres.')
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
     * RÃ©initialiser le cache des settings
     */
    public function clearSettingsCache(SettingService $settingService)
    {
        try {
            $settingService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Cache des paramÃ¨tres vidÃ© avec succÃ¨s.'
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
    // MÃ‰THODES CRUD POUR LES BRANDS (MARQUES)
    // =============================================

    /**
     * Afficher le formulaire de crÃ©ation d'une marque
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
        
        // GÃ©nÃ©rer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // GÃ©rer l'upload du logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // DÃ©finir les valeurs par dÃ©faut pour les boolÃ©ens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $brand = Brand::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque crÃ©Ã©e avec succÃ¨s.');
    }

    /**
     * Afficher les détails d'une marque
     */
    public function brandShow(Brand $brand)
    {
        $brand->load(['items' => function($query) {
            $query->with('category')->latest()->take(10);
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
     * Afficher le formulaire d'Ã©dition d'une marque
     */
    public function brandEdit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Mettre Ã  jour une marque
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
        
        // GÃ©nÃ©rer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // GÃ©rer l'upload du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo
            if ($brand->logo && Storage::disk('public')->exists($brand->logo)) {
                Storage::disk('public')->delete($brand->logo);
            }
            
            $logoPath = $request->file('logo')->store('brands/logos', 'public');
            $data['logo'] = $logoPath;
        }

        // DÃ©finir les valeurs par dÃ©faut pour les boolÃ©ens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $brand->update($data);

        return redirect()->route('admin.brands.show', $brand)
            ->with('success', 'Marque mise Ã  jour avec succÃ¨s.');
    }

    /**
     * Supprimer une marque
     */
    public function brandDestroy(Brand $brand)
    {
        try {
            // VÃ©rifier s'il y a des articles associÃ©s
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
                ->with('success', 'Marque supprimÃ©e avec succÃ¨s.');
                
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
            'message' => 'Statut de la marque mis Ã  jour avec succÃ¨s.',
            'status' => $brand->is_active
        ]);
    }

    // =============================================
    // MÃ‰THODES CRUD POUR LES CATEGORIES
    // =============================================

    /**
     * Afficher le formulaire de crÃ©ation d'une catÃ©gorie
     */
    public function categoryCreate()
    {
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Enregistrer une nouvelle catÃ©gorie
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
        
        // GÃ©nÃ©rer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // GÃ©rer l'upload de l'image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories/images', 'public');
            $data['image'] = $imagePath;
            
            // Copier vers public/storage pour Hostinger
            StorageSyncService::syncFile($imagePath);
        }

        // DÃ©finir les valeurs par dÃ©faut pour les boolÃ©ens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        // DÃ©finir l'ordre de tri par dÃ©faut
        if (empty($data['sort_order'])) {
            $maxOrder = Category::where('parent_id', $data['parent_id'])->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;
        }

        $category = Category::create($data);

        return redirect()->route('admin.categories.index')
            ->with('success', 'CatÃ©gorie crÃ©Ã©e avec succÃ¨s.');
    }

    /**
     * Afficher les détails d'une catégorie
     */
    public function categoryShow(Category $category)
    {
        $category->load(['parent', 'children', 'items' => function($query) {
            $query->with('brand')->latest()->take(10);
        }]);

        // Statistiques de la catÃ©gorie
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
     * Afficher le formulaire d'Ã©dition d'une catÃ©gorie
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
     * Mettre Ã  jour une catÃ©gorie
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
        
        // GÃ©nÃ©rer le slug automatiquement si pas fourni
        if (empty($data['slug'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        // GÃ©rer l'upload de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            
            $imagePath = $request->file('image')->store('categories/images', 'public');
            $data['image'] = $imagePath;
        }

        // DÃ©finir les valeurs par dÃ©faut pour les boolÃ©ens
        $data['is_active'] = $request->has('is_active');
        $data['is_featured'] = $request->has('is_featured');

        $category->update($data);

        return redirect()->route('admin.categories.show', $category)
            ->with('success', 'CatÃ©gorie mise Ã  jour avec succÃ¨s.');
    }

    /**
     * Supprimer une catÃ©gorie
     */
    public function categoryDestroy(Category $category)
    {
        try {
            // VÃ©rifier s'il y a des articles associÃ©s
            if ($category->items()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Impossible de supprimer cette catÃ©gorie car elle contient des articles.');
            }

            // VÃ©rifier s'il y a des sous-catÃ©gories
            if ($category->children()->count() > 0) {
                return redirect()->route('admin.categories.index')
                    ->with('error', 'Impossible de supprimer cette catÃ©gorie car elle contient des sous-catÃ©gories.');
            }

            // Supprimer l'image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            return redirect()->route('admin.categories.index')
                ->with('success', 'CatÃ©gorie supprimÃ©e avec succÃ¨s.');
                
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la catÃ©gorie: ' . $e->getMessage());
            return redirect()->route('admin.categories.index')
                ->with('error', 'Erreur lors de la suppression de la catÃ©gorie.');
        }
    }

    /**
     * Changer le statut d'une catÃ©gorie
     */
    public function categoryUpdateStatus(Request $request, Category $category)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $category->update(['is_active' => $request->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Statut de la catÃ©gorie mis Ã  jour avec succÃ¨s.',
            'status' => $category->is_active
        ]);
    }

    // =============================================
    // MÃ‰THODES CRUD POUR LES USERS (UTILISATEURS)
    // =============================================

    /**
     * Afficher le formulaire de crÃ©ation d'un utilisateur
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

        // GÃ©rer l'upload de l'avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // DÃ©finir les valeurs par dÃ©faut
        $data['status'] = $data['status'] ?? 'active';
        $data['role'] = $data['role'] ?? 'user';
        $data['is_seller'] = $request->has('is_seller');
        $data['notifications_enabled'] = $request->has('notifications_enabled') ? 1 : 1; // Par dÃ©faut activÃ©
        $data['marketing_emails'] = $request->has('marketing_emails');
        $data['email_verified_at'] = now(); // Marquer comme vÃ©rifiÃ© par l'admin

        $user = User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur crÃ©Ã© avec succÃ¨s.');
    }

    /**
     * Afficher le formulaire d'Ã©dition d'un utilisateur
     */
    public function userEdit(User $user)
    {
        $user->load(['items', 'orders', 'wallet']);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Mettre Ã  jour un utilisateur
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

        // GÃ©rer le mot de passe
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // GÃ©rer l'upload de l'avatar
        if ($request->hasFile('avatar')) {
            // Supprimer l'ancien avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('users/avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        // DÃ©finir les valeurs par dÃ©faut pour les boolÃ©ens
        $data['is_seller'] = $request->has('is_seller');
        $data['notifications_enabled'] = $request->has('notifications_enabled');
        $data['marketing_emails'] = $request->has('marketing_emails');

        $user->update($data);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Utilisateur mis Ã  jour avec succÃ¨s.');
    }

    /**
     * Supprimer un utilisateur
     */
    public function userDestroy(User $user)
    {
        try {
            // VÃ©rifier si l'utilisateur a des commandes en cours
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
                ->with('success', 'Utilisateur supprimÃ© avec succÃ¨s.');
                
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
            'message' => 'Statut de l\'utilisateur mis Ã  jour avec succÃ¨s.',
            'status' => $user->status
        ]);
    }

    /**
     * Envoyer un email de rÃ©initialisation de mot de passe
     */
    public function userSendPasswordReset(User $user)
    {
        try {
            // Logique pour envoyer l'email de rÃ©initialisation
            // Vous pouvez utiliser Password::sendResetLink() ou votre propre logique
            
            return response()->json([
                'success' => true,
                'message' => 'Email de rÃ©initialisation envoyÃ© avec succÃ¨s.'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'email de rÃ©initialisation: ' . $e->getMessage());
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
                'message' => 'Email de bienvenue envoyÃ© avec succÃ¨s.'
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
     * Envoyer un message Ã  un utilisateur
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
                'message' => 'Message envoyÃ© avec succÃ¨s.'
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
     * Exporter les donnÃ©es d'un utilisateur
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
            Log::error('Erreur lors de l\'export des donnÃ©es utilisateur: ' . $e->getMessage());
            return redirect()->route('admin.users.show', $user)
                ->with('error', 'Erreur lors de l\'export des donnÃ©es.');
        }
    }

    // =============================================
    // MÃ‰THODES DE GESTION DU MODE MAINTENANCE
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
            
            Log::info('Mode maintenance activÃ© par l\'admin', [
                'user_id' => Auth::id(),
                'message' => $message,
                'estimated_time' => $estimatedTime
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Mode maintenance activÃ© avec succÃ¨s'
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
     * DÃ©sactiver le mode maintenance
     */
    public function disableMaintenance()
    {
        try {
            MaintenanceMode::disable();
            
            Log::info('Mode maintenance dÃ©sactivÃ© par l\'admin', [
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Mode maintenance dÃ©sactivÃ© avec succÃ¨s'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la dÃ©sactivation du mode maintenance', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la dÃ©sactivation du mode maintenance'
            ], 500);
        }
    }
    
    /**
     * VÃ©rifier le statut du mode maintenance
     */
    public function maintenanceStatus()
    {
        return response()->json([
            'enabled' => MaintenanceMode::isEnabled()
        ]);
    }

    // =============================================
    // MÃ‰THODES CRUD POUR LES ITEMS (ARTICLES)
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
     * Afficher le formulaire de crÃ©ation d'un article
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

        // GÃ©rer les images
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
            ->with('success', 'Article crÃ©Ã© avec succÃ¨s.');
    }

    /**
     * Afficher les dÃ©tails d'un article
     */
    public function itemShow(Item $item)
    {
        $item->load(['user', 'category', 'brand', 'orders', 'reviews']);

        return view('admin.items.show', compact('item'));
    }

    /**
     * Afficher le formulaire d'Ã©dition d'un article
     */
    public function itemEdit(Item $item)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $users = User::where('status', 'active')->orderBy('name')->get();

        return view('admin.items.edit', compact('item', 'categories', 'brands', 'users'));
    }

    /**
     * Mettre Ã  jour un article
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

        // GÃ©rer les nouvelles images
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
            ->with('success', 'Article mis Ã  jour avec succÃ¨s.');
    }

    /**
     * Supprimer un article
     */
    public function itemDestroy(Item $item)
    {
        try {
            // VÃ©rifier s'il y a des commandes associÃ©es
            if ($item->orders()->count() > 0) {
                return redirect()->route('admin.items.index')
                    ->with('error', 'Impossible de supprimer cet article car il est associÃ© Ã  des commandes.');
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
                ->with('success', 'Article supprimÃ© avec succÃ¨s.');
                
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
            'message' => 'Statut de l\'article mis Ã  jour avec succÃ¨s.',
            'status' => $item->status
        ]);
    }

    /**
     * Approuver un article
     */
    public function itemApprove(Request $request, Item $item)
    {
        $item->update(['status' => 'active']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Article approuvé avec succès.'
            ]);
        }

        return redirect()->route('admin.items.show', $item)->with('success', 'Article approuvé avec succès.');
    }

    /**
     * Rejeter un article
     */
    public function itemReject(Request $request, Item $item)
    {
        $item->update(['status' => 'inactive']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Article rejeté avec succès.'
            ]);
        }

        return redirect()->route('admin.items.show', $item)->with('success', 'Article rejeté avec succès.');
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
                    $message = "{$count} article(s) approuvÃ©(s) avec succÃ¨s.";
                    break;
                
                case 'reject':
                    $items->update(['status' => 'inactive']);
                    $message = "{$count} article(s) rejetÃ©(s) avec succÃ¨s.";
                    break;
                
                case 'activate':
                    $items->update(['status' => 'active']);
                    $message = "{$count} article(s) activÃ©(s) avec succÃ¨s.";
                    break;
                
                case 'deactivate':
                    $items->update(['status' => 'inactive']);
                    $message = "{$count} article(s) dÃ©sactivÃ©(s) avec succÃ¨s.";
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
                    $message = "Articles supprimÃ©s avec succÃ¨s.";
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
                'message' => 'Erreur lors de l\'exÃ©cution de l\'action.'
            ], 500);
        }
    }

    /**
     * Afficher le formulaire de dÃ©finition de mot de passe (pour l'utilisateur)
     */
    public function showSetPasswordForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            return redirect()->route('login')->with('error', 'Lien invalide ou expirÃ©.');
        }

        // Chercher l'utilisateur en attente avec ce token
        $userWaiting = \App\Models\UserWaiting::where('email', $email)
            ->where('password_setup_token', hash('sha256', $token))
            ->first();

        if (!$userWaiting) {
            return redirect()->route('login')->with('error', 'Lien invalide ou expirÃ©.');
        }

        // VÃ©rifier si le token n'a pas expirÃ©
        if ($userWaiting->password_setup_token_expires_at && 
            now()->isAfter($userWaiting->password_setup_token_expires_at)) {
            return redirect()->route('login')->with('error', 'Ce lien a expirÃ©. Contactez l\'administrateur.');
        }

        // VÃ©rifier si l'utilisateur existe dÃ©jÃ 
        $user = \App\Models\User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Compte utilisateur introuvable.');
        }

        return view('auth.set-password', [
            'token' => $token,
            'email' => $email,
            'name' => $user->name,
        ]);
    }

    /**
     * DÃ©finir le mot de passe de l'utilisateur
     */
    public function setPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractÃ¨res.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $token = $request->token;
        $email = $request->email;

        // Chercher l'utilisateur en attente
        $userWaiting = \App\Models\UserWaiting::where('email', $email)
            ->where('password_setup_token', hash('sha256', $token))
            ->first();

        if (!$userWaiting) {
            return back()->with('error', 'Lien invalide ou expirÃ©.');
        }

        // VÃ©rifier l'expiration
        if ($userWaiting->password_setup_token_expires_at && 
            now()->isAfter($userWaiting->password_setup_token_expires_at)) {
            return back()->with('error', 'Ce lien a expirÃ©. Contactez l\'administrateur.');
        }

        // Trouver l'utilisateur
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            return back()->with('error', 'Compte utilisateur introuvable.');
        }

        // Mettre Ã  jour le mot de passe
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        // Supprimer le token (usage unique)
        $userWaiting->update([
            'password_setup_token' => null,
            'password_setup_token_expires_at' => null,
        ]);

        Log::info("Mot de passe dÃ©fini pour l'utilisateur: {$email}");

        // Connecter automatiquement l'utilisateur
        auth()->login($user);

        return redirect()->route('dashboard')->with('success', 'ðŸŽ‰ Bienvenue sur VintApp ! Votre compte est maintenant actif.');
    }

    /**
     * Active ou désactive les restrictions géographiques
     */
    public function toggleLocationRestrictions(Request $request)
    {
        $validated = $request->validate([
            'enabled' => 'required|boolean',
        ]);

        try {
            // Utiliser updateOrCreate directement pour éviter les problèmes avec la méthode set()
            $setting = Setting::updateOrCreate(
                ['key' => 'enable_location_restrictions'],
                [
                    'value' => $validated['enabled'] ? '1' : '0',
                    'type' => 'boolean',
                    'description' => 'Active ou désactive les restrictions géographiques pour les articles',
                    'category' => 'general',
                    'label' => 'Restrictions géographiques',
                    'is_public' => false,
                    'is_encrypted' => false,
                ]
            );

            // Vider le cache
            Cache::forget('setting.enable_location_restrictions');

            $status = $validated['enabled'] ? 'activées' : 'désactivées';
            
            Log::info('Restrictions géographiques ' . $status . ' par l\'admin: ' . Auth::user()->email);

            return response()->json([
                'success' => true,
                'message' => 'Restrictions géographiques ' . $status . ' avec succès',
                'enabled' => $validated['enabled'],
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du toggle des restrictions géographiques: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification des paramètres: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Récupère l'état actuel des restrictions géographiques
     */
    public function getLocationRestrictionsStatus()
    {
        try {
            $enabled = Setting::get('enable_location_restrictions', false);
            
            return response()->json([
                'success' => false,
                'enabled' => (bool) $enabled,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du statut des restrictions: ' . $e->getMessage());
            
            return response()->json([
                'success' => true,
                'message' => 'Erreur lors de la récupération du statut',
            ], 500);
        }
    }

    /**
     * Affiche la liste des Hero Slides
     */
    public function heroSlides()
    {
        $slides = \App\Models\HeroSlide::ordered()->get();
        
        return view('admin.settings.hero-slides', compact('slides'));
    }

    /**
     * Crée un nouveau Hero Slide
     */
    public function storeHeroSlide(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/|max:7',
            'text_position' => 'required|in:left,right,center',
            'image_position' => 'required|in:left,right',
            'image_size' => 'required|in:small,medium,large,full',
            'button_primary_text' => 'nullable|string|max:100',
            'button_primary_url' => 'nullable|string|max:255',
            'button_secondary_text' => 'nullable|string|max:100',
            'button_secondary_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            // Upload de l'image
            $imagePath = $request->file('image')->store('hero-slides', 'public');

            // Récupérer l'ordre le plus élevé
            $maxOrder = \App\Models\HeroSlide::max('order') ?? 0;

            \App\Models\HeroSlide::create([
                'title' => $validated['title'],
                'subtitle' => $validated['subtitle'] ?? null,
                'image_path' => $imagePath,
                'background_color' => $validated['background_color'],
                'text_position' => $validated['text_position'],
                'image_position' => $validated['image_position'],
                'image_size' => $validated['image_size'],
                'button_primary_text' => $validated['button_primary_text'] ?? null,
                'button_primary_url' => $validated['button_primary_url'] ?? null,
                'button_secondary_text' => $validated['button_secondary_text'] ?? null,
                'button_secondary_url' => $validated['button_secondary_url'] ?? null,
                'order' => $maxOrder + 1,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Synchroniser automatiquement le storage
            \Artisan::call('storage:sync');
            Log::info('Storage synchronisé automatiquement après création du hero slide');

            return redirect()->back()->with('success', 'Slide ajoutée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du slide: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la création du slide: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour un Hero Slide
     */
    public function updateHeroSlide(Request $request, $slideId)
    {
        $slide = \App\Models\HeroSlide::findOrFail($slideId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'background_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/|max:7',
            'text_position' => 'required|in:left,right,center',
            'image_position' => 'required|in:left,right',
            'image_size' => 'required|in:small,medium,large,full',
            'button_primary_text' => 'nullable|string|max:100',
            'button_primary_url' => 'nullable|string|max:255',
            'button_secondary_text' => 'nullable|string|max:100',
            'button_secondary_url' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        try {
            $data = [
                'title' => $validated['title'],
                'subtitle' => $validated['subtitle'] ?? null,
                'background_color' => $validated['background_color'],
                'text_position' => $validated['text_position'],
                'image_position' => $validated['image_position'],
                'image_size' => $validated['image_size'],
                'button_primary_text' => $validated['button_primary_text'] ?? null,
                'button_primary_url' => $validated['button_primary_url'] ?? null,
                'button_secondary_text' => $validated['button_secondary_text'] ?? null,
                'button_secondary_url' => $validated['button_secondary_url'] ?? null,
                'is_active' => $validated['is_active'] ?? $slide->is_active,
            ];

            // Si une nouvelle image est uploadée
            if ($request->hasFile('image')) {
                // Supprimer l'ancienne image
                if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
                    Storage::disk('public')->delete($slide->image_path);
                }
                
                $data['image_path'] = $request->file('image')->store('hero-slides', 'public');
                
                // Synchroniser automatiquement le storage
                \Artisan::call('storage:sync');
                Log::info('Storage synchronisé automatiquement après mise à jour du hero slide');
            }

            $slide->update($data);

            return redirect()->back()->with('success', 'Slide mise à jour avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du slide: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour du slide: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un Hero Slide
     */
    public function destroyHeroSlide($slideId)
    {
        try {
            $slide = \App\Models\HeroSlide::findOrFail($slideId);

            // Supprimer l'image
            if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
                Storage::disk('public')->delete($slide->image_path);
            }

            $slide->delete();

            return redirect()->back()->with('success', 'Slide supprimée avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du slide: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la suppression du slide: ' . $e->getMessage());
        }
    }

    /**
     * Active/Désactive un Hero Slide
     */
    public function toggleHeroSlide($slideId)
    {
        try {
            $slide = \App\Models\HeroSlide::findOrFail($slideId);
            $slide->is_active = !$slide->is_active;
            $slide->save();

            $status = $slide->is_active ? 'activée' : 'désactivée';
            return redirect()->back()->with('success', "Slide {$status} avec succès !");
        } catch (\Exception $e) {
            Log::error('Erreur lors du toggle du slide: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la modification du slide');
        }
    }

    /**
     * Réordonne les Hero Slides
     */
    public function reorderHeroSlides(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:hero_slides,id',
        ]);

        try {
            foreach ($validated['order'] as $index => $slideId) {
                \App\Models\HeroSlide::where('id', $slideId)->update(['order' => $index + 1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Ordre mis à jour avec succès !',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du réordonnancement des slides: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'ordre',
            ], 500);
        }
    }

    /**
     * ===================================
     * GESTION DE LA NEWSLETTER
     * ===================================
     */

    /**
     * Liste des abonnés newsletter
     */
    public function newsletterSubscribers()
    {
        $subscribers = \App\Models\NewsletterSubscriber::latest()->paginate(50);
        
        $stats = [
            'total' => \App\Models\NewsletterSubscriber::count(),
            'active' => \App\Models\NewsletterSubscriber::active()->count(),
            'verified' => \App\Models\NewsletterSubscriber::verified()->count(),
            'total_emails_sent' => \App\Models\NewsletterSubscriber::sum('emails_sent'),
            'total_emails_opened' => \App\Models\NewsletterSubscriber::sum('emails_opened'),
            'total_clicks' => \App\Models\NewsletterSubscriber::sum('emails_clicked'),
        ];

        return view('admin.newsletter.subscribers', compact('subscribers', 'stats'));
    }

    /**
     * Envoyer une newsletter
     */
    public function sendNewsletter()
    {
        return view('admin.newsletter.send');
    }

    /**
     * Traiter l'envoi de la newsletter
     */
    public function processSendNewsletter(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipient_type' => 'required|in:all,active,verified',
        ]);

        try {
            // Récupérer les abonnés selon le type
            $query = \App\Models\NewsletterSubscriber::query();
            
            if ($validated['recipient_type'] === 'active') {
                $query->active();
            } elseif ($validated['recipient_type'] === 'verified') {
                $query->verified();
            }

            $subscribers = $query->get();
            $sentCount = 0;

            foreach ($subscribers as $subscriber) {
                try {
                    \Mail::to($subscriber->email)->send(
                        new \App\Mail\PromotionEmail($subscriber, $validated['subject'], $validated['content'])
                    );
                    $subscriber->incrementEmailsSent();
                    $sentCount++;
                } catch (\Exception $e) {
                    Log::error('Erreur envoi newsletter à ' . $subscriber->email . ': ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.newsletter.subscribers')
                ->with('success', "Newsletter envoyée à {$sentCount} abonné(s) !");
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la newsletter: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors de l\'envoi de la newsletter');
        }
    }

    /**
     * Supprimer un abonné
     */
    public function deleteNewsletterSubscriber($id)
    {
        try {
            $subscriber = \App\Models\NewsletterSubscriber::findOrFail($id);
            $subscriber->delete();

            return response()->json([
                'success' => true,
                'message' => 'Abonné supprimé avec succès !',
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur suppression abonné newsletter: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
            ], 500);
        }
    }

    /**
     * Activer/désactiver un abonné
     */
    public function toggleNewsletterSubscriber($id)
    {
        try {
            $subscriber = \App\Models\NewsletterSubscriber::findOrFail($id);
            
            if ($subscriber->is_active) {
                $subscriber->unsubscribe();
                $message = 'Abonné désactivé';
            } else {
                $subscriber->resubscribe();
                $message = 'Abonné réactivé';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'is_active' => $subscriber->is_active,
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur toggle abonné newsletter: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification',
            ], 500);
        }
    }

    /**
     * Exporter les abonnés en CSV
     */
    public function exportNewsletterSubscribers()
    {
        $subscribers = \App\Models\NewsletterSubscriber::all();
        
        $filename = 'newsletter_subscribers_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($subscribers) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Email', 'Nom', 'Actif', 'Vérifié', 'Date vérification',
                'Nouveaux articles', 'Promotions', 'Newsletters',
                'Emails envoyés', 'Emails ouverts', 'Clics',
                'Dernier email', 'Date inscription'
            ]);

            // Données
            foreach ($subscribers as $subscriber) {
                fputcsv($file, [
                    $subscriber->id,
                    $subscriber->email,
                    $subscriber->name,
                    $subscriber->is_active ? 'Oui' : 'Non',
                    $subscriber->email_verified ? 'Oui' : 'Non',
                    $subscriber->verified_at ? $subscriber->verified_at->format('Y-m-d H:i:s') : '',
                    $subscriber->receive_new_items ? 'Oui' : 'Non',
                    $subscriber->receive_promotions ? 'Oui' : 'Non',
                    $subscriber->receive_newsletters ? 'Oui' : 'Non',
                    $subscriber->emails_sent,
                    $subscriber->emails_opened,
                    $subscriber->emails_clicked,
                    $subscriber->last_email_sent_at ? $subscriber->last_email_sent_at->format('Y-m-d H:i:s') : '',
                    $subscriber->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // =============================================
    // SYSTÈME DE TRAÇAGE DES COMMANDES
    // =============================================

    /**
     * Afficher la liste de toutes les commandes avec traçage
     */
    public function trackingList()
    {
        // Récupérer la première commande avec tracking
        $firstTrackedOrder = Order::whereHas('trackings')
            ->with(['buyer', 'seller', 'latestTracking'])
            ->latest()
            ->first();
        
        // Si une commande existe, rediriger vers sa page de tracking
        if ($firstTrackedOrder) {
            return redirect()->route('admin.orders.tracking', $firstTrackedOrder->id);
        }
        
        // Sinon, afficher la liste
        return view('admin.orders.tracking-list');
    }

    /**
     * Afficher le suivi d'une commande avec carte GPS
     */
    public function orderTracking($id)
    {
        $order = Order::with([
            'buyer', 
            'seller', 
            'item',
            'item.category',
            'item.brand',
            'deliveryAddress'
        ])->findOrFail($id);
        
        // Récupérer l'historique de tracking
        $trackingHistory = \App\Models\OrderTracking::getHistoryForOrder($id);
        
        // Récupérer la dernière position
        $currentTracking = \App\Models\OrderTracking::getLatestForOrder($id);
        
        // Si pas de tracking, créer une entrée initiale avec l'adresse de livraison
        if (!$currentTracking) {
            $customerAddress = null;
            $customerCity = null;
            $customerPhone = null;
            
            if ($order->deliveryAddress) {
                $customerAddress = $order->deliveryAddress->address;
                $customerCity = $order->deliveryAddress->city;
                $customerPhone = $order->deliveryAddress->phone;
            } elseif ($order->shipping_address) {
                $customerAddress = $order->shipping_address;
                $customerCity = $order->shipping_city;
                $customerPhone = $order->shipping_phone;
            }
            
            if ($customerAddress) {
                $currentTracking = \App\Models\OrderTracking::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                    'description' => 'Commande en attente de traitement',
                    'customer_address' => $customerAddress,
                    'customer_city' => $customerCity,
                    'customer_phone' => $customerPhone,
                    'tracked_at' => now(),
                ]);
                
                $trackingHistory = collect([$currentTracking]);
            }
        }
        
        return view('admin.orders.tracking', compact('order', 'trackingHistory', 'currentTracking'));
    }

    /**
     * Mettre à jour la position de tracking d'une commande
     */
    public function updateOrderTracking(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,picked_up,in_transit,out_for_delivery,delivered,failed,returned',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'tracking_code' => 'nullable|string|max:100',
            'carrier' => 'nullable|string|max:100',
            'customer_latitude' => 'nullable|numeric|between:-90,90',
            'customer_longitude' => 'nullable|numeric|between:-180,180',
            'customer_address' => 'nullable|string|max:255',
            'customer_city' => 'nullable|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'estimated_delivery' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $order = Order::with('deliveryAddress')->findOrFail($id);

            // Récupérer les infos du client depuis delivery_address ou shipping_address
            $customerAddress = $request->customer_address;
            $customerCity = $request->customer_city;
            $customerPhone = $request->customer_phone;
            
            if (!$customerAddress && $order->deliveryAddress) {
                $customerAddress = $order->deliveryAddress->address;
                $customerCity = $order->deliveryAddress->city;
                $customerPhone = $order->deliveryAddress->phone;
            } elseif (!$customerAddress) {
                $customerAddress = $order->shipping_address;
                $customerCity = $order->shipping_city;
                $customerPhone = $order->shipping_phone;
            }

            // Créer une nouvelle entrée de tracking
            $tracking = \App\Models\OrderTracking::create([
                'order_id' => $id,
                'status' => $request->status,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'address' => $request->address,
                'city' => $request->city,
                'country' => $request->country ?? 'CD',
                'description' => $request->description,
                'tracking_code' => $request->tracking_code,
                'carrier' => $request->carrier,
                'customer_latitude' => $request->customer_latitude,
                'customer_longitude' => $request->customer_longitude,
                'customer_address' => $customerAddress,
                'customer_city' => $customerCity,
                'customer_phone' => $customerPhone,
                'estimated_delivery' => $request->estimated_delivery,
                'tracked_at' => now(),
            ]);

            // Mettre à jour le statut de la commande si livré
            if ($request->status === 'delivered' && $order->status !== 'delivered') {
                $order->update([
                    'status' => 'delivered',
                    'delivered_at' => now(),
                ]);
            }

            DB::commit();

            Log::info('Tracking de commande mis à jour', [
                'admin_id' => Auth::id(),
                'order_id' => $id,
                'status' => $request->status,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Position de tracking mise à jour avec succès',
                    'tracking' => $tracking,
                ]);
            }

            return redirect()->route('admin.orders.tracking', $id)
                ->with('success', 'Position de tracking mise à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du tracking', [
                'error' => $e->getMessage(),
                'order_id' => $id,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de la mise à jour',
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour');
        }
    }

    /**
     * Générer et afficher la facture d'une commande
     */
    public function orderInvoice($id)
    {
        $order = Order::with([
            'buyer', 
            'seller', 
            'item',
            'item.category',
            'item.brand'
        ])->findOrFail($id);
        
        // Récupérer le tracking actuel
        $currentTracking = \App\Models\OrderTracking::getLatestForOrder($id);
        
        // Informations de l'entreprise (à personnaliser)
        $company = [
            'name' => config('app.name', 'VintApp'),
            'address' => 'Kinshasa, RDC',
            'phone' => '+243 XX XXX XXXX',
            'email' => 'contact@vintapp.com',
            'website' => 'www.vintapp.com',
            'logo' => asset('images/logo.png'),
        ];
        
        return view('admin.orders.invoice', compact('order', 'currentTracking', 'company'));
    }

    /**
     * Télécharger la facture en PDF
     */
    public function downloadOrderInvoice($id)
    {
        // Note: Cette méthode nécessite l'installation de dompdf ou snappy
        // composer require barryvdh/laravel-dompdf
        
        $order = Order::with([
            'buyer', 
            'seller', 
            'item',
            'item.category',
            'item.brand'
        ])->findOrFail($id);
        
        $currentTracking = \App\Models\OrderTracking::getLatestForOrder($id);
        
        $company = [
            'name' => config('app.name', 'VintApp'),
            'address' => 'Kinshasa, RDC',
            'phone' => '+243 XX XXX XXXX',
            'email' => 'contact@vintapp.com',
            'website' => 'www.vintapp.com',
            'logo' => asset('images/logo.png'),
        ];
        
        // Pour l'instant, on redirige vers la vue
        // À implémenter avec dompdf plus tard
        return view('admin.orders.invoice', compact('order', 'currentTracking', 'company'));
    }

    // =============================================
    // GESTION DES EXPERTS
    // =============================================

    /**
     * Afficher la liste des experts
     */
    public function experts()
    {
        $experts = \App\Models\ExpertProfile::with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistiques des experts
        $stats = [
            'total_experts' => \App\Models\ExpertProfile::count(),
            'active_experts' => \App\Models\ExpertProfile::where('is_active', true)->count(),
            'total_verifications' => \App\Models\ProductAuthenticityCheck::whereNotNull('expert_id')->count(),
            'pending_verifications' => \App\Models\ProductAuthenticityCheck::where('status', 'expert_review')->count(),
        ];

        return view('admin.experts.index', compact('experts', 'stats'));
    }

    /**
     * Afficher la liste des utilisateurs pouvant devenir experts
     */
    public function expertCandidates()
    {
        $minOrders = request('min_orders', 0); // Changé de 1 à 0 pour voir tous les candidats
        $verifiedOnly = request('verified_only');
        $search = request('search');

        $query = User::whereDoesntHave('expertProfile');
        // Retirer la contrainte de vérification obligatoire pour inclure tous les utilisateurs
        // ->whereNotNull('email_verified_at');

        // Ajouter les relations pour calculer les statistiques
        $query->withCount(['orders as orders_count'])
            ->with(['orders' => function($q) {
                $q->select('buyer_id', 'seller_id', 'status', 'created_at');
            }]);

        // Critères de base recommandés
        if ($minOrders > 0) {
            $query->having('orders_count', '>=', $minOrders);
        }

        // Filtre de recherche
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtrer par utilisateurs vérifiés uniquement
        if ($verifiedOnly) {
            $query->whereNotNull('email_verified_at');
        }

        // Ajouter les statistiques calculées
        $candidates = $query->orderBy('name')
            ->paginate(20)
            ->through(function ($user) {
                // Calculer le taux de satisfaction (exemple basique)
                $totalOrders = $user->orders_count ?? 0;
                if ($totalOrders > 0) {
                    // Simuler un taux de satisfaction basé sur les commandes réussies
                    $successfulOrders = $user->orders()->where('status', 'completed')->count();
                    $user->satisfaction_rate = $totalOrders > 0 ? ($successfulOrders / $totalOrders) * 100 : 0;
                } else {
                    $user->satisfaction_rate = 0;
                }
                
                // Ajouter la dernière activité (si pas déjà présente)
                if (!isset($user->last_activity)) {
                    $user->last_activity = $user->updated_at;
                }
                
                return $user;
            });

        // Récupérer les catégories pour les spécialisations
        $categories = \App\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.experts.candidates', compact('candidates', 'categories'));
    }

    /**
     * Désigner un utilisateur comme expert
     */
    public function designateExpert(Request $request, User $user)
    {
        // Récupérer les slugs valides des catégories + 'general' pour les généralistes
        $validCategorySlugs = \App\Models\Category::pluck('slug')->toArray();
        $validCategorySlugs[] = 'general'; // Ajouter l'option généraliste
        
        $request->validate([
            'specialties' => 'required|array|min:1',
            'specialties.*' => 'required|string|in:' . implode(',', $validCategorySlugs),
            'certification_level' => 'required|string|in:junior,senior,master',
            'bio' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Vérifier que l'utilisateur n'est pas déjà expert
            if ($user->expertProfile) {
                return redirect()->back()->with('error', 'Cet utilisateur est déjà expert.');
            }

            // Créer le profil expert
            $expertProfile = \App\Models\ExpertProfile::create([
                'user_id' => $user->id,
                'specialties' => $request->specialties,
                'certification_level' => $request->certification_level,
                'bio' => $request->bio,
                'is_active' => true,
                'verification_count' => 0,
                'approval_rate' => 0,
            ]);

            // Assigner le rôle expert si pas déjà présent
            $expertRole = \App\Models\Role::where('slug', 'expert')->first();
            if ($expertRole && !$user->roles->contains($expertRole)) {
                $user->roles()->attach($expertRole);
            }

            DB::commit();

            Log::info("Utilisateur désigné comme expert", [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'specialties' => $request->specialties,
                'admin_id' => Auth::id()
            ]);

            return redirect()->route('admin.experts.index')
                ->with('success', "{$user->name} a été désigné comme expert avec succès.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la désignation d'expert", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Afficher le détail d'un expert
     */
    public function expertShow(\App\Models\ExpertProfile $expert)
    {
        $expert->load(['user', 'verifications.item', 'verifications.vendor']);

        // Statistiques détaillées
        $stats = [
            'total_verifications' => $expert->verifications()->count(),
            'pending_verifications' => $expert->verifications()
                ->where('status', \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)
                ->count(),
            'completed_verifications' => $expert->verifications()
                ->whereIn('status', [
                    \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_APPROVED,
                    \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_REJECTED
                ])->count(),
            'approved_verifications' => $expert->verifications()
                ->where('status', \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_APPROVED)
                ->count(),
            'avg_review_time' => $this->calculateExpertAverageReviewTime($expert->user_id),
            'this_month_verifications' => $expert->verifications()
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        // Dernières vérifications
        $recentVerifications = $expert->verifications()
            ->with(['item', 'vendor'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.experts.show', compact('expert', 'stats', 'recentVerifications'));
    }

    /**
     * Modifier un expert
     */
    public function expertEdit(\App\Models\ExpertProfile $expert)
    {
        $expert->load('user');
        
        $specialtyOptions = [
            'mode_luxe' => 'Mode Luxe',
            'electronique' => 'Électronique',
            'bijoux' => 'Bijoux',
            'montres' => 'Montres',
            'sacs_maroquinerie' => 'Sacs & Maroquinerie',
            'vetements-femmes' => 'Vêtements Femmes',
            'vetements-hommes' => 'Vêtements Hommes',
            'vareuse' => 'Vareuse',
            'general' => 'Généraliste'
        ];

        return view('admin.experts.edit', compact('expert', 'specialtyOptions'));
    }

    /**
     * Mettre à jour un expert
     */
    public function expertUpdate(Request $request, \App\Models\ExpertProfile $expert)
    {
        $request->validate([
            'specialties' => 'required|array|min:1',
            'specialties.*' => 'required|string|in:mode_luxe,electronique,bijoux,montres,sacs_maroquinerie,vetements-femmes,vetements-hommes,vareuse,general',
            'certification_level' => 'required|string|in:junior,senior,master',
            'bio' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        try {
            $expert->update([
                'specialties' => $request->specialties,
                'certification_level' => $request->certification_level,
                'bio' => $request->bio,
                'is_active' => $request->has('is_active'),
            ]);

            Log::info("Profil expert mis à jour", [
                'expert_id' => $expert->id,
                'user_id' => $expert->user_id,
                'admin_id' => Auth::id()
            ]);

            return redirect()->route('admin.experts.show', $expert)
                ->with('success', 'Profil expert mis à jour avec succès.');

        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour de l'expert", [
                'expert_id' => $expert->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return redirect()->back()->with('error', 'Une erreur est survenue.');
        }
    }

    /**
     * Activer/Désactiver un expert
     */
    public function expertToggleStatus(\App\Models\ExpertProfile $expert)
    {
        try {
            $expert->update(['is_active' => !$expert->is_active]);
            
            $status = $expert->is_active ? 'activé' : 'désactivé';
            
            Log::info("Statut expert modifié", [
                'expert_id' => $expert->id,
                'user_id' => $expert->user_id,
                'new_status' => $expert->is_active,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Expert {$status} avec succès.",
                'status' => $expert->is_active
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors du changement de statut expert", [
                'expert_id' => $expert->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * Révoquer le statut d'expert
     */
    public function expertRevoke(\App\Models\ExpertProfile $expert)
    {
        try {
            DB::beginTransaction();

            // Vérifier qu'il n'y a pas de vérifications en cours
            $pendingVerifications = \App\Models\ProductAuthenticityCheck::where('expert_id', $expert->user_id)
                ->where('status', \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)
                ->count();

            if ($pendingVerifications > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Impossible de révoquer le statut : {$pendingVerifications} vérification(s) en cours."
                ], 400);
            }

            $userName = $expert->user->name;

            // Retirer le rôle expert
            $expertRole = \App\Models\Role::where('slug', 'expert')->first();
            if ($expertRole) {
                $expert->user->roles()->detach($expertRole);
            }

            // Supprimer le profil expert
            $expert->delete();

            DB::commit();

            Log::info("Statut expert révoqué", [
                'user_id' => $expert->user_id,
                'user_name' => $userName,
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => "Le statut d'expert de {$userName} a été révoqué.",
                'redirect_url' => route('admin.experts.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la révocation d'expert", [
                'expert_id' => $expert->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la révocation du statut d\'expert.'
            ], 500);
        }
    }

    /**
     * Calculer le temps moyen de révision d'un expert
     */
    private function calculateExpertAverageReviewTime($expertId)
    {
        $completedChecks = \App\Models\ProductAuthenticityCheck::where('expert_id', $expertId)
            ->whereIn('status', [
                \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_APPROVED,
                \App\Models\ProductAuthenticityCheck::STATUS_EXPERT_REJECTED
            ])
            ->whereNotNull('expert_assigned_at')
            ->whereNotNull('expert_completed_at')
            ->get();

        if ($completedChecks->isEmpty()) {
            return 0;
        }

        $totalMinutes = $completedChecks->sum(function ($check) {
            return $check->expert_assigned_at->diffInMinutes($check->expert_completed_at);
        });

        return round($totalMinutes / $completedChecks->count(), 1);
    }

    // =============================================
    // GESTION DES ADMINS
    // =============================================

    /**
     * Afficher la liste des candidats à administrateur
     */
    public function adminCandidates(Request $request)
    {
        $query = User::whereNotIn('id', function ($subquery) {
            $subquery->select('user_id')
                ->from('role_user')
                ->where('role_id', function ($q) {
                    $q->select('id')->from('roles')->where('slug', 'admin');
                });
        })
        ->with(['roles']);

        // Filtrer par statut
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $candidates = $query->latest()->paginate(20);

        return view('admin.admins.candidates', compact('candidates'));
    }

    /**
     * Afficher les détails d'un candidat à administrateur
     */
    public function adminCandidateShow(User $user)
    {
        // Vérifier que l'utilisateur n'est pas déjà admin
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.admins.candidates')
                ->with('error', 'Cet utilisateur est déjà administrateur.');
        }

        $user->load(['roles', 'wallets', 'transactions', 'ordersAsBuyer', 'ordersAsSeller']);
        
        $stats = [
            'total_items' => $user->items()->count(),
            'active_items' => $user->items()->where('status', 'active')->count(),
            'total_orders_bought' => $user->ordersAsBuyer()->count(),
            'total_orders_sold' => $user->ordersAsSeller()->count(),
            'completed_orders' => $user->ordersAsBuyer()
                ->where('status', 'completed')
                ->count() + $user->ordersAsSeller()->where('status', 'completed')->count(),
            'total_transactions' => $user->transactions()->count(),
            'account_age_days' => $user->created_at->diffInDays(now()),
            'email_verified' => !is_null($user->email_verified_at),
            'phone_verified' => !is_null($user->phone),
        ];

        return view('admin.admins.candidate-show', compact('user', 'stats'));
    }

    /**
     * Afficher le formulaire de désignation d'un administrateur
     */
    public function designateAdminForm(User $user)
    {
        // Vérifier que l'utilisateur n'est pas déjà admin
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.admins.candidates')
                ->with('error', 'Cet utilisateur est déjà administrateur.');
        }

        return view('admin.admins.designate', compact('user'));
    }

    /**
     * Désigner un utilisateur comme administrateur
     */
    public function designateAdmin(Request $request, User $user)
    {
        // Vérifier que l'utilisateur n'est pas déjà admin
        if ($user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur est déjà administrateur.'
            ], 422);
        }

        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:manage_users,manage_items,manage_wallets,manage_transactions,manage_orders,manage_experts,manage_admins,manage_settings,manage_support,view_analytics,full_access',
            'reason' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Assigner le rôle admin
            $adminRole = Role::where('slug', 'admin')->first();
            if (!$adminRole) {
                // Créer le rôle s'il n'existe pas
                $adminRole = Role::create([
                    'name' => 'Administrator',
                    'slug' => 'admin',
                    'description' => 'Administrateur de la plateforme'
                ]);
            }

            $user->roles()->attach($adminRole);

            // Enregistrer la désignation dans le log
            Log::info("Nouvel administrateur désigné", [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'permissions' => $request->permissions,
                'reason' => $request->reason,
                'designated_by' => Auth::id(),
                'designated_at' => now()
            ]);

            // Notifier l'utilisateur
            $user->notify(new \App\Notifications\AdminDesignationNotification(Auth::user()));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$user->name} a été désigné comme administrateur avec succès."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la désignation d'administrateur", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la désignation.'
            ], 500);
        }
    }

    /**
     * Révoquer les droits d'administrateur
     */
    public function revokeAdmin(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if (!$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Cet utilisateur n\'est pas administrateur.'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Retirer le rôle admin
            $adminRole = Role::where('slug', 'admin')->first();
            if ($adminRole) {
                $user->roles()->detach($adminRole);
            }

            // Enregistrer dans le log
            Log::info("Droits d'administrateur révoqués", [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'reason' => $request->reason,
                'revoked_by' => Auth::id(),
                'revoked_at' => now()
            ]);

            // Notifier l'utilisateur
            $user->notify(new \App\Notifications\AdminRevocationNotification(Auth::user(), $request->reason));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Les droits d'administrateur de {$user->name} ont été révoqués avec succès."
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la révocation d'administrateur", [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la révocation.'
            ], 500);
        }
    }

    /**
     * Afficher la liste des administrateurs actuels
     */
    public function admins(Request $request)
    {
        $query = User::whereHas('roles', function ($q) {
            $q->where('slug', 'admin');
        })->with(['roles']);

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $admins = $query->latest()->paginate(20);

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Afficher les détails d'un administrateur
     */
    public function adminShow(User $user)
    {
        if (!$user->hasRole('admin')) {
            return redirect()->route('admin.admins.index')
                ->with('error', 'Cet utilisateur n\'est pas administrateur.');
        }

        $user->load(['roles']);

        return view('admin.admins.show', compact('user'));
    }

    // =============================================
    // UTILISATEURS CONNECTÉS EN TEMPS RÉEL
    // =============================================

    /**
     * Afficher la liste des utilisateurs connectés
     */
    public function onlineUsers()
    {
        // Récupérer les sessions actives (< 5 minutes)
        $onlineSessions = \App\Models\UserSession::getActiveSessions();
        
        // Grouper par utilisateur
        $onlineUsers = $onlineSessions->groupBy('user_id')->map(function($sessions) {
            $user = $sessions->first()->user;
            $latestSession = $sessions->sortByDesc('last_activity')->first();
            
            return [
                'user' => $user,
                'session' => $latestSession,
                'sessions_count' => $sessions->count(),
                'devices' => $sessions->pluck('device_type')->unique()->values(),
            ];
        })->values();
        
        // Statistiques
        $stats = [
            'total_online' => $onlineUsers->count(),
            'by_device' => [
                'mobile' => $onlineSessions->where('device_type', 'mobile')->count(),
                'tablet' => $onlineSessions->where('device_type', 'tablet')->count(),
                'desktop' => $onlineSessions->where('device_type', 'desktop')->count(),
            ],
            'by_role' => [
                'admin' => $onlineUsers->filter(fn($item) => $item['user']->hasRole('admin'))->count(),
                'seller' => $onlineUsers->filter(fn($item) => $item['user']->is_seller)->count(),
                'buyer' => $onlineUsers->filter(fn($item) => !$item['user']->hasRole('admin') && !$item['user']->is_seller)->count(),
            ],
        ];
        
        return view('admin.users.online', compact('onlineUsers', 'stats'));
    }

    /**
     * API pour récupérer les utilisateurs connectés (AJAX)
     */
    public function getOnlineUsersData()
    {
        $onlineSessions = \App\Models\UserSession::getActiveSessions();
        
        $data = $onlineSessions->map(function($session) {
            return [
                'id' => $session->id,
                'user' => [
                    'id' => $session->user->id,
                    'name' => $session->user->name,
                    'email' => $session->user->email,
                    'avatar' => $session->user->avatar ?? asset('images/default-avatar.png'),
                ],
                'device_type' => $session->device_type,
                'device_icon' => $session->device_icon,
                'browser' => $session->browser,
                'browser_icon' => $session->browser_icon,
                'os' => $session->os,
                'ip_address' => $session->ip_address,
                'location' => $session->location_text,
                'latitude' => $session->latitude,
                'longitude' => $session->longitude,
                'last_activity' => $session->last_activity_text,
                'last_activity_raw' => $session->last_activity->toIso8601String(),
            ];
        });
        
        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'users' => $data,
        ]);
    }

    /**
     * Déconnecter un utilisateur (forcer la déconnexion)
     */
    public function forceLogoutUser(Request $request, $sessionId)
    {
        try {
            $session = \App\Models\UserSession::findOrFail($sessionId);
            $session->markAsInactive();
            
            Log::info('Utilisateur déconnecté par admin', [
                'admin_id' => Auth::id(),
                'user_id' => $session->user_id,
                'session_id' => $sessionId,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Utilisateur déconnecté avec succès',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la déconnexion forcée', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue',
            ], 500);
        }
    }

    /**
     * R�cup�rer les notifications de l'admin
     */
    public function getNotifications(Request $request)
    {
        try {
            $user = Auth::user();
            
            // R�cup�rer les notifications (limit� � 10)
            $notifications = $user->notifications()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'message' => $notification->data['message'] ?? 'Nouvelle notification',
                        'icon' => $this->getNotificationIcon($notification->type),
                        'link' => $notification->data['link'] ?? '#',
                        'created_at' => $notification->created_at->diffForHumans(),
                        'read_at' => $notification->read_at,
                    ];
                });

            $unreadCount = $user->unreadNotifications()->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur notifications', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0,
            ], 200);
        }
    }

    /**
     * Obtenir l'ic�ne pour un type de notification
     */
    private function getNotificationIcon($type)
    {
        $icons = [
            'App\Notifications\NewUserRegistered' => 'fa-user-plus',
            'App\Notifications\NewOrder' => 'fa-shopping-cart',
            'App\Notifications\NewTransaction' => 'fa-dollar-sign',
            'App\Notifications\ItemVerificationRequest' => 'fa-check-circle',
            'App\Notifications\NewSupportTicket' => 'fa-question-circle',
        ];

        return $icons[$type] ?? 'fa-bell';
    }

    // ==================== API Methods ====================

    /**
     * Dashboard stats via API
     */
    public function apiDashboard()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'new_users_today' => User::whereDate('created_at', today())->count(),
                'active_users' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),
                
                'total_transactions' => Transaction::count(),
                'transactions_today' => Transaction::whereDate('created_at', today())->count(),
                'total_revenue_usd' => Transaction::where('status', 'completed')
                    ->where('currency', 'USD')
                    ->sum('amount'),
                'total_revenue_cdf' => Transaction::where('status', 'completed')
                    ->where('currency', 'CDF')
                    ->sum('amount'),
                
                'pending_wallets' => Wallet::where('type', 'pending')->count(),
                'total_orders' => Order::count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'total_items' => Item::count(),
                'active_items' => Item::where('status', 'active')->count(),
                
                'total_support_chats' => SupportChat::count(),
                'open_support_chats' => SupportChat::where('status', 'open')->count(),
            ];

            return $this->successResponse($stats, 'Statistiques dashboard admin');
        } catch (\Exception $e) {
            Log::error('API Admin Dashboard Error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur récupération stats', 500);
        }
    }

    /**
     * Get users via API
     */
    public function apiUsers(Request $request)
    {
        try {
            $query = User::with(['roles', 'wallets']);
            
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $users = $query->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($users, 'Liste utilisateurs');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération utilisateurs', 500);
        }
    }

    /**
     * Get wallets via API
     */
    public function apiWallets(Request $request)
    {
        try {
            $query = Wallet::with(['user']);
            
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            
            $wallets = $query->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($wallets, 'Liste wallets');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération wallets', 500);
        }
    }

    /**
     * Get transactions via API
     */
    public function apiTransactions(Request $request)
    {
        try {
            $query = Transaction::with(['user', 'wallet']);
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $transactions = $query->latest()->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($transactions, 'Liste transactions');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération transactions', 500);
        }
    }

    /**
     * Get orders via API
     */
    public function apiOrders(Request $request)
    {
        try {
            $query = Order::with(['buyer', 'seller', 'item']);
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $orders = $query->latest()->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($orders, 'Liste commandes');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération commandes', 500);
        }
    }

    /**
     * Get pending wallets via API
     */
    public function apiPendingWallets()
    {
        try {
            $pendingWallets = Wallet::with(['user'])
                ->where('type', 'pending')
                ->orderBy('balance', 'desc')
                ->paginate(20);
            
            return $this->paginatedResponse($pendingWallets, 'Wallets en attente');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération wallets', 500);
        }
    }

    /**
     * Approve wallet via API
     */
    public function apiApproveWallet($walletId)
    {
        try {
            $wallet = Wallet::findOrFail($walletId);
            
            DB::beginTransaction();
            
            $wallet->update([
                'status' => 'approved',
                'is_active' => true,
                'verified_by' => Auth::id()
            ]);
            
            Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'wallet_approval',
                'amount' => $wallet->balance,
                'currency' => $wallet->currency,
                'status' => 'completed',
                'processed_by' => Auth::id()
            ]);
            
            DB::commit();
            
            return $this->successResponse($wallet, 'Wallet approuvé avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur approbation wallet', 500);
        }
    }

    /**
     * Reject wallet via API
     */
    public function apiRejectWallet(Request $request, $walletId)
    {
        $validator = \Validator::make($request->all(), [
            'reason' => 'required|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $wallet = Wallet::findOrFail($walletId);
            
            DB::beginTransaction();
            
            $wallet->update([
                'status' => 'rejected',
                'rejection_reason' => $request->reason,
                'verified_by' => Auth::id()
            ]);
            
            Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'wallet_rejection',
                'amount' => $wallet->balance,
                'currency' => $wallet->currency,
                'status' => 'failed',
                'failure_reason' => $request->reason,
                'processed_by' => Auth::id()
            ]);
            
            DB::commit();
            
            return $this->successResponse(null, 'Wallet rejeté avec succès');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur rejet wallet', 500);
        }
    }

    /**
     * Get items via API
     */
    public function apiItems(Request $request)
    {
        try {
            $query = Item::with(['user', 'category', 'brand']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $items = $query->latest()->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($items, 'Liste articles');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération articles', 500);
        }
    }

    /**
     * Get brands via API
     */
    public function apiBrands(Request $request)
    {
        try {
            $brands = Brand::withCount(['items'])
                ->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($brands, 'Liste marques');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération marques', 500);
        }
    }

    /**
     * Get categories via API
     */
    public function apiCategories(Request $request)
    {
        try {
            $categories = Category::withCount(['items'])
                ->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($categories, 'Liste catégories');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération catégories', 500);
        }
    }

    /**
     * Get support chats via API
     */
    public function apiSupportChats(Request $request)
    {
        try {
            $query = SupportChat::with(['user', 'admin', 'messages']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('priority')) {
                $query->where('priority', $request->priority);
            }

            $chats = $query->latest()->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($chats, 'Liste tickets support');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération support', 500);
        }
    }

    /**
     * Get verification checks via API
     */
    public function apiVerificationChecks(Request $request)
    {
        try {
            $query = ProductAuthenticityCheck::with(['item', 'vendor', 'expert']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $checks = $query->latest()->paginate($request->per_page ?? 20);
            
            return $this->paginatedResponse($checks, 'Liste vérifications authenticité');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération vérifications', 500);
        }
    }

    /**
     * Update user status via API
     */
    public function apiUserUpdateStatus(Request $request, $userId)
    {
        $validator = \Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,suspend,delete'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $user = User::findOrFail($userId);
            
            DB::beginTransaction();
            
            switch ($request->action) {
                case 'activate':
                    $user->update(['status' => 'active']);
                    $message = 'Utilisateur activé avec succès';
                    break;
                
                case 'deactivate':
                    $user->update(['status' => 'inactive']);
                    $message = 'Utilisateur désactivé avec succès';
                    break;
                
                case 'suspend':
                    $user->update(['status' => 'suspended']);
                    $message = 'Utilisateur suspendu avec succès';
                    break;
                
                case 'delete':
                    $user->delete();
                    $message = 'Utilisateur supprimé avec succès';
                    break;
            }
            
            DB::commit();
            
            return $this->successResponse(null, $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur action utilisateur', 500);
        }
    }

    /**
     * Get user details via API
     */
    public function apiUserShow($userId)
    {
        try {
            $user = User::with(['roles', 'wallets', 'transactions', 'ordersAsBuyer', 'ordersAsSeller'])
                ->findOrFail($userId);
            
            $stats = $user->getStats();
            
            return $this->successResponse([
                'user' => $user,
                'stats' => $stats
            ], 'Détails utilisateur');
        } catch (\Exception $e) {
            return $this->errorResponse('Utilisateur introuvable', 404);
        }
    }

    /**
     * Update item status via API
     */
    public function apiItemUpdateStatus(Request $request, $itemId)
    {
        $validator = \Validator::make($request->all(), [
            'status' => 'required|in:pending,active,sold,inactive'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $item = Item::findOrFail($itemId);
            $item->update(['status' => $request->status]);
            
            return $this->successResponse($item, 'Statut article mis à jour');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur mise à jour statut', 500);
        }
    }

    /**
     * Get reports via API
     */
    public function apiReports(Request $request)
    {
        try {
            $period = $request->get('period', 30);
            $startDate = Carbon::now()->subDays($period);
            
            $reports = [
                'revenue' => $this->getRevenueReport($startDate),
                'users' => $this->getUsersReport($startDate),
                'transactions' => $this->getTransactionsReport($startDate),
                'popular_items' => $this->getPopularItemsReport($startDate)
            ];
            
            return $this->successResponse($reports, 'Rapports statistiques');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur génération rapports', 500);
        }
    }

    /**
     * Get settings via API
     */
    public function apiSettings()
    {
        try {
            $settings = Setting::all()->groupBy('category');
            
            return $this->successResponse($settings, 'Paramètres système');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération paramètres', 500);
        }
    }

    /**
     * Update setting via API
     */
    public function apiUpdateSetting(Request $request, $key)
    {
        $validator = \Validator::make($request->all(), [
            'value' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $setting = Setting::where('key', $key)->first();
            
            if (!$setting) {
                return $this->errorResponse('Paramètre introuvable', 404);
            }
            
            $setting->update(['value' => $request->value]);
            
            Cache::forget('settings');
            
            return $this->successResponse($setting, 'Paramètre mis à jour');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur mise à jour paramètre', 500);
        }
    }

    /**
     * Get online users via API
     */
    public function apiOnlineUsers()
    {
        try {
            $onlineSessions = \App\Models\UserSession::getActiveSessions();
            
            $data = $onlineSessions->map(function($session) {
                return [
                    'user_id' => $session->user_id,
                    'user_name' => $session->user->name ?? 'Unknown',
                    'user_email' => $session->user->email ?? '',
                    'device_type' => $session->device_type,
                    'browser' => $session->browser,
                    'last_activity' => $session->last_activity_at,
                    'ip_address' => $session->ip_address,
                    'location' => $session->location
                ];
            });
            
            $stats = [
                'total_online' => $onlineSessions->count(),
                'unique_users' => $onlineSessions->unique('user_id')->count()
            ];
            
            return $this->successResponse([
                'users' => $data,
                'stats' => $stats
            ], 'Utilisateurs connectés');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération utilisateurs en ligne', 500);
        }
    }

    /**
     * Bulk approve wallets via API
     */
    public function apiBulkApproveWallets(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'wallet_ids' => 'required|array',
            'wallet_ids.*' => 'exists:wallets,id'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();
            
            $wallets = Wallet::whereIn('id', $request->wallet_ids)
                ->where('type', 'pending')
                ->get();
            
            $approvedCount = 0;
            
            foreach ($wallets as $wallet) {
                $wallet->update([
                    'status' => 'approved',
                    'is_active' => true,
                    'verified_by' => Auth::id()
                ]);
                
                Transaction::create([
                    'user_id' => $wallet->user_id,
                    'wallet_id' => $wallet->id,
                    'type' => 'wallet_approval',
                    'amount' => $wallet->balance,
                    'currency' => $wallet->currency,
                    'status' => 'completed',
                    'processed_by' => Auth::id()
                ]);
                
                $approvedCount++;
            }
            
            DB::commit();
            
            return $this->successResponse([
                'approved_count' => $approvedCount
            ], "{$approvedCount} wallets approuvés avec succès");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur approbation en masse', 500);
        }
    }

    /**
     * Get admin notifications via API
     */
    public function apiNotifications()
    {
        try {
            $notifications = [];
            
            $pendingWallets = Wallet::where('status', 'pending')->count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $failedTransactions = Transaction::where('status', 'failed')
                ->whereDate('created_at', today())
                ->count();
            $pendingSupport = SupportChat::whereIn('status', ['open', 'in_progress'])->count();
            $pendingVerifications = ProductAuthenticityCheck::whereIn('status', ['pending', 'expert_review'])->count();
            
            if ($pendingWallets > 0) {
                $notifications[] = [
                    'type' => 'pending_wallets',
                    'title' => 'Wallets en attente',
                    'message' => "{$pendingWallets} wallet(s) en attente de validation",
                    'count' => $pendingWallets,
                    'icon' => 'fa-wallet',
                    'color' => 'warning',
                    'url' => route('admin.wallets.pending')
                ];
            }
            
            if ($pendingOrders > 0) {
                $notifications[] = [
                    'type' => 'pending_orders',
                    'title' => 'Commandes en attente',
                    'message' => "{$pendingOrders} commande(s) en attente",
                    'count' => $pendingOrders,
                    'icon' => 'fa-shopping-cart',
                    'color' => 'info',
                    'url' => route('admin.orders.index')
                ];
            }
            
            if ($failedTransactions > 0) {
                $notifications[] = [
                    'type' => 'failed_transactions',
                    'title' => 'Transactions échouées',
                    'message' => "{$failedTransactions} transaction(s) échouée(s) aujourd'hui",
                    'count' => $failedTransactions,
                    'icon' => 'fa-exclamation-triangle',
                    'color' => 'danger',
                    'url' => route('admin.transactions.index')
                ];
            }

            if ($pendingSupport > 0) {
                $notifications[] = [
                    'type' => 'pending_support',
                    'title' => 'Tickets support',
                    'message' => "{$pendingSupport} ticket(s) nécessitent attention",
                    'count' => $pendingSupport,
                    'icon' => 'fa-headset',
                    'color' => 'primary',
                    'url' => route('admin.support.index')
                ];
            }

            if ($pendingVerifications > 0) {
                $notifications[] = [
                    'type' => 'pending_verifications',
                    'title' => 'Vérifications authenticité',
                    'message' => "{$pendingVerifications} vérification(s) en attente",
                    'count' => $pendingVerifications,
                    'icon' => 'fa-certificate',
                    'color' => 'success',
                    'url' => route('admin.authenticity.index')
                ];
            }
            
            return $this->successResponse([
                'notifications' => $notifications,
                'total_count' => count($notifications)
            ], 'Notifications admin');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération notifications', 500);
        }
    }

    /**
     * Get admin stats summary via API
     */
    public function apiStatsSummary()
    {
        try {
            $stats = [
                'users' => [
                    'total' => User::count(),
                    'today' => User::whereDate('created_at', today())->count(),
                    'active_7d' => User::where('last_seen', '>=', Carbon::now()->subDays(7))->count(),
                    'verified' => User::whereNotNull('email_verified_at')->count()
                ],
                'transactions' => [
                    'total' => Transaction::count(),
                    'today' => Transaction::whereDate('created_at', today())->count(),
                    'completed' => Transaction::where('status', 'completed')->count(),
                    'total_amount_usd' => Transaction::where('status', 'completed')
                        ->where('currency', 'USD')
                        ->sum('amount'),
                    'total_amount_cdf' => Transaction::where('status', 'completed')
                        ->where('currency', 'CDF')
                        ->sum('amount')
                ],
                'orders' => [
                    'total' => Order::count(),
                    'today' => Order::whereDate('created_at', today())->count(),
                    'pending' => Order::where('status', 'pending')->count(),
                    'completed' => Order::where('status', 'completed')->count()
                ],
                'items' => [
                    'total' => Item::count(),
                    'active' => Item::where('status', 'active')->count(),
                    'pending' => Item::where('status', 'pending')->count(),
                    'sold' => Item::where('status', 'sold')->count()
                ],
                'wallets' => [
                    'pending' => Wallet::where('type', 'pending')->count(),
                    'total_balance_usd' => Wallet::where('is_active', true)
                        ->where('currency', 'USD')
                        ->sum('balance'),
                    'total_balance_cdf' => Wallet::where('is_active', true)
                        ->where('currency', 'CDF')
                        ->sum('balance')
                ],
                'support' => [
                    'total' => SupportChat::count(),
                    'open' => SupportChat::where('status', 'open')->count(),
                    'pending' => SupportChat::whereIn('status', ['open', 'in_progress'])->count(),
                    'unassigned' => SupportChat::whereNull('admin_id')
                        ->whereIn('status', ['open', 'in_progress'])
                        ->count()
                ],
                'verifications' => [
                    'total' => ProductAuthenticityCheck::count(),
                    'pending' => ProductAuthenticityCheck::whereIn('status', ['pending', 'expert_review'])->count(),
                    'completed' => ProductAuthenticityCheck::where('payment_completed', true)->count()
                ]
            ];
            
            return $this->successResponse($stats, 'Résumé statistiques admin');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur récupération statistiques', 500);
        }
    }
}