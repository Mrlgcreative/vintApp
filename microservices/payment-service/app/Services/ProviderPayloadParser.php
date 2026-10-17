<?php

namespace App\Services;

use Illuminate\Http\Request;

/**
 * Traduit le payload brut d'un opérateur en données normalisées.
 *
 * Les noms de champs varient d'un opérateur à l'autre et parfois d'une
 * version de l'API à l'autre ; chaque adaptateur accepte les alias
 * rencontrés dans le monolithe.
 */
class ProviderPayloadParser
{
    /**
     * @return array{transaction_id: ?string, reference: ?string, status: string, amount: ?int, currency: string, phone_number: ?string, message: ?string, event_kind?: string}|null
     */
    public function parse(Request $request, string $provider): ?array
    {
        $parsed = match ($provider) {
            'mpesa' => $this->mpesa($request),
            'orange_money' => $this->orangeMoney($request),
            'airtel_money' => $this->airtelMoney($request),
            'africell' => $this->africell($request),
            'cinetpay' => $this->cinetPay($request),
            'maishapay' => $this->maishaPay($request),
            'pawapay' => $this->generic($request, $this->pawapayStatus(...)),
            'afribapay' => $this->generic($request, $this->afribaPayStatus(...)),
            'kpay' => $this->kpay($request),
            default => null,
        };

        if ($parsed === null) {
            return null;
        }

        // Sans transaction_id ni reference, le callback ne peut pas être
        // rattaché à un paiement : on le refuse plutôt que de deviner.
        if (($parsed['transaction_id'] ?? null) === null && ($parsed['reference'] ?? null) === null) {
            return null;
        }

        $parsed['amount'] = isset($parsed['amount']) ? (int) $parsed['amount'] : null;

        return $parsed;
    }

    private function mpesa(Request $request): array
    {
        return [
            'transaction_id' => $request->input('TransID') ?? $request->input('transaction_id'),
            'reference' => $request->input('BillRefNumber') ?? $request->input('AccountReference'),
            'status' => $this->matchStatus((string) $request->input('ResultCode'), [
                '0,SUCCESS' => 'success',
                'PENDING' => 'pending',
                'CANCELLED' => 'cancelled',
            ]),
            'amount' => $request->input('TransAmount') ?? $request->input('amount'),
            'currency' => (string) ($request->input('Currency') ?? 'USD'),
            'phone_number' => $request->input('MSISDN') ?? $request->input('phone'),
            'message' => $request->input('ResultDesc'),
        ];
    }

    private function orangeMoney(Request $request): array
    {
        return [
            'transaction_id' => $request->input('txnid') ?? $request->input('transaction_id'),
            'reference' => $request->input('order_id') ?? $request->input('reference'),
            'status' => $this->matchStatus((string) $request->input('status'), [
                'SUCCESS,SUCCESSFUL,COMPLETED' => 'success',
                'PENDING,PROCESSING' => 'pending',
                'CANCELLED,CANCELED' => 'cancelled',
            ]),
            'amount' => $request->input('amount'),
            'currency' => (string) ($request->input('currency') ?? 'USD'),
            'phone_number' => $request->input('msisdn') ?? $request->input('phone'),
            'message' => $request->input('message'),
        ];
    }

    private function airtelMoney(Request $request): array
    {
        return [
            'transaction_id' => $request->input('transaction_id') ?? $request->input('airtel_money_id'),
            'reference' => $request->input('reference') ?? $request->input('ext_txn_id'),
            'status' => $this->matchStatus((string) $request->input('transaction_status'), [
                'TS,SUCCESS,SUCCESSFUL' => 'success',
                'TIP,PENDING' => 'pending',
            ]),
            'amount' => $request->input('transaction_amount'),
            'currency' => (string) ($request->input('currency') ?? 'USD'),
            'phone_number' => $request->input('msisdn'),
            'message' => $request->input('status_message'),
        ];
    }

    private function africell(Request $request): array
    {
        return [
            'transaction_id' => $request->input('trans_id'),
            'reference' => $request->input('ref') ?? $request->input('order_id'),
            'status' => $this->matchStatus((string) $request->input('status'), [
                'SUCCESS,SUCCESSFUL' => 'success',
                'PENDING' => 'pending',
            ]),
            'amount' => $request->input('amount'),
            'currency' => 'USD',
            'phone_number' => $request->input('phone'),
            'message' => $request->input('msg'),
        ];
    }

