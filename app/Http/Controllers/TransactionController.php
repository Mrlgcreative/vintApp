<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Affiche la liste des transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::query()
            ->with(['user', 'wallet']);

        // Filtres
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Tri par défaut
        $query->latest();

        $transactions = $query->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($transactions);
        }

        return view('admin.transactions.index', compact('transactions'));
    }

    /**
     * Affiche une transaction spécifique
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'wallet']);

        if (request()->wantsJson()) {
            return response()->json($transaction);
        }

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Met à jour le statut d'une transaction
     */
    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:completed,failed,refunded'
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $transaction->status;
            $transaction->status = $request->status;

            // Si la transaction est complétée
            if ($request->status === 'completed' && $oldStatus === 'pending') {
                $wallet = $transaction->wallet;
                
                if ($transaction->type === 'deposit') {
                    $wallet->credit($transaction->amount);
                } elseif ($transaction->type === 'withdraw') {
                    $wallet->debit($transaction->amount);
                }
            }

            // Si la transaction est remboursée
            if ($request->status === 'refunded' && $oldStatus === 'completed') {
                $wallet = $transaction->wallet;
                
                if ($transaction->type === 'deposit') {
                    $wallet->debit($transaction->amount);
                } elseif ($transaction->type === 'withdraw') {
                    $wallet->credit($transaction->amount);
                }
            }

            $transaction->save();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Statut de la transaction mis à jour'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la mise à jour: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retourne les statistiques des transactions
     */
    public function statistics(Request $request)
    {
        $stats = [
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::pending()->count(),
            'completed_transactions' => Transaction::completed()->count(),
            'mobile_money_transactions' => Transaction::mobileMoney()->count(),
            'total_amount_today' => Transaction::whereDate('created_at', today())
                                              ->where('status', 'completed')
                                              ->sum('amount'),
            'transactions_by_payment_method' => Transaction::select('payment_method', DB::raw('count(*) as count'))
                                                         ->groupBy('payment_method')
                                                         ->get()
        ];

        if ($request->wantsJson()) {
            return response()->json($stats);
        }

        return view('admin.transactions.statistics', compact('stats'));
    }
}