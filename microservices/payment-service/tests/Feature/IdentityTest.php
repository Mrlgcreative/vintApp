<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * payment-service ne possède pas la table users : toute identité vient
 * d'un appel à auth-service, et l'échec doit être closed.
 */
class IdentityTest extends TestCase
{
    use RefreshDatabase;

    private function configureAuth(string $secret = 'secret-service'): void
    {
        config()->set('payments.auth_service.url', 'http://auth.test');
        config()->set('payments.auth_service.shared_secret', $secret);
    }

    public function test_api_refused_when_auth_service_not_configured(): void
    {
        // Ni URL ni secret : fail-closed, pas de mode dégradé.
        config()->set('payments.auth_service.url', '');
        config()->set('payments.auth_service.shared_secret', null);

        $this->withToken('peu-importe')->getJson('/v1/payments')->assertStatus(503);
    }

    public function test_api_refused_without_token(): void
    {
        $this->configureAuth();

        $this->getJson('/v1/payments')->assertStatus(401);
    }

    public function test_api_refused_when_token_is_rejected_by_auth_service(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response(['success' => false], 401)]);

        $this->withToken('token-invalide')->getJson('/v1/payments')->assertStatus(401);
    }

    public function test_api_refused_when_auth_service_is_unreachable(): void
    {
        $this->configureAuth();

        Http::fake(fn () => throw new ConnectionException('connexion refusée'));

        $this->withToken('token-valide')->getJson('/v1/payments')->assertStatus(503);
    }

    public function test_api_refused_when_auth_service_errors(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response(['boom' => true], 500)]);

        $this->withToken('token-valide')->getJson('/v1/payments')->assertStatus(503);
    }

    public function test_api_accepts_valid_token_and_scopes_to_its_owner(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response([
            'success' => true,
            'data' => ['active' => true, 'identity' => [
                'user_id' => 42,
                'roles' => ['user'],
            ]],
        ])]);

        $mien = $this->pendingPayment(['user_id' => 42]);
        $autre = $this->pendingPayment(['user_id' => 99]);

        $this->withToken('bon-token')
            ->getJson('/v1/payments')
            ->assertOk()
            ->assertJsonPath('data.payments.0.id', $mien->public_id);

        // Le paiement d'un autre utilisateur n'est pas listé.
        $this->getJson('/v1/payments')->assertJsonMissing(['id' => $autre->public_id]);
    }

    public function test_payment_creation_uses_identity_not_client_supplied_user_id(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response([
            'success' => true,
            'data' => ['active' => true, 'identity' => [
                'user_id' => 42,
                'roles' => ['user'],
            ]],
        ])]);

        // Le client tente d'imposer un user_id : il doit être ignoré.
        $this->withToken('bon-token')
            ->postJson('/v1/payments', [
                'amount' => 2500,
                'currency' => 'USD',
                'method' => 'mpesa',
                'user_id' => 999,
            ])
            ->assertCreated()
            ->assertJsonPath('data.payment.amount', 2500);

        $this->assertDatabaseHas('payments', ['user_id' => 42, 'amount' => 2500]);
        $this->assertDatabaseMissing('payments', ['user_id' => 999]);
    }

    public function test_user_cannot_read_another_users_payment(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response([
            'success' => true,
            'data' => ['active' => true, 'identity' => [
                'user_id' => 42,
                'roles' => ['user'],
            ]],
        ])]);

        $autre = $this->pendingPayment(['user_id' => 99]);

        $this->withToken('bon-token')
            ->getJson('/v1/payments/'.$autre->public_id)
            ->assertStatus(404);
    }

    public function test_refund_is_reserved_to_admins(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response([
            'success' => true,
            'data' => ['active' => true, 'identity' => [
                'user_id' => 42,
                'roles' => ['user'],
            ]],
        ])]);

        $payment = $this->pendingPayment(['user_id' => 42, 'status' => 'completed']);

        $this->withToken('bon-token')
            ->postJson('/v1/payments/'.$payment->public_id.'/refund', ['amount' => 1000])
            ->assertStatus(403);

        $this->assertDatabaseCount('refunds', 0);
    }
}
