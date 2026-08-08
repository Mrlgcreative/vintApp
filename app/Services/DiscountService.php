<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\Item;
use App\Models\User;
use DomainException;

class DiscountService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Applique une réduction proposée par le vendeur (canal web + API).
     * Lève une DomainException pour les erreurs métier.
     */
    public function applyDiscount(
        int $itemId,
        int $buyerId,
        float $percentage,
        User $seller,
        ?int $messageId = null,
        int $expiresHours = 24
    ): Discount {
        $item = Item::findOrFail($itemId);

        if ($item->user_id !== $seller->id) {
            throw new DomainException("Vous n'êtes pas le propriétaire de cet article.");
        }

        if ($this->hasActiveDiscount($itemId, $buyerId)) {
            throw new DomainException('Une réduction est déjà active pour ce client sur cet article.');
        }

        $discount = new Discount([
            'item_id' => $itemId,
            'user_id' => $buyerId,
            'seller_id' => $seller->id,
            'message_id' => $messageId,
            'original_price' => $item->price,
            'discount_percentage' => $percentage,
            'status' => Discount::STATUS_APPROVED,
            'expires_at' => now()->addHours($expiresHours),
            'reason' => 'Réduction appliquée par le vendeur',
        ]);

        $discount->calculateFinalPrice();
        $discount->save();

        $this->notificationService->createDiscountNotification(
            $seller->id,
            $buyerId,
            $item->name,
            $percentage,
            $item->currency_symbol . ' ' . $discount->final_price
        );

        return $discount;
    }

    /**
     * Vérifie si une réduction active existe pour un acheteur sur un article.
     */
    public function hasActiveDiscount(int $itemId, int $buyerId): bool
    {
        return Discount::where('item_id', $itemId)
            ->where('user_id', $buyerId)
            ->valid()
            ->exists();
    }

    /**
     * Réductions actives d'un acheteur pour un article (canal web + API).
     */
    public function getAvailableDiscounts(int $itemId, int $buyerId)
    {
        return Discount::where('item_id', $itemId)
            ->where('user_id', $buyerId)
            ->valid()
            ->get();
    }

    /**
     * Construit le message automatique envoyé à l'acheteur (canal web).
     */
    public function buildDiscountMessage(Discount $discount): string
    {
        $percentage = (float) $discount->discount_percentage;
        $item = $discount->item;
        $symbol = $item->currency_symbol;

        return "🎉 Bonne nouvelle ! Le vendeur vous propose une réduction de {$percentage}% sur l'article \"{$item->name}\".\n\n"
            . "Prix original: {$symbol} {$discount->original_price}\n"
            . "Prix avec réduction: {$symbol} {$discount->final_price}\n"
            . "Cette offre expire le " . $discount->expires_at->format('d/m/Y à H:i') . ".\n\n"
            . "Commandez vite pour profiter de cette offre !";
    }

    /**
     * Code HTTP associé à une erreur métier (403 non-propriétaire, 409 doublon).
     */
    public function errorStatusCode(DomainException $e): int
    {
        return match ($e->getMessage()) {
            "Vous n'êtes pas le propriétaire de cet article." => 403,
            'Une réduction est déjà active pour ce client sur cet article.' => 409,
            default => 400,
        };
    }
}
