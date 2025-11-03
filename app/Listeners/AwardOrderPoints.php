<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Log;

class AwardOrderPoints
{
    protected AffiliateService $affiliateService;

    /**
     * Create the event listener.
     */
    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        try {
            $order = $event->order;
            $buyer = $event->buyer;
            $seller = $event->seller;

            // Points pour l'acheteur (2% du montant de la commande)
            $buyerTransaction = $this->affiliateService->awardPoints($buyer, 'purchase', [
                'amount' => $order->total_amount,
                'percentage' => 2.0
            ]);

            // Points pour le vendeur (1% du montant de la vente)
            $sellerTransaction = $this->affiliateService->awardPoints($seller, 'sale', [
                'amount' => $order->total_amount,
                'percentage' => 1.0
            ]);

            // Vérifier si c'est le premier achat (bonus supplémentaire)
            $firstPurchaseTransaction = $this->affiliateService->awardPoints($buyer, 'first_purchase', [
                'amount' => $order->total_amount
            ]);

            // Vérifier et compléter les parrainages éligibles
            $this->affiliateService->checkReferralCompletion($buyer);
            $this->affiliateService->checkReferralCompletion($seller);

            Log::info('Points attribués pour commande complétée', [
                'order_id' => $order->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'buyer_points' => $buyerTransaction ? $buyerTransaction->amount : 0,
                'seller_points' => $sellerTransaction ? $sellerTransaction->amount : 0,
                'first_purchase_bonus' => $firstPurchaseTransaction ? $firstPurchaseTransaction->amount : 0,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'attribution des points de commande', [
                'order_id' => $event->order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}