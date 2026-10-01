<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Signalé quand un paiement passe en `completed`.
 *
 * L'événement durable est écrit par WebhookProcessor dans l'outbox, dans la
 * même transaction. Cette classe n'est que le signal local (logs, metrics) :
 * l'ignorer ne perd rien, contrairement à une publication directe.
 *
 * order-service, wallet-service et authenticity-service réagissent au message
 * du bus `vintapp.payment`, pas à cet objet.
 */
class PaymentCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Payment $payment) {}
}
