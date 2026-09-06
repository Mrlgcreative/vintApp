<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PaymentCallback;
use App\Models\Transaction;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Services\KPay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Webhooks K-PAY.
 *
 * K-PAY POST à l'URL configurée dans le dashboard à chaque changement de
 * statut (paiement, retrait, remboursement). Le payload est le même quelque
 * soit le type, la nature de l'évènement étant donnée par le champ `event` :
 *   - payment.*  → dépôt (encaissement),   ex: payment.completed
 *   - payout.*   → retrait (décaissement), ex: payout.failed
 *   - refund.*   → remboursement
 *
 * Sécurité : header `X-KPAY-Signature` = HMAC-SHA256 (hex) calculé sur le body
 * RAW reçu avec le webhook secret. Toujours vérifier AVANT de traiter.
 *
 * URLs à configurer côté dashboard K-PAY :
 *  - Deposits    : /api/v1/kpay/callback/deposit
 *  - Withdrawals : /api/v1/kpay/callback/withdraw
 *  - Refunds     : /api/v1/kpay/callback/refund
 *  - Générique   : /api/v1/kpay/callback
 *
 * Doc : https://kpay.site/documentation/webhooks
 */
class KPayCallbackController extends Controller
{
    /**
     * Point d'entrée dépôt (event payment.*).
     */
    public function deposit(Request $request): JsonResponse
    {
        return $this->handle($request);
    }

    /**
     * Point d'entrée retrait (event payout.*).
     */
    public function withdraw(Request $request): JsonResponse
    {
        return $this->handle($request);
    }

    /**
     * Point d'entrée remboursement (event refund.*).
     */
    public function refund(Request $request): JsonResponse
    {
        return $this->handle($request);
    }

