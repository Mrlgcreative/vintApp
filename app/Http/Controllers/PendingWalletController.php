<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PendingWalletController extends Controller
{
    /**
     * Liste tous les wallets en attente
     */
    public function index(Request $request)
    {
        $query = Wallet::where('type', 'pending')
            ->with(['user', 'transactions' => function ($q) {
                $q->where('status', 'pending');
            }]);

        if ($request->has('currency')) {
            $query->where('currency', $request->currency);
        }

        $pendingWallets = $query->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($pendingWallets);
        }

        return view('admin.pending-wallets.index', compact('pendingWallets'));
    }

    /**
     * Affiche un wallet en attente spécifique
     */
    public function show(Wallet $wallet)
    {
        if ($wallet->type !== 'pending') {
            return abort(404);
        }

        $wallet->load(['user', 'transactions' => function ($q) {
            $q->where('status', 'pending')->latest();
        }]);

        if (request()->wantsJson()) {
            return response()->json($wallet);
        }

        return view('admin.pending-wallets.show', compact('wallet'));
    }

    /**
     * Confirme le transfert des fonds du wallet en attente vers le wallet principal
     */
    public function confirmTransfer(Request $request, Wallet $pendingWallet)
    {
        if ($pendingWallet->type !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ce wallet n\'est pas un wallet en attente'
            ], 400);
        }

        $request->validate([
            'transaction_id' => 'required|exists:transactions,id'
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($request->transaction_id);
            
            if ($transaction->status !== 'pending') {
                throw new \Exception('Cette transaction a déjà été traitée');
            }

            // Récupérer le wallet principal du vendeur
            $mainWallet = $transaction->user->mainWallet()
                ->where('currency', $pendingWallet->currency)
                ->firstOrFail();

            // Transférer les fonds
            $amount = $transaction->amount;
            $pendingWallet->transferTo($mainWallet, $amount);

            // Mettre à jour le statut de la transaction
            $transaction->markAsCompleted();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transfert confirmé avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors du transfert: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Annule une transaction en attente
     */
    public function cancelTransaction(Request $request, Wallet $pendingWallet)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id'
        ]);

        DB::beginTransaction();
        try {
            $transaction = Transaction::findOrFail($request->transaction_id);
            
            if ($transaction->status !== 'pending') {
                throw new \Exception('Cette transaction a déjà été traitée');
            }

            // Marquer la transaction comme échouée
            $transaction->markAsFailed();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction annulée avec succès'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'annulation: ' . $e->getMessage()
            ], 500);
        }
    }
}