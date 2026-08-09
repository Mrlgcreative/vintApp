<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\PaymentCallback;
use App\Models\Transaction;
use App\Models\WithdrawalRequest;
use App\Services\PawaPay;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Callbacks (webhooks) PawaPay.
 *
 * PawaPay POST à l'URL configurée dans le dashboard quand un paiement atteint
 * un statut final. Le body est la ressource du paiement (deposit/checkout/
 * payout/refund) avec son identifiant (depositId, checkoutId, payoutId,
 * refundId) et le champ `status` (COMPLETED/FAILED/...).
 *
 * 4 URLs distinctes à configurer dans le dashboard PawaPay :
 *  - Deposits  : /api/v1/pawapay/callback/deposit
 *  - Checkouts : /api/v1/pawapay/callback/checkout
 *  - Payouts   : /api/v1/pawapay/callback/payout
 *  - Refunds   : /api/v1/pawapay/callback/refund
 *
 * Doc : https://docs.pawapay.io/v2/api-reference/deposits/deposit-callback
 */
class PawaPayCallbackController extends Controller
{
    /** Map type de callback → nom du champ identifiant dans le body PawaPay. */
    private const ID_FIELDS = [
        'deposit'  => 'depositId',
        'checkout' => 'checkoutId',
        'payout'   => 'payoutId',
        'refund'   => 'refundId',
    ];

    public function deposit(Request $request): JsonResponse
    {
        return $this->handle($request, 'deposit');
    }

    public function checkout(Request $request): JsonResponse
    {
        return $this->handle($request, 'checkout');
    }

    public function payout(Request $request): JsonResponse
    {
        return $this->handle($request, 'payout');
    }

    public function refund(Request $request): JsonResponse
    {
        return $this->handle($request, 'refund');
    }

    /**
     * Point d'entrée générique : /api/v1/pawapay/callback/{type}
     * Permet de configurer une seule URL paramétrée si besoin.
     */
    public function handleTyped(Request $request, string $type): JsonResponse
    {
        $type = strtolower($type);

        if (!isset(self::ID_FIELDS[$type])) {
            return $this->ack(false, 'Type de callback inconnu', 400);
        }

        return $this->handle($request, $type);
    }

    /**
     * Traite un callback PawaPay quelque soit son type.
     */
    protected function handle(Request $request, string $type): JsonResponse
    {
        $pawaPay = new PawaPay();
        $payload = $request->json()->all() ?: [];
        $idField = self::ID_FIELDS[$type];
        $paymentId = $payload[$idField] ?? null;
        $status = strtoupper((string) ($payload['status'] ?? ''));

        Log::info("PawaPay '{$type}' callback reçu", [
            'payment_id' => $paymentId,
            'status'     => $status,
            'ip'         => $request->ip(),
        ]);

        if (!$paymentId) {
            Log::warning('PawaPay callback: identifiant manquant', [
                'type'    => $type,
                'payload' => $payload,
            ]);

            return $this->ack(false, 'Identifiant de paiement manquant', 400);
        }

        // 1. Vérification de signature (RFC-9421 si callbacks signés activés)
        if (!$this->verifySignature($request, $pawaPay)) {
            Log::warning('PawaPay callback: signature invalide', [
                'type'       => $type,
                'payment_id' => $paymentId,
                'ip'         => $request->ip(),
            ]);

            return $this->ack(false, 'Signature invalide', 403);
        }

        $internalStatus = $pawaPay->mapStatus($status);

        // 2. Idempotence : ne pas retraiter un callback déjà traité.
        // PawaPay rediffuse les callbacks tant qu'il ne reçoit pas 200 ; on
        // renvoie donc 200 même si déjà traité, sans recommencer le traitement.
        $alreadyProcessed = PaymentCallback::where('provider', 'pawapay')
            ->where('callback_type', 'pawapay_' . $type)
            ->where('external_transaction_id', $paymentId)
            ->where('is_processed', true)
            ->exists();

        if ($alreadyProcessed) {
            Log::info('PawaPay callback déjà traité (idempotent)', [
                'type'       => $type,
                'payment_id' => $paymentId,
            ]);

            return $this->ack();
        }

        // 3. Enregistrer le callback brut pour audit
        $callback = PaymentCallback::create([
            'provider'                => 'pawapay',
            'callback_type'           => 'pawapay_' . $type,
            'external_transaction_id' => $paymentId,
            'status'                  => $internalStatus,
            'amount'                  => $payload['amount'] ?? 0,
            'currency'                => $payload['currency'] ?? 'USD',
            'phone_number'            => $this->extractPhone($payload),
            'raw_payload'             => json_encode($payload),
            'parsed_data'             => $payload,
            'ip_address'              => $request->ip(),
            'is_verified'             => true,
            'is_processed'            => false,
        ]);

        // 4. Traiter selon le type
        try {
            match ($type) {
                'deposit', 'checkout' => $this->processDeposit($paymentId, $internalStatus, $payload, $callback),
                'payout'              => $this->processPayout($paymentId, $internalStatus, $payload, $callback),
                'refund'              => $this->processRefund($paymentId, $internalStatus, $payload, $callback),
            };

            $callback->markAsProcessed();
        } catch (\Throwable $e) {
            $callback->recordError($e->getMessage());

            Log::error('PawaPay callback: erreur traitement', [
                'type'       => $type,
                'payment_id' => $paymentId,
                'error'      => $e->getMessage(),
                'trace'      => $e->getTraceAsString(),
            ]);

            // On ack 200 pour stopper les retries PawaPay (sinon boucle infinie).
            return $this->ack();
        }

        return $this->ack();
    }

