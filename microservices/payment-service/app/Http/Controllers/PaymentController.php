<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Refund;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentController extends ApiController
{
    /**
     * POST /v1/payments
     *
     * Enregistre une intention de paiement. L'identité vient de auth-service
     * (middleware), jamais d'un user_id fourni par le client.
     */
    public function store(Request $request): JsonResponse
    {
        $identity = $request->attributes->get('vintapp_identity');

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'method' => ['required', 'string', 'in:'.implode(',', array_keys(config('payments.providers')))],
            'order_id' => ['nullable', 'integer'],
            'wallet_id' => ['nullable', 'integer'],
            'seller_id' => ['nullable', 'integer'],
            'designation' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:32'],
            'reference' => ['nullable', 'string', 'max:64'],
            'metadata' => ['nullable', 'array'],
        ]);

        $payment = Payment::create([
            'user_id' => $identity['user_id'],
            'buyer_id' => $identity['user_id'],
            'seller_id' => $data['seller_id'] ?? null,
            'order_id' => $data['order_id'] ?? null,
            'wallet_id' => $data['wallet_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'method' => $data['method'],
            'provider_key' => $data['method'],
            'status' => 'pending',
            'designation' => $data['designation'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
            'reference' => $data['reference'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'ip_address' => $request->ip(),
            'initiated_at' => now(),
        ]);

        return $this->successResponse(
            ['payment' => $this->paymentPayload($payment)],
            'Paiement enregistré',
            [],
            201
        );
    }

    public function show(Request $request, string $publicId): JsonResponse
    {
        $identity = $request->attributes->get('vintapp_identity');

        $payment = Payment::where('public_id', $publicId)->first();

        // Un utilisateur ne voit que ses paiements ; un admin voit tout.
        if (! $payment) {
            return $this->errorResponse('Paiement introuvable.', 404);
        }

        if (! in_array('admin', $identity['roles'] ?? [], true) && $payment->user_id !== $identity['user_id']) {
            return $this->errorResponse('Paiement introuvable.', 404);
        }

        return $this->successResponse(['payment' => $this->paymentPayload($payment)]);
    }

    public function index(Request $request): JsonResponse
    {
        $identity = $request->attributes->get('vintapp_identity');

        $query = Payment::query();

        if (! in_array('admin', $identity['roles'] ?? [], true)) {
            $query->where('user_id', $identity['user_id']);
        }

        $payments = $query->latest()
            ->paginate(min((int) $request->input('per_page', 20), 100));

        return $this->successResponse(
            ['payments' => array_map(fn (Payment $p) => $this->paymentPayload($p), $payments->items())],
            'OK',
            [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ]
        );
    }

    /**
     * POST /v1/payments/{payment}/refund
     */
    public function refund(Request $request, string $publicId): JsonResponse
    {
        $identity = $request->attributes->get('vintapp_identity');

        if (! in_array('admin', $identity['roles'] ?? [], true)) {
            return $this->errorResponse('Réservé aux administrateurs.', 403);
        }

        $payment = Payment::where('public_id', $publicId)->first();

        if (! $payment) {
            return $this->errorResponse('Paiement introuvable.', 404);
        }

        if ($payment->status !== 'completed') {
            throw ValidationException::withMessages([
                'payment' => 'Seul un paiement terminé peut être remboursé.',
            ]);
        }

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['amount'] > $payment->amount) {
            throw ValidationException::withMessages([
                'amount' => 'Le remboursement ne peut pas dépasser le montant payé.',
            ]);
        }

        $refund = DB::transaction(function () use ($payment, $data) {
            $refund = Refund::create([
                'payment_id' => $payment->id,
                'amount' => $data['amount'],
                'currency' => $payment->currency,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
            ]);

            $payment->forceFill(['status' => 'refunded'])->save();

            return $refund;
        });

        return $this->successResponse([
            'refund' => [
                'id' => $refund->public_id,
                'amount' => $refund->amount,
                'currency' => $refund->currency,
                'status' => $refund->status,
            ],
        ], 'Remboursement enregistré', [], 201);
    }

    private function paymentPayload(Payment $payment): array
    {
        return [
            'id' => $payment->public_id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'method' => $payment->method,
            'status' => $payment->status,
            'reference' => $payment->reference,
            'external_reference' => $payment->external_reference,
            'order_id' => $payment->order_id,
            'initiated_at' => $payment->initiated_at,
            'paid_at' => $payment->paid_at,
        ];
    }
}