    /**
     * Point d'entrée générique — le type est déduit de l'event dans le payload.
     */
    public function handle(Request $request): JsonResponse
    {
        $kpay = new KPay();
        $payload = $request->json()->all() ?: [];
        $event = $request->header('X-KPAY-Event', (string) ($payload['event'] ?? ''));
        $paymentId = $payload['paymentId'] ?? null;
        $status = $kpay->mapStatus((string) ($payload['status'] ?? ''));

        Log::info("K-PAY webhook reçu", [
            'event' => $event,
            'payment_id' => $paymentId,
            'status' => $status,
            'ip' => $request->ip(),
        ]);

        if (!$paymentId) {
            Log::warning('K-PAY webhook: paymentId manquant', [
                'event' => $event,
                'payload' => $payload,
            ]);

            return $this->ack(false, 'paymentId manquant', 400);
        }

        // 1. Vérification de signature (obligatoire)
        if (!$kpay->verifyWebhookSignature($request)) {
            Log::warning('K-PAY webhook: signature invalide ou webhook_secret non configuré', [
                'event' => $event,
                'payment_id' => $paymentId,
                'ip' => $request->ip(),
            ]);

            return $this->ack(false, 'Signature invalide', 403);
        }

        // 2. Idempotence : ne pas retraiter un event déjà traité.
        // K-PAY rediffuse les webhooks tant qu'il ne reçoit pas 200 ; on renvoie
        // donc 200 même si déjà traité, sans recommencer le traitement.
        $alreadyProcessed = PaymentCallback::where('provider', 'kpay')
            ->where('callback_type', 'kpay_' . $event)
            ->where('external_transaction_id', $paymentId)
            ->where('is_processed', true)
            ->exists();

        if ($alreadyProcessed) {
            Log::info('K-PAY webhook déjà traité (idempotent)', [
                'event' => $event,
                'payment_id' => $paymentId,
            ]);

            return $this->ack();
        }

        // 3. Enregistrer le callback brut pour audit
        $callback = PaymentCallback::create([
            'provider' => 'kpay',
            'callback_type' => 'kpay_' . $event,
            'external_transaction_id' => $paymentId,
            'status' => $status,
            'amount' => $payload['amount'] ?? 0,
            'currency' => $payload['currency'] ?? null,
            'phone_number' => $payload['phoneNumber'] ?? null,
            'raw_payload' => json_encode($payload),
            'parsed_data' => $payload,
            'signature' => $request->header('X-KPAY-Signature'),
            'ip_address' => $request->ip(),
            'is_verified' => true,
            'is_processed' => false,
        ]);

        // 4. Traiter selon l'évènement
        try {
            if (str_starts_with($event, 'payout.')) {
                $this->processPayout($paymentId, $status, $payload, $callback);
            } elseif (str_starts_with($event, 'refund.')) {
                $this->processRefund($paymentId, $status, $payload, $callback);
            } else {
                // payment.* (et tout event non reconnu) → dépôt
                $this->processDeposit($paymentId, $status, $payload, $callback);
            }

            $callback->markAsProcessed();
        } catch (\Throwable $e) {
            $callback->recordError($e->getMessage());

            Log::error('K-PAY webhook: erreur traitement', [
                'event' => $event,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // On ack 200 pour stopper les retries K-PAY (sinon boucle infinie).
            return $this->ack();
        }

        return $this->ack();
    }

    /**
     * Dépôt finalisé (encaissement d'un paiement client).
     * Rapproche via transaction_ref = paymentId.
     */
    protected function processDeposit(string $paymentId, string $status, array $payload, PaymentCallback $callback): void
    {
        $transaction = Transaction::where('provider', 'kpay')
            ->where(fn ($q) => $q->where('transaction_ref', $paymentId)
                ->orWhere('metadata', 'like', '%' . $payload['externalId'] . '%'))
            ->first();

        if (!$transaction) {
            Log::warning('K-PAY deposit: transaction introuvable', [
                'paymentId' => $paymentId,
                'externalId' => $payload['externalId'] ?? null,
            ]);

            return;
        }

        $callback->update(['transaction_id' => $transaction->id]);

        // Ne pas re-finaliser une transaction déjà à terme.
        if (in_array($transaction->status, [Transaction::STATUS_COMPLETED, Transaction::STATUS_FAILED], true)) {
            return;
        }

        // Mettre à jour le statut + conserver le payload reçu
        $existingMeta = is_string($transaction->metadata)
            ? (json_decode($transaction->metadata ?? '{}', true) ?: [])
            : ($transaction->metadata ?? []);
        $existingMeta['kpay_callback'] = $payload;

        $transaction->update([
            'status' => $status,
            'metadata' => json_encode($existingMeta),
        ]);

        // Création des commandes à partir du panier stocké dans la transaction
        if ($status === Transaction::STATUS_COMPLETED) {
            create_orders_from_transaction($transaction->fresh());
        }
    }

    /**
     * Retrait finalisé (décaissement / retrait wallet vers mobile money).
     * Rapproche via provider_reference = paymentId.
     */
    protected function processPayout(string $paymentId, string $status, array $payload, PaymentCallback $callback): void
    {
        $withdrawal = WithdrawalRequest::where('provider_reference', $paymentId)->first();

        // Fallback : rapprochement par externalId (la WithdrawalRequest n'a pas
        // de colonne dédiée, on cherche dans provider_response).
        if (!$withdrawal && !empty($payload['externalId'])) {
            $withdrawal = WithdrawalRequest::where('provider_response', 'like', '%' . $payload['externalId'] . '%')
                ->whereIn('status', ['pending', 'processing'])
                ->first();
        }

        if (!$withdrawal) {
            Log::warning('K-PAY payout: retrait introuvable', [
                'paymentId' => $paymentId,
                'externalId' => $payload['externalId'] ?? null,
            ]);

            return;
        }

        // Ne pas re-finaliser un retrait déjà traité.
        if ($withdrawal->isCompleted() || $withdrawal->isFailed()) {
            return;
        }

        $providerResponse = ['kpay_callback' => $payload];

        if ($status === 'completed') {
            $withdrawal->markAsCompleted($providerResponse);
        } elseif ($status === 'failed') {
            $reason = $payload['failureReason']
                ?? $payload['failure_reason']
                ?? 'Échec du décaissement K-PAY';
            $withdrawal->markAsFailed($reason, $providerResponse);

            // Rembourser le wallet de façon idempotente (une seule fois)
            $this->refundFailedWithdrawal($withdrawal);
        }
        // Statuts intermédiaires (pending/processing) → on ne fait rien.
    }

    /**
     * Remboursement finalisé. Rapproche la transaction d'origine via paymentId
     * ou externalId puis la marque comme remboursée.
     */
    protected function processRefund(string $paymentId, string $status, array $payload, PaymentCallback $callback): void
    {
        $externalId = $payload['externalId'] ?? null;

        $transaction = Transaction::where('provider', 'kpay')
            ->where(fn ($q) => $q->where('transaction_ref', $paymentId)
                ->orWhere('transaction_ref', $externalId))
            ->first();

        if ($transaction) {
            $callback->update(['transaction_id' => $transaction->id]);

            if ($status === 'completed') {
                $transaction->update(['status' => Transaction::STATUS_REFUNDED]);
            }
        } else {
            Log::warning('K-PAY refund: transaction d\'origine introuvable', [
                'paymentId' => $paymentId,
                'externalId' => $externalId,
            ]);
        }
    }

    /**
     * Recrédite le wallet lorsqu'un retrait a échoué (idempotent).
     * Réplique la logique de WalletController::handleWithdrawalWebhook.
     */
    protected function refundFailedWithdrawal(WithdrawalRequest $withdrawal): void
    {
        $transaction = $withdrawal->walletTransaction;

        if (!$transaction) {
            Log::warning('K-PAY payout: transaction wallet introuvable pour remboursement', [
                'withdrawal_id' => $withdrawal->id,
            ]);

            return;
        }

        $refundRef = 'REFUND-WTH-' . $transaction->id;
        $alreadyRefunded = WalletTransaction::where('reference', $refundRef)
            ->where('type', 'credit')
            ->exists();

        if ($alreadyRefunded) {
            Log::warning('K-PAY payout: remboursement déjà effectué (idempotent)', [
                'transaction_id' => $transaction->id,
                'refund_ref' => $refundRef,
            ]);

            return;
        }

        DB::transaction(function () use ($transaction, $refundRef) {
            $wallet = $transaction->wallet;
            $wallet->increment('balance', $transaction->amount);

            $wallet->transactions()->create([
                'type' => 'credit',
                'amount' => $transaction->amount,
                'balance_after' => $wallet->fresh()->balance,
                'description' => 'Remboursement suite à échec de retrait K-PAY - Ref: ' . $refundRef,
                'reference' => $refundRef,
                'status' => 'completed',
            ]);
        });

        Log::info('K-PAY payout: wallet recrédité après échec', [
            'transaction_id' => $transaction->id,
            'amount' => $transaction->amount,
        ]);
    }

    /**
     * Réponse d'accusé de réception envoyée à K-PAY.
     * K-PAY attend un 200 pour stopper les retries.
     */
    protected function ack(bool $success = true, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json(['success' => $success, 'message' => $message], $code);
    }
}