<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductBoost;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductBoostController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductBoost::query()
            ->with(['item', 'user', 'boostType']);

        if ($search = $request->search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('item', function ($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        if ($request->status && in_array($request->status, ['active', 'expired', 'cancelled'])) {
            $query->where('status', $request->status);
        }

        if ($request->boost_type) {
            $query->where('boost_type_id', $request->boost_type);
        }

        $sort = $request->sort ?? '-created_at';
        $direction = 'asc';
        if (str_starts_with($sort, '-')) {
            $direction = 'desc';
            $sort = substr($sort, 1);
        }
        $query->orderBy($sort, $direction);

        $boosts = $query->paginate(15)->withQueryString();
        $boostTypes = \App\Models\BoostType::active()->orderBy('sort_order')->get();

        $stats = [
            'total' => ProductBoost::count(),
            'active' => ProductBoost::where('status', 'active')->count(),
            'expired' => ProductBoost::where('status', 'expired')->count(),
            'cancelled' => ProductBoost::where('status', 'cancelled')->count(),
            'revenue' => ProductBoost::where('status', 'active')->sum('total_price'),
        ];

        return view('admin.product-boosts.index', compact('boosts', 'boostTypes', 'stats'));
    }

    public function show(ProductBoost $productBoost)
    {
        $productBoost->load(['item', 'user', 'boostType']);
        return view('admin.product-boosts.show', compact('productBoost'));
    }

    public function cancel(Request $request, ProductBoost $productBoost)
    {
        if ($productBoost->status !== 'active') {
            return redirect()->route('admin.product-boosts.show', $productBoost)
                ->with('error', 'Ce boost n\'est pas actif.');
        }

        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $refundAmount = $productBoost->calculateRefundAmount();
            $boostType = $productBoost->boostType;

            $productBoost->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'refund_amount' => $refundAmount,
            ]);

            if ($refundAmount > 0) {
                $user = $productBoost->user;
                $boostCurrency = $productBoost->currency ?? 'CDF';

                // Wallet de l'utilisateur dans la devise du boost
                $wallet = $boostCurrency === 'USD'
                    ? $user->getOrCreateUsdWallet()
                    : $user->getOrCreateCdfWallet();

                $wallet->credit($refundAmount);
                $wallet->transactions()->create([
                    'type' => WalletTransaction::TYPE_CREDIT,
                    'amount' => $refundAmount,
                    'balance_after' => $wallet->fresh()->balance,
                    'description' => 'Remboursement annulation boost #' . $productBoost->id . ' (admin)',
                    'reference' => 'BOOST_REFUND_ADMIN_' . $productBoost->id . '_' . time(),
                    'status' => 'completed',
                ]);

                // Retirer le montant remboursé du revenu entreprise boost
                $boostEnterpriseWallet = Wallet::getEnterpriseSubWallet(Wallet::SUBTYPE_BOOST, $boostCurrency)
                    ?: Wallet::getEnterpriseWallet($boostCurrency);
                if ($boostEnterpriseWallet && $boostEnterpriseWallet->balance >= $refundAmount) {
                    $boostEnterpriseWallet->debit($refundAmount);
                    $boostEnterpriseWallet->transactions()->create([
                        'type' => WalletTransaction::TYPE_DEBIT,
                        'amount' => $refundAmount,
                        'balance_after' => $boostEnterpriseWallet->fresh()->balance,
                        'description' => 'Remboursement annulation boost #' . $productBoost->id . ' (admin)',
                        'reference' => 'BOOST_REFUND_ADMIN_IN_' . $productBoost->id . '_' . time(),
                        'status' => 'completed',
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.product-boosts.show', $productBoost)
                ->with('success', 'Boost annulé avec succès.'
                    . ($refundAmount > 0 ? " Remboursement de {$refundAmount} effectué." : ''));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin cancel boost failed', [
                'boost_id' => $productBoost->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.product-boosts.show', $productBoost)
                ->with('error', 'Erreur lors de l\'annulation : ' . $e->getMessage());
        }
    }
}