    private function cinetPay(Request $request): array
    {
        // CinetPay notifie toujours un succès de notification ; c'est la clé et
        // le montant qui prouvent que la notification est authentique.
        return [
            'transaction_id' => $request->input('TransactionId') ?? $request->input('transaction_id'),
            'reference' => $request->input('Reference') ?? $request->input('reference'),
            'status' => 'success',
            'amount' => $request->input('Amount'),
            'currency' => (string) ($request->input('Currency') ?? 'USD'),
            'phone_number' => $request->input('CustomerPhone') ?? $request->input('phone'),
            'message' => $request->input('Designation'),
        ];
    }

    private function maishaPay(Request $request): array
    {
        $data = $request->all();

        return [
            'transaction_id' => $data['originatingTransactionId']
                ?? $data['transactionId']
                ?? $data['transactionReference']
                ?? $request->input('transaction_id'),
            'reference' => $data['originatingTransactionId'] ?? $data['order']['reference'] ?? null,
            'status' => $this->matchStatus((string) ($data['transactionStatus'] ?? $data['status'] ?? ''), [
                'SUCCESS,SUCCESSFUL,COMPLETED,APPROVED' => 'success',
                'PENDING' => 'pending',
                'CANCELLED,CANCELED' => 'cancelled',
            ]),
            'amount' => $data['order']['cost']['amount']
                ?? $data['order']['amount']
                ?? $request->input('amount'),
            'currency' => (string) ($data['order']['cost']['currency']
                ?? $data['order']['currency']
                ?? $request->input('currency', 'CDF')),
            'phone_number' => $data['paymentChannel']['walletID'] ?? $request->input('walletID'),
            'message' => $data['transactionDescription'] ?? null,
        ];
    }

    private function pawapayStatus(string $value): string
    {
        return $this->matchStatus($value, [
            'SUCCESS' => 'success',
            'PENDING' => 'pending',
            'FAILED,CANCELLED' => 'cancelled',
        ]);
    }

    private function afribaPayStatus(string $value): string
    {
        return $this->matchStatus($value, [
            'SUCCESS,SUCCEEDED' => 'success',
            'PENDING' => 'pending',
            'FAILED,CANCELLED' => 'cancelled',
        ]);
    }

    /**
     * K-PAY envoie tous ses événements sur la même URL, le type étant porté
     * par `event` (payment.*, payout.*, refund.*). Seuls les dépôts concernent
     * ce service ; `event_kind` permet au contrôleur d'ignorer le reste sans
     * faire d'effet.
     */
    private function kpay(Request $request): array
    {
        $data = $request->all();
        $event = (string) ($request->header('X-KPAY-Event') ?? $data['event'] ?? '');

        return [
            'transaction_id' => $data['paymentId'] ?? $data['id'] ?? null,
            'reference' => $data['externalId'] ?? $data['reference'] ?? null,
            'status' => $this->kpayStatus((string) ($data['status'] ?? '')),
            'amount' => $data['amount'] ?? null,
            'currency' => (string) ($data['currency'] ?? 'CDF'),
            'phone_number' => $data['phoneNumber'] ?? $data['phone'] ?? null,
            'message' => $data['message'] ?? $data['failureReason'] ?? $data['failure_reason'] ?? null,
            'event_kind' => $this->kpayEventKind($event),
        ];
    }

    private function kpayStatus(string $value): string
    {
        return $this->matchStatus($value, [
            'COMPLETED,SUCCESS' => 'success',
            'PENDING,PROCESSING' => 'pending',
            'FAILED,REJECTED' => 'failed',
            'CANCELLED,CANCELED' => 'cancelled',
        ]);
    }

    /**
     * @return 'payment'|'payout'|'refund'
     */
    private function kpayEventKind(string $event): string
    {
        return match (true) {
            str_starts_with($event, 'payout.') => 'payout',
            str_starts_with($event, 'refund.') => 'refund',
            default => 'payment',
        };
    }

    private function generic(Request $request, callable $statusMapper): array
    {
        return [
            'transaction_id' => $request->input('transaction_id') ?? $request->input('reference'),
            'reference' => $request->input('reference') ?? $request->input('order_id'),
            'status' => $statusMapper((string) $request->input('status')),
            'amount' => $request->input('amount'),
            'currency' => (string) ($request->input('currency') ?? 'USD'),
            'phone_number' => $request->input('phone') ?? $request->input('msisdn'),
            'message' => $request->input('message'),
        ];
    }

    /**
     * Les opérateurs utilisent plusieurs libellés pour un même état ; chaque
     * entrée associe des alias (séparés par une virgule) à un état interne.
     *
     * @param  array<string, string>  $map
     */
    private function matchStatus(string $value, array $map): string
    {
        $normalized = strtoupper(trim($value));

        foreach ($map as $aliases => $status) {
            foreach (explode(',', $aliases) as $alias) {
                if (strtoupper(trim($alias)) === $normalized) {
                    return $status;
                }
            }
        }

        return 'failed';
    }
}
