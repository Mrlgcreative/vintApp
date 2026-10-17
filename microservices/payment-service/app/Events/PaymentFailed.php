<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Signalé quand un paiement passe en `failed`.
 *
 * L'événement durable est écrit par WebhookProcessor dans l'outbox, dans la
 * même transaction. Voir PaymentCompleted.
 */
class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Payment $payment) {}
}