    /**
     * Dépôt / Checkout finalisé (encaissement d'un paiement client).
     * Rapproche via transaction_ref = depositId/checkoutId.
     */
    protected function processDeposit(string $paymentId, string $status, array $payload, PaymentCallback $callback): void
    {
        $transaction = Transaction::where('provider', 'pawapay')
            ->where('transaction_ref', $paymentId)
            ->first();

        if (!$transaction) {
            Log::warning('PawaPay deposit: transaction introuvable', [
                'depositId' => $paymentId,
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
        $existingMeta['pawapay_callback'] = $payload;

        $transaction->update([
            'status'   => $status,
            'metadata' => json_encode($existingMeta),
        ]);

        // Création des commandes à partir du panier stocké dans la transaction
        if ($status === Transaction::STATUS_COMPLETED) {
            create_orders_from_transaction($transaction->fresh());
        }
    }

    /**
     * Payout finalisé (décaissement / retrait wallet vers mobile money).
     * Rapproche via provider_reference = payoutId.
     */
    protected function processPayout(string $paymentId, string $status, array $payload, PaymentCallback $callback): void
    {
        $withdrawal = WithdrawalRequest::where('provider_reference', $paymentId)->first();

        if (!$withdrawal) {
            Log::warning('PawaPay payout: retrait introuvable', [
                'payoutId' => $paymentId,
            ]);

            return;
        }

        // Ne pas re-finaliser un retrait déjà traité.
        if ($withdrawal->isCompleted() || $withdrawal->isFailed()) {
            return;
        }

        if ($status === 'completed') {
            $withdrawal->markAsCompleted(['pawapay_callback' => $payload]);
        } elseif ($status === 'failed') {
            $reason = $payload['failureReason']['failureMessage']
                ?? $payload['failureReason']['failureCode']
                ?? 'Échec du décaissement PawaPay';
            $withdrawal->markAsFailed($reason, ['pawapay_callback' => $payload]);
        }
        // Statuts intermédiaires (pending/processing) → on ne fait rien.
    }

    /**
     * Refund finalisé (remboursement).
     * Le refundId est un nouvel identifiant ; on rapproche avec le dépôt
     * d'origine via `clientReferenceId` (qui contient généralement le
     * depositId d'origine).
     */
    protected function processRefund(string $paymentId, string $status, array $payload, PaymentCallback $callback): void
    {
        $originalRef = $payload['clientReferenceId'] ?? null;

        $transaction = $originalRef
            ? Transaction::where('provider', 'pawapay')
                ->where('transaction_ref', $originalRef)
                ->first()
            : null;

        if ($transaction) {
            $callback->update(['transaction_id' => $transaction->id]);

            if ($status === 'completed') {
                $transaction->update(['status' => Transaction::STATUS_REFUNDED]);
            }
        } else {
            Log::warning('PawaPay refund: transaction d\'origine introuvable', [
                'refundId'          => $paymentId,
                'clientReferenceId' => $originalRef,
            ]);
        }
    }

    /**
     * Vérifie la signature du callback entrant.
     *
     * - Si PawaPay envoie les headers de signature (RFC-9421), on les vérifie
     *   via PawaPay::verifyCallback() (Content-Digest + signature).
     * - Sinon (mode non signé, défaut), on accepte. La sécurité repose alors
     *   sur l'idempotence + le rapprochement avec une transaction existante.
     * - Si `signed_callbacks` est forcé dans la config, on rejette les
     *   callbacks non signés.
     */
    protected function verifySignature(Request $request, PawaPay $pawaPay): bool
    {
        $hasSignature = $request->hasHeader('Signature') && $request->hasHeader('Signature-Input');

        if (!$hasSignature) {
            return !config('services.pawapay.signed_callbacks', false);
        }

        return $pawaPay->verifyCallback($request);
    }

    /**
     * Extrait le numéro de téléphone du payer/recipient du payload.
     */
    protected function extractPhone(array $payload): string
    {
        return (string) (
            $payload['payer']['accountDetails']['phoneNumber']
            ?? $payload['recipient']['accountDetails']['phoneNumber']
            ?? ''
        );
    }

    /**
     * Réponse d'accusé de réception envoyée à PawaPay.
     * PawaPay attend un 200 pour stopper les retries.
     */
    protected function ack(bool $success = true, string $message = 'OK', int $code = 200): JsonResponse
    {
        return response()->json(['success' => $success, 'message' => $message], $code);
    }
}