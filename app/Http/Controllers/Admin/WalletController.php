<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WalletController extends Controller
{
    /**
     * Display a listing of enterprise wallets.
     */
    public function index()
    {
        // Récupérer les wallets entreprise avec statistiques
        $enterpriseWallets = Wallet::enterprise()
            ->with(['transactions' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get()
            ->map(function ($wallet) {
                // Calculer les statistiques pour chaque wallet
                $wallet->total_transactions = $wallet->transactions()->count();
                $wallet->monthly_commission = $wallet->transactions()
                    ->where('type', 'credit')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
                
                return $wallet;
            });

        // Statistiques générales
        $totalWallets = Wallet::enterprise()->count();
        $totalUsdBalance = Wallet::enterprise()->where('currency', 'USD')->sum('balance');
        $totalCdfBalance = Wallet::enterprise()->where('currency', 'CDF')->sum('balance');
        
        // Commissions aujourd'hui
        $commissionsToday = WalletTransaction::whereHas('wallet', function($query) {
                $query->enterprise();
            })
            ->where('type', 'credit')
            ->whereDate('created_at', today())
            ->count();

        // Données pour les graphiques (30 derniers jours)
        $chartData = $this->getChartData();

        // Dernières transactions entreprise
        $recentTransactions = WalletTransaction::whereHas('wallet', function($query) {
                $query->enterprise();
            })
            ->with('wallet')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($transaction) {
                $transaction->currency = $transaction->wallet->currency;
                return $transaction;
            });

        return view('admin.wallets.index', compact(
            'enterpriseWallets',
            'totalWallets',
            'totalUsdBalance',
            'totalCdfBalance',
            'commissionsToday',
            'chartData',
            'recentTransactions'
        ));
    }

    /**
     * Store a newly created enterprise wallet.
     */
    public function store(Request $request)
    {
        $request->validate([
            'currency' => 'required|in:USD,CDF',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'initial_balance' => 'nullable|numeric|min:0',
        ]);

        // Vérifier qu'un wallet entreprise pour cette devise n'existe pas déjà
        $existingWallet = Wallet::enterprise()
            ->where('currency', $request->currency)
            ->first();

        if ($existingWallet) {
            return redirect()->back()->with('error', 
                "Un wallet entreprise {$request->currency} existe déjà.");
        }

        DB::transaction(function () use ($request) {
            $wallet = Wallet::create([
                'user_id' => null, // Wallet entreprise n'appartient à aucun utilisateur
                'currency' => $request->currency,
                'balance' => $request->initial_balance ?? 0.00,
                'type' => Wallet::TYPE_ENTERPRISE,
                'commission_rate' => $request->commission_rate,
                'status' => 'active',
                'is_active' => true,
            ]);

            // Créer une transaction initiale si un solde initial est fourni
            if ($request->initial_balance && $request->initial_balance > 0) {
                $wallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $request->initial_balance,
                    'balance_after' => $wallet->balance,
                    'description' => 'Solde initial du wallet entreprise',
                    'reference' => 'INIT-' . time() . '-' . rand(1000, 9999),
                ]);
            }
        });

        return redirect()->route('admin.wallets.index')
            ->with('success', 'Wallet entreprise créé avec succès !');
    }

    /**
     * Display the specified wallet.
     */
    public function show(Wallet $wallet)
    {
        // Vérifier que c'est un wallet entreprise
        if (!$wallet->isEnterprise()) {
            abort(404, 'Wallet non trouvé');
        }

        $wallet->load(['transactions' => function($query) {
            $query->latest()->paginate(20);
        }]);

        // Statistiques du wallet
        $stats = [
            'total_transactions' => $wallet->transactions()->count(),
            'total_credits' => $wallet->transactions()->where('type', 'credit')->sum('amount'),
            'total_debits' => $wallet->transactions()->where('type', 'debit')->sum('amount'),
            'monthly_volume' => $wallet->transactions()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'weekly_volume' => $wallet->transactions()
                ->where('created_at', '>=', now()->subWeek())
                ->sum('amount'),
        ];

        return view('admin.wallets.show', compact('wallet', 'stats'));
    }

    /**
     * Display transactions for a specific wallet.
     */
    public function transactions(Wallet $wallet)
    {
        // Vérifier que c'est un wallet entreprise
        if (!$wallet->isEnterprise()) {
            abort(404, 'Wallet non trouvé');
        }

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20);

        return view('admin.wallets.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Get chart data for the last 30 days.
     */
    private function getChartData()
    {
        $days = collect();
        $usdData = collect();
        $cdfData = collect();

        // Générer les 30 derniers jours
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days->push($date->format('d/m'));
            
            // Commissions USD du jour
            $usdCommissions = WalletTransaction::whereHas('wallet', function($query) {
                    $query->enterprise()->where('currency', 'USD');
                })
                ->where('type', 'credit')
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            // Commissions CDF du jour
            $cdfCommissions = WalletTransaction::whereHas('wallet', function($query) {
                    $query->enterprise()->where('currency', 'CDF');
                })
                ->where('type', 'credit')
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            $usdData->push($usdCommissions);
            $cdfData->push($cdfCommissions);
        }

        return [
            'labels' => $days->toArray(),
            'usd' => $usdData->toArray(),
            'cdf' => $cdfData->toArray(),
        ];
    }

    /**
     * Add commission to enterprise wallet.
     */
    public function addCommission(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|in:USD,CDF',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
        ]);

        $wallet = Wallet::getEnterpriseWallet($request->currency);
        
        if (!$wallet) {
            return response()->json([
                'success' => false,
                'message' => "Aucun wallet entreprise {$request->currency} trouvé"
            ], 404);
        }

        DB::transaction(function () use ($wallet, $request) {
            $wallet->increment('balance', $request->amount);
            
            $wallet->transactions()->create([
                'type' => 'credit',
                'amount' => $request->amount,
                'balance_after' => $wallet->fresh()->balance,
                'description' => $request->description,
                'reference' => $request->reference ?? 'COM-' . time() . '-' . rand(1000, 9999),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Commission ajoutée avec succès',
            'new_balance' => $wallet->fresh()->formatted_balance
        ]);
    }

    /**
     * Process enterprise withdrawal.
     */
    public function withdraw(Request $request, Wallet $wallet)
    {
        if (!$wallet->isEnterprise()) {
            abort(404, 'Wallet non trouvé');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
            'description' => 'required|string|max:255',
            'payment_method' => 'required|in:bank_transfer,mobile_money',
            'account_details' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($wallet, $request) {
            $wallet->decrement('balance', $request->amount);
            
            $wallet->transactions()->create([
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $wallet->fresh()->balance,
                'description' => $request->description,
                'reference' => 'WTH-' . time() . '-' . rand(1000, 9999),
            ]);
        });

        return redirect()->back()->with('success', 'Retrait effectué avec succès !');
    }
}