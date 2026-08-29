<?php

namespace App\Services;

use App\Models\Distribution;
use App\Models\Item;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Crée une commande et met à jour le stock de l'article.
     *
     * @param array $data item_id, quantity, delivery_address_id, shipping_address|delivery_address,
     *                     shipping_city|delivery_city, shipping_phone|delivery_phone, notes|delivery_notes
     */
    public function create(array $data, User $buyer): Order
    {
        $item = Item::findOrFail($data['item_id']);

        if ($item->user_id === $buyer->id) {
            throw new DomainException('Vous ne pouvez pas acheter votre propre article.');
        }

        if ($item->status !== 'active') {
            throw new DomainException("Cet article n'est plus disponible.");
        }

        $quantity = $data['quantity'] ?? 1;
        if ($quantity > $item->quantity) {
            throw new DomainException('La quantité demandée dépasse le stock disponible.');
        }

        return DB::transaction(function () use ($item, $buyer, $data, $quantity) {
            $order = new Order();
            $order->buyer_id = $buyer->id;
            $order->seller_id = $item->user_id;
            $order->item_id = $item->id;
            $order->quantity = $quantity;
            $order->unit_price = $item->price;
            $order->total_amount = $item->price * $quantity;
            $order->currency = $item->currency;
            $order->status = 'pending';
            $order->shipping_address = $data['shipping_address'] ?? $data['delivery_address'] ?? null;
            $order->shipping_city = $data['shipping_city'] ?? $data['delivery_city'] ?? null;
            $order->shipping_phone = $data['shipping_phone'] ?? $data['delivery_phone'] ?? null;
            $order->delivery_address_id = $data['delivery_address_id'] ?? null;
            $order->notes = $data['notes'] ?? $data['delivery_notes'] ?? null;
            $order->save();

            $item->quantity -= $quantity;
            if ($item->quantity <= 0) {
                $item->status = 'sold';
            }
            $item->save();

            return $order;
        });
    }

    /**
     * Marque une commande pending comme confirmée et crédite le wallet pending
     * (escrow) du vendeur du montant total de la commande.
     */
    public function confirmPayment(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw new DomainException('Cette commande ne peut plus être confirmée.');
        }

        DB::transaction(function () use ($order) {
            $order->paid_at = now();
            $order->status = 'confirmed';
            $order->save();

            $this->creditEscrow($order);
        });
    }

    /**
     * Crédite le wallet pending (escrow) du vendeur du montant total de la
     * commande. Idempotent : ne crédite qu'une seule fois par commande pour
     * éviter tout double-crédit (les commandes déjà créditées via
     * create_orders_from_transaction sont ignorées).
     */
    public function creditEscrow(Order $order): void
    {
        $alreadyCredited = Transaction::where('type', 'deposit')
            ->where('purpose', 'like', 'Escrow - Commande #' . $order->id . '%')
            ->exists();

        if ($alreadyCredited) {
            return;
        }

        $seller = User::find($order->seller_id);
        if (!$seller) {
            throw new DomainException('Vendeur introuvable, fonds non crédités.');
        }

        $sellerPendingWallet = Wallet::firstOrCreate(
            [
                'user_id' => $seller->id,
                'type' => 'pending',
                'currency' => $order->currency,
            ],
            [
                'balance' => 0,
                'status' => 'active',
                'is_active' => true,
            ]
        );

        $sellerPendingWallet->increment('balance', $order->total_amount);

        Transaction::create([
            'transaction_id' => 'ESCROW-' . strtoupper(Str::random(12)),
            'user_id' => $seller->id,
            'buyer_id' => $order->buyer_id,
            'wallet_id' => $sellerPendingWallet->id,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'type' => 'deposit',
            'status' => 'completed',
            'payment_method' => 'wallet',
            'purpose' => 'Escrow - Commande #' . $order->id,
            'provider' => 'Escrow Deposit',
            'phone' => 'N/A',
        ]);

        Log::info("Escrow crédité pour la commande #{$order->id}", [
            'seller_id' => $seller->id,
            'amount' => $order->total_amount,
            'currency' => $order->currency,
            'pending_balance' => $sellerPendingWallet->fresh()->balance,
        ]);
    }

    /**
     * Marque une commande confirmée comme expédiée.
     */
    public function markShipped(Order $order): void
    {
        if ($order->status !== 'confirmed') {
            throw new DomainException("Cette commande ne peut pas être expédiée dans son état actuel.");
        }

        $order->status = 'shipped';
        $order->shipped_at = now();
        $order->save();
    }

    /**
     * Marque une commande expédiée comme livrée.
     */
    public function markDelivered(Order $order): void
    {
        if ($order->status !== 'shipped') {
            throw new DomainException("Cette commande doit d'abord être marquée comme expédiée.");
        }

        $order->status = 'delivered';
        $order->delivered_at = now();
        $order->save();
    }

    /**
     * Annule une commande en attente et remet le stock disponible.
     */
    public function cancel(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw new DomainException('Vous ne pouvez annuler que les commandes en attente.');
        }

        DB::transaction(function () use ($order) {
            $item = $order->item;

            // Si l'article a été supprimé entre-temps (suppression définitive),
            // on ne peut plus restaurer le stock : la commande est supprimée
            // quand même pour ne pas bloquer l'acheteur.
            if ($item) {
                $item->quantity += $order->quantity;
                if ($item->status === 'sold') {
                    $item->status = 'active';
                }
                $item->save();
            }

            $order->delete();
        });
    }

    /**
     * Confirme la réception de la commande par l'acheteur et distribue les fonds.
     */
    public function confirmDelivery(Order $order, ?string $note = null): array
    {
        if (!in_array($order->status, ['shipped', 'delivered', 'completed'])) {
            throw new DomainException("Cette commande n'est pas encore expédiée.");
        }

        if ($order->confirmed_by_buyer_at) {
            throw new DomainException('Vous avez déjà confirmé la réception de cette commande.');
        }

        return DB::transaction(function () use ($order, $note) {
            // Verrouillage pessimiste de la commande : deux confirmations
            // simultanées se sérialisent, empêchant tout double débit.
            $locked = Order::whereKey($order->id)->lockForUpdate()->first();

            // Idempotence : si les fonds ont déjà été distribués (doublon,
            // retry après rollback partiel), on ne redébite/ne re-crédite rien.
            $alreadyDistributed = Transaction::where('type', 'deposit')
                ->where('purpose', 'like', 'Vente confirmée - Commande #' . $order->id . '%')
                ->exists();

            if ($alreadyDistributed || ($locked && $locked->confirmed_by_buyer_at)) {
                $order->confirmed_by_buyer_at = now();
                $order->buyer_confirmation_note = $note;
                $order->status = 'completed';
                $order->save();
                return [];
            }

            $distribution = $this->distributeFunds($order);

            $order->confirmed_by_buyer_at = now();
            $order->buyer_confirmation_note = $note;
            $order->status = 'completed';
            $order->save();

            return $distribution;
        });
    }

    /**
     * Distribue le montant total d'une commande après confirmation de réception.
     *
     * Modèle économique (transport payé par l'acheteur) :
     * - L'acheteur règle "sous-total + frais de transport" au paiement.
     * - L'escrow du vendeur ne contient que le sous-total (order.total_amount).
     * - À la distribution, on débite l'escrow du sous-total, on prélève la
     *   commission plateforme, et le vendeur reçoit le reste (sous-total net).
     * - Les frais de transport (payés par l'acheteur en sus) ne transitent pas
     *   par l'escrow et ne sont donc pas déduits de la part du vendeur.
     *
     * Persistance : une Distribution par part (vendeur, commission), chacune
     * liée à sa transaction, pour l'audit.
     *
     * @throws \DomainException si le wallet pending du vendeur est introuvable
     *                          ou insuffisant (la transaction est alors rollbackée).
     * @return array Détail de la distribution
     */
    public function distributeFunds(Order $order): array
    {
        $commissionPercent = (float) (DB::table('settings')
            ->where('key', 'platform_commission_percentage')
            ->value('value') ?? 10);

        $totalAmount = (float) $order->total_amount;

        // Arrondi exact : commission arrondie, le vendeur reçoit le solde,
        // de sorte que seller + commission == total (aucun écart de centimes).
        $commissionAmount = round($totalAmount * ($commissionPercent / 100), 2);
        $sellerAmount = round($totalAmount - $commissionAmount, 2);

        $distribution = [
            'total_amount' => $totalAmount,
            'seller_amount' => $sellerAmount,
            'commission_amount' => $commissionAmount,
            'transport_amount' => 0,
            'commission_percent' => $commissionPercent,
            'transport_percent' => 0,
        ];

        Log::info("Distribution calculée pour commande #{$order->id}", [
            'total' => $totalAmount,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'seller_amount' => $sellerAmount,
            'currency' => $order->currency,
        ]);

        $seller = User::find($order->seller_id);
        if (!$seller) {
            throw new DomainException('Vendeur introuvable, fonds non distribués.');
        }

        $sellerPendingWallet = Wallet::where('user_id', $seller->id)
            ->where('type', 'pending')
            ->where('currency', $order->currency)
            ->first();

        if (!$sellerPendingWallet || $sellerPendingWallet->balance < $totalAmount) {
            Log::error("Solde insuffisant dans le wallet pending du vendeur pour la commande #{$order->id}", [
                'pending_balance' => $sellerPendingWallet?->balance ?? 'wallet absent',
                'required' => $totalAmount,
                'currency' => $order->currency,
            ]);
            throw new DomainException('Impossible de distribuer les fonds : solde vendeur en attente insuffisant.');
        }

        $sellerMainWallet = Wallet::firstOrCreate(
            [
                'user_id' => $seller->id,
                'type' => 'main',
                'currency' => $order->currency,
            ],
            [
                'balance' => 0,
                'status' => 'active',
                'is_active' => true,
            ]
        );

        $commissionWallet = Wallet::getEnterpriseSubWallet('commission', $order->currency);

        if (!$commissionWallet) {
            $commissionWallet = Wallet::firstOrCreate(
                [
                    'user_id' => null,
                    'type' => 'enterprise',
                    'currency' => $order->currency,
                ],
                [
                    'balance' => 0,
                    'status' => 'active',
                    'is_active' => true,
                ]
            );
        }

        $sellerPendingWallet->decrement('balance', $totalAmount);
        $sellerMainWallet->increment('balance', $sellerAmount);
        $commissionWallet->increment('balance', $commissionAmount);

        $sellerTransaction = Transaction::create([
            'transaction_id' => 'SELLER-' . strtoupper(Str::random(12)),
            'user_id' => $seller->id,
            'buyer_id' => $seller->id,
            'wallet_id' => $sellerMainWallet->id,
            'amount' => $sellerAmount,
            'currency' => $order->currency,
            'type' => 'deposit',
            'status' => 'completed',
            'payment_method' => 'wallet',
            'purpose' => 'Vente confirmée - Commande #' . $order->id . ' (Montant net après commission ' . $commissionPercent . '%)',
            'provider' => 'Wallet Transfer',
            'phone' => 'N/A',
        ]);

        $commissionTransaction = Transaction::create([
            'transaction_id' => 'COMMISSION-' . strtoupper(Str::random(12)),
            'user_id' => $seller->id,
            'buyer_id' => $order->buyer_id,
            'wallet_id' => $commissionWallet->id,
            'amount' => $commissionAmount,
            'currency' => $order->currency,
            'type' => 'deposit',
            'status' => 'completed',
            'payment_method' => 'wallet',
            'purpose' => 'Commission plateforme (' . $commissionPercent . '%) - Commande #' . $order->id,
            'provider' => 'Platform Commission',
            'phone' => 'N/A',
        ]);

        // Persistance pour l'audit : une Distribution par part, liée à la
        // transaction correspondante.
        Distribution::create([
            'transaction_id' => $sellerTransaction->id,
            'beneficiary_id' => $seller->id,
            'beneficiary_type' => 'seller',
            'amount' => $sellerAmount,
            'percentage' => (int) round(($sellerAmount / $totalAmount) * 100),
        ]);

        Distribution::create([
            'transaction_id' => $commissionTransaction->id,
            'beneficiary_id' => $commissionWallet->id,
            'beneficiary_type' => 'platform_commission',
            'amount' => $commissionAmount,
            'percentage' => (int) $commissionPercent,
        ]);

        Log::info("Distribution effectuée", [
            'seller_id' => $seller->id,
            'order_id' => $order->id,
            'total_amount' => $totalAmount,
            'seller_amount' => $sellerAmount,
            'commission_amount' => $commissionAmount,
            'currency' => $order->currency,
            'pending_balance' => $sellerPendingWallet->balance,
            'main_balance' => $sellerMainWallet->balance,
            'commission_wallet_balance' => $commissionWallet->fresh()->balance,
        ]);

        $buyerName = $order->buyer?->name ?? 'L\'acheteur';
        $seller->notifications()->create([
            'type' => 'order_delivered_confirmed',
            'title' => 'Commande confirmée reçue - Paiement distribué',
            'message' => $buyerName . ' a confirmé avoir reçu la commande #' . $order->id . '. ' .
                'Montant reçu: ' . number_format($sellerAmount, 2) . ' ' . $order->currency . ' ' .
                '(Total: ' . number_format($totalAmount, 2) . ' - ' .
                'Commission: ' . number_format($commissionAmount, 2) . ')',
            'action_url' => route('orders.show', $order->id),
        ]);

        return $distribution;
    }
}
