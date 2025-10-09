<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleConfirmed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Order $order;
    public float $amount;
    public int $sellerId;
    public string $currency;

    /**
     * Create a new event instance.
     *
     * @param Order $order L'ordre confirmé
     * @param float $amount Le montant total de la vente
     * @param int $sellerId L'ID du vendeur
     * @param string $currency La devise (USD ou CDF)
     */
    public function __construct(Order $order, float $amount, int $sellerId, string $currency = 'USD')
    {
        $this->order = $order;
        $this->amount = $amount;
        $this->sellerId = $sellerId;
        $this->currency = $currency;
    }
}

