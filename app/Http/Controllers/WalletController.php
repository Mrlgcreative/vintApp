<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Affiche les wallets de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Créer les wallets s'ils n'existent pas
        $usdWallet = $this->getOrCreateUserWallet($user, 'USD');
        $cdfWallet = $this->getOrCreateUserWallet($user, 'CDF');

        // Récupérer les transactions récentes
        $recentTransactions = WalletTransaction::whereIn('wallet_id', [$usdWallet->id, $cdfWallet->id])
            ->with('wallet')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('wallet.index', compact('usdWallet', 'cdfWallet', 'recentTransactions'));
    }

    /**
     * Obtient ou crée un wallet pour un utilisateur et une devise donnée.
     */
    private function getOrCreateUserWallet($user, $currency)
    {
        $wallet = $user->wallets()->where('currency', $currency)->first();
        
        if (!$wallet) {
            $wallet = $user->wallets()->create([
                'currency' => $currency,
                'balance' => 0.00,
                'is_active' => true,
            ]);
        }
        
        return $wallet;
    }

    /**
     * Affiche l'historique des transactions d'un wallet.
     */
    public function transactions(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $transactions = $wallet->transactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.transactions', compact('wallet', 'transactions'));
    }

    /**
     * Affiche le formulaire d'ajout de fonds.
     */
    public function addFunds(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('wallet.add-funds', compact('wallet'));
    }

    /**
     * Traite l'ajout de fonds.
     */
    public function storeAddFunds(Request $request, Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($wallet, $validated) {
                $wallet->increment('balance', $validated['amount']);
                
                $wallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $validated['amount'],
                    'balance_after' => $wallet->fresh()->balance,
                    'description' => $validated['description'] ?? 'Ajout de fonds',
                    'reference' => 'ADD-' . time() . '-' . rand(1000, 9999),
                ]);
            });

            return redirect()->route('wallet.index')
                ->with('success', 'Fonds ajoutés avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'ajout des fonds : ' . $e->getMessage());
        }
    }

    /**
     * Affiche le formulaire de retrait de fonds.
     */
    public function withdrawFunds(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return view('wallet.withdraw-funds', compact('wallet'));
    }

    /**
     * Traite le retrait de fonds.
     */
    public function storeWithdrawFunds(Request $request, Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $wallet->balance,
            'description' => 'nullable|string|max:255',
        ]);

        if ($validated['amount'] > $wallet->balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Solde insuffisant pour effectuer ce retrait.');
        }

        try {
            DB::transaction(function () use ($wallet, $validated) {
                $wallet->decrement('balance', $validated['amount']);
                
                $wallet->transactions()->create([
                    'type' => 'debit',
                    'amount' => $validated['amount'],
                    'balance_after' => $wallet->fresh()->balance,
                    'description' => $validated['description'] ?? 'Retrait de fonds',
                    'reference' => 'WTH-' . time() . '-' . rand(1000, 9999),
                ]);
            });

            return redirect()->route('wallet.index')
                ->with('success', 'Retrait effectué avec succès !');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors du retrait : ' . $e->getMessage());
        }
    }

    /**
     * API pour obtenir le solde d'un wallet.
     */
    public function getBalance(Wallet $wallet)
    {
        // Vérifier que le wallet appartient à l'utilisateur connecté
        if ($wallet->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        return response()->json([
            'balance' => $wallet->balance,
            'formatted_balance' => $wallet->formatted_balance,
            'currency' => $wallet->currency,
        ]);
    }
}
