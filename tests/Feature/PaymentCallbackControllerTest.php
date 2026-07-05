<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentCallbackControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_maishapay_transaction_status_from_external_check(): void
    {
        config()->set('services.maishapay.api_key', 'test-key');
        config()->set('services.maishapay.secret_key', 'test-secret');

        $user = User::factory()->create();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'buyer_id' => $user->id,
            'amount' => 15.00,
            'currency' => 'USD',
            'status' => 'pending',
            'provider' => 'maishapay',
            'transaction_id' => 'MP-TEST-123',
            'transaction_ref' => 'maisha-ext-ref',
            'purpose' => 'Test',
            'phone' => '0990000000',
        ]);

        Http::fake([
            'https://marchand.maishapay.online/api/payments/maisha-ext-ref/status' => Http::response([
                'data' => ['status' => 'SUCCESS'],
                'message' => 'success',
            ], 200),
        ]);

        $response = $this->getJson('/api/payment-callbacks/status?transaction_id=' . $transaction->id);

        $response->assertOk();
        $this->assertSame('completed', $transaction->fresh()->status);
    }
}
