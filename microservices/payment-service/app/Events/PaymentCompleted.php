<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Émis quand un paiement est confirmé par un webhook opérateur authentifié.
 *
 * order-service, wallet-service et authenticity-service réagissent à cet
 * événement. payment-service ne touche jamais leurs tables.
 */
class PaymentCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Payment $payment) {}
}
