<?php

namespace Tests;

use App\Models\Payment;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Paiement en attente, prêt à être soldé par un webhook.
     */
    protected function pendingPayment(array $attributes = []): Payment
    {
        return Payment::create(array_merge([
            'user_id' => 1,
            'buyer_id' => 1,
            'amount' => 5000,
            'currency' => 'USD',
            'method' => 'mpesa',
            'provider_key' => 'mpesa',
            'status' => 'pending',
            'reference' => 'REF-'.uniqid(),
            'initiated_at' => now(),
        ], $attributes));
    }

    /**
     * Signature HMAC conforme à ce qu'attend l'opérateur.
     */
    protected function hmac(array $payload, string $secret): string
    {
        return hash_hmac('sha256', json_encode($payload), $secret);
    }
}
