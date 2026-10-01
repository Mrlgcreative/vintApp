<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenIntrospectionTest extends TestCase
{
    use RefreshDatabase;

    private const SERVICE_KEY = 'test-service-secret';

    public function test_valid_token_resolves_identity(): void
    {
        $user = $this->actingAsFreshUser();

        $token = $this->loginAndGetToken($user);

        $this->withToken($token)
            ->withHeader('X-Vintapp-Key', self::SERVICE_KEY)
            ->postJson('/v1/token/introspect')
            ->assertOk()
            ->assertJsonPath('data.active', true)
            ->assertJsonPath('data.identity.email', $user->email)
            ->assertJsonPath('data.identity.user_id', $user->id)
            ->assertJsonPath('data.identity.two_factor_enabled', false)
            ->assertJsonStructure(['data' => ['identity' => ['user_id', 'public_id', 'email', 'roles', 'two_factor_enabled']]]);
    }

    public function test_introspection_refused_without_service_signature(): void
    {
        $token = $this->loginAndGetToken($this->actingAsFreshUser());

        $this->withToken($token)
            ->postJson('/v1/token/introspect')
            ->assertStatus(401);
    }

    public function test_introspection_refused_with_wrong_service_signature(): void
    {
        $token = $this->loginAndGetToken($this->actingAsFreshUser());

        $this->withToken($token)
            ->withHeader('X-Vintapp-Key', 'mauvais-secret')
            ->postJson('/v1/token/introspect')
            ->assertStatus(401);
    }

    public function test_introspection_refused_for_garbage_token(): void
    {
        $this->withToken('token-inexistant')
            ->withHeader('X-Vintapp-Key', self::SERVICE_KEY)
            ->postJson('/v1/token/introspect')
            ->assertStatus(401);
    }

    public function test_introspection_refused_after_logout(): void
    {
        $token = $this->loginAndGetToken($this->actingAsFreshUser());

        $this->withToken($token)->postJson('/v1/logout')->assertOk();

        $this->withToken($token)
            ->withHeader('X-Vintapp-Key', self::SERVICE_KEY)
            ->postJson('/v1/token/introspect')
            ->assertStatus(401);
    }

    public function test_pending_two_factor_token_is_not_introspectable(): void
    {
        $user = $this->actingAsFreshUser(['google2fa_enabled' => true]);

        $pending = $this->postJson('/v1/login', [
            'email' => $user->email,
            'password' => 'motdepasse123',
        ])->json('data.pending_token');

        $this->assertIsString($pending);

        // Un token 2FA en attente ne doit jamais servir d'identité.
        $this->withToken($pending)
            ->withHeader('X-Vintapp-Key', self::SERVICE_KEY)
            ->postJson('/v1/token/introspect')
            ->assertStatus(401);
    }
}
