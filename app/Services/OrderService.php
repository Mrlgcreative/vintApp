<?php

namespace App\Services;

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
     * Marque une commande pending comme confirmée.
     */
    public function confirmPayment(Order $order): void
    {
        if ($order->status !== 'pending') {
            throw new DomainException('Cette commande ne peut plus être confirmée.');
        }

        $order->paid_at = now();
        $order->status = 'confirmed';
        $order->save();
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
    public function confirmDelivery(Order $order, ?string $note = null): void
    {
        if (!in_array($order->status, ['shipped', 'delivered'])) {
            throw new DomainException("Cette commande n'est pas encore expédiée.");
        }

        if ($order->confirmed_by_buyer_at) {
            throw new DomainException('Vous avez déjà confirmé la réception de cette commande.');
        }

        DB::transaction(function () use ($order, $note) {
            $order->confirmed_by_buyer_at = now();
            $order->buyer_confirmation_note = $note;
            $order->status = 'completed';
            $order->save();

            $this->distributeFunds($order);
        });
    }

    /**
     * Distribue le montant total d'une commande après confirmation de réception :
     * debite le wallet pending du vendeur, crédite son wallet main et les
     * sous-wallets entreprise (commission + transport), puis crée les transactions.
     *
     * @return array Détail de la distribution
     */
    public function distributeFunds(Order $order): array
    {
        $commissionPercent = (float) (DB::table('settings')
            ->where('key', 'platform_commission_percentage')
            ->value('value') ?? 10);

        $transportPercent = (float) (DB::table('settings')
            ->where('key', 'transport_fee_percentage')
            ->value('value') ?? 5);

        $totalAmount = (float) $order->total_amount;
        $commissionAmount = round($totalAmount * ($commissionPercent / 100), 2);
        $transportAmount = round($totalAmount * ($transportPercent / 100), 2);
        $sellerAmount = $totalAmount - $commissionAmount - $transportAmount;

        $distribution = [
            'total_amount' => $totalAmount,
            'seller_amount' => $sellerAmount,
            'commission_amount' => $commissionAmount,
            'transport_amount' => $transportAmount,
            'commission_percent' => $commissionPercent,
            'transport_percent' => $transportPercent,
        ];

        Log::info("Distribution calculée pour commande #{$order->id}", [
            'total' => $totalAmount,
            'commission_percent' => $commissionPercent,
            'commission_amount' => $commissionAmount,
            'transport_percent' => $transportPercent,
            'transport_amount' => $transportAmount,
            'seller_amount' => $sellerAmount,
            'currency' => $order->currency,
        ]);

        $seller = User::find($order->seller_id);
        if (!$seller) {
            return $distribution;
        }

        $sellerPendingWallet = Wallet::where('user_id', $seller->id)
            ->where('type', 'pending')
            ->where('currency', $order->currency)
            ->first();

        if (!$sellerPendingWallet || $sellerPendingWallet->balance < $order->total_amount) {
            Log::warning("Solde insuffisant dans le wallet pending pour la commande #{$order->id}");
            return $distribution;
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
        $transportWallet = Wallet::getEnterpriseSubWallet('transport', $order->currency);

        if (!$commissionWallet || !$transportWallet) {
            $enterpriseWallet = Wallet::firstOrCreate(
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
            $commissionWallet = $commissionWallet ?: $enterpriseWallet;
            $transportWallet = $transportWallet ?: $enterpriseWallet;
        }

        $sellerPendingWallet->decrement('balance', $totalAmount);
        $sellerMainWallet->increment('balance', $sellerAmount);
        $commissionWallet->increment('balance', $commissionAmount);
        $transportWallet->increment('balance', $transportAmount);

        Log::info("Distribution effectuée", [
            'seller_id' => $seller->id,
            'order_id' => $order->id,
            'total_amount' => $totalAmount,
            'seller_amount' => $sellerAmount,
            'commission_amount' => $commissionAmount,
            'transport_amount' => $transportAmount,
            'currency' => $order->currency,
            'pending_balance' => $sellerPendingWallet->balance,
            'main_balance' => $sellerMainWallet->balance,
            'commission_wallet_balance' => $commissionWallet->fresh()->balance,
            'transport_wallet_balance' => $transportWallet->fresh()->balance,
        ]);

        Transaction::create([
            'transaction_id' => 'SELLER-' . strtoupper(Str::random(12)),
            'user_id' => $seller->id,
            'buyer_id' => $seller->id,
            'wallet_id' => $sellerMainWallet->id,
            'amount' => $sellerAmount,
            'currency' => $order->currency,
            'type' => 'deposit',
            'status' => 'completed',
            'payment_method' => 'wallet',
            'purpose' => 'Vente confirmée - Commande #' . $order->id . ' (Montant net après commission ' . $commissionPercent . '% et transport ' . $transportPercent . '%)',
            'provider' => 'Wallet Transfer',
            'phone' => 'N/A',
        ]);

        Transaction::create([
            'transaction_id' => 'COMMISSION-' . strtoupper(Str::random(12)),
            'user_id' => 1,
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

        Transaction::create([
            'transaction_id' => 'TRANSPORT-' . strtoupper(Str::random(12)),
            'user_id' => 1,
            'buyer_id' => $order->buyer_id,
            'wallet_id' => $transportWallet->id,
            'amount' => $transportAmount,
            'currency' => $order->currency,
            'type' => 'deposit',
            'status' => 'completed',
            'payment_method' => 'wallet',
            'purpose' => 'Frais de transport (' . $transportPercent . '%) - Commande #' . $order->id,
            'provider' => 'Transport Fee',
            'phone' => 'N/A',
        ]);

        $buyerName = $order->buyer?->name ?? 'L\'acheteur';
        $seller->notifications()->create([
            'type' => 'order_delivered_confirmed',
            'title' => 'Commande confirmée reçue - Paiement distribué',
            'message' => $buyerName . ' a confirmé avoir reçu la commande #' . $order->id . '. ' .
                'Montant reçu: ' . number_format($sellerAmount, 2) . ' ' . $order->currency . ' ' .
                '(Total: ' . number_format($totalAmount, 2) . ' - ' .
                'Commission: ' . number_format($commissionAmount, 2) . ' - ' .
                'Transport: ' . number_format($transportAmount, 2) . ')',
            'action_url' => route('orders.show', $order->id),
            'is_read' => false,
        ]);

        return $distribution;
    }
}
