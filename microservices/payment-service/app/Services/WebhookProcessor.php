<?php

namespace App\Services;

use App\Events\PaymentCompleted;
use App\Events\PaymentFailed;
use App\Models\Payment;
use App\Models\PaymentCallback;
use App\Models\ProcessedWebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Rattache un webhook à un paiement et déclenche les effets une seule fois.
 *
 * Deux garanties :
 * - le rattachement se fait par référence explicite, jamais par montant +
 *   téléphone. Un opérateur qui authentifie correctement ses callbacks mais
 *   envoie un payload erroné ne doit pas pouvoir solder une transaction qui
 *   n'est pas la sienne.
 * - la déduplication repose sur une clé unique : un rejeu est acquitté sans
 *   déclencher dsecond effet.
 */
class WebhookProcessor
{
    public function __construct(
        private readonly WebhookReplayGuard $replayGuard,
        private readonly OutboxWriter $outbox,
    ) {}

    /**
     * @return array{ok: bool, code: string, callback: ?PaymentCallback, payment: ?Payment}
     */
    public function process(PaymentCallback $callback): array
    {
        $parsed = $callback->parsed_data ?? [];

        $dedupeKey = $this->dedupeKey($callback->provider, $parsed);

        if ($this->replayGuard->alreadyProcessed($dedupeKey)) {
            Log::info('Webhook déjà traité, acquitté sans effet', [
                'callback_id' => $callback->id,
                'dedupe_key' => $dedupeKey,
            ]);

            return ['ok' => true, 'code' => 'already_processed', 'callback' => $callback, 'payment' => null];
        }

        return DB::transaction(function () use ($callback, $parsed, $dedupeKey) {
            $payment = $this->resolvePayment($callback, $parsed);

            if ($payment === null) {
                $callback->recordError('Aucun paiement ne correspond à cette référence.');
                $callback->markAsProcessed();

                return ['ok' => true, 'code' => 'unmatched', 'callback' => $callback, 'payment' => null];
            }

            $callback->payment_id = $payment->id;
            $callback->save();

            $this->applyStatus($payment, $callback);

            $callback->markAsProcessed();

            ProcessedWebhookEvent::create([
                'dedupe_key' => $dedupeKey,
                'provider' => $callback->provider,
                'payment_callback_id' => $callback->id,
                'processed_at' => now(),
            ]);

            return ['ok' => true, 'code' => 'processed', 'callback' => $callback, 'payment' => $payment];
        });
    }

    /**
     * Rattachement strict par référence. Un fallback par montant autoriserait
     * un opérateur à solder le paiement d'un autre client.
     */
    private function resolvePayment(PaymentCallback $callback, array $parsed): ?Payment
    {
        if (! empty($callback->external_transaction_id)) {
            $payment = Payment::where('provider_key', $callback->provider)
                ->where('external_reference', $callback->external_transaction_id)
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        if (! empty($parsed['reference'])) {
            $payment = Payment::where('provider_key', $callback->provider)
                ->where('reference', $parsed['reference'])
                ->first();

            if ($payment) {
                return $payment;
            }
        }

        return null;
    }

    private function applyStatus(Payment $payment, PaymentCallback $callback): void
    {
        $status = $callback->status;

        if ($payment->status === 'completed' && $status === 'success') {
            // Un opérateur peut notifier deux fois le même succès.
            Log::info('Paiement déjà terminé, notification ignorée', ['payment_id' => $payment->id]);

            return;
        }

        match ($status) {
            'success' => $this->markCompleted($payment, $callback),
            'failed' => $this->markFailed($payment, $callback),
            'cancelled' => $this->markCancelled($payment, $callback),
            'pending' => $payment->forceFill(['status' => 'pending'])->save(),
            default => $payment->forceFill(['status' => 'failed'])->save(),
        };
    }

    private function markCompleted(Payment $payment, PaymentCallback $callback): void
    {
        $payment->forceFill([
            'status' => 'completed',
            'external_reference' => $callback->external_transaction_id ?? $payment->external_reference,
            'paid_at' => now(),
            'error_message' => null,
        ])->save();

        // Écrit dans la transaction métier : le paiement ne peut pas être
        // completed sans que l'événement à publier existe en base.
        $this->outbox->record('payment.completed', $payment, [
            'transaction_ref' => $callback->external_transaction_id,
        ]);

        PaymentCompleted::dispatch($payment);
    }

    private function markFailed(Payment $payment, PaymentCallback $callback): void
    {
        $message = $callback->parsed_data['message'] ?? null;

        $payment->forceFill([
            'status' => 'failed',
            'error_message' => $message,
        ])->save();

        $this->outbox->record('payment.failed', $payment, [
            'reason' => $message ?? 'Statut opérateur inconnu',
        ]);

        PaymentFailed::dispatch($payment);
    }

    private function markCancelled(Payment $payment, PaymentCallback $callback): void
    {
        $payment->forceFill([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'error_message' => $callback->parsed_data['message'] ?? null,
        ])->save();
    }

    private function dedupeKey(string $provider, array $parsed): string
    {
        $parts = [
            $provider,
            $parsed['transaction_id'] ?? '',
            $parsed['reference'] ?? '',
            $parsed['status'] ?? '',
            (string) ($parsed['amount'] ?? ''),
        ];

        return hash('sha256', implode('|', $parts));
    }
}
