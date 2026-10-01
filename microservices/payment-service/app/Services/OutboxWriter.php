<?php

namespace App\Services;

use App\Models\OutboxMessage;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Écriture des événements dans l'outbox.
 *
 * Appelé depuis WebhookProcessor, donc dans la transaction métier : le
 * changement de statut du paiement et l'événement à publier Atomicien.
 */
class OutboxWriter
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function record(string $type, Payment $payment, array $extra = []): OutboxMessage
    {
        $payload = array_merge([
            'payment_id' => $payment->id,
            'payment_public_id' => $payment->public_id,
            'order_id' => $payment->order_id,
            'wallet_id' => $payment->wallet_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'provider' => $payment->provider_key,
        ], $extra);

        // Les clés de config sont des types ('payment.completed') contenant un
        // point : une lecture par notation pointée traverserait 'payment' puis
        // chercherait 'completed'. On indexe donc le tableau explicitement.
        $streams = config('events.streams', []);

        return OutboxMessage::create([
            'event_id' => (string) Str::uuid(),
            'type' => $type,
            'stream' => is_array($streams) ? ($streams[$type] ?? 'vintapp.events') : 'vintapp.events',
            'payload' => $payload,
            'available_at' => now(),
        ]);
    }

    /**
     * Écrit l'événement en n'utilisant la transaction courante que si elle
     * existe. Utilisé par le bootstrap et les tests hors webhook.
     */
    public function recordOutsideTransaction(string $type, Payment $payment, array $extra = []): OutboxMessage
    {
        if (DB::transactionLevel() > 0) {
            return $this->record($type, $payment, $extra);
        }

        return DB::transaction(fn () => $this->record($type, $payment, $extra));
    }
}
