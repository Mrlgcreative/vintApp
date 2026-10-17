<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_logout_revokes_token_and_session(): void
    {
        $token = $this->loginAndGetToken($this->actingAsFreshUser());

        $this->withToken($token)
            ->postJson('/v1/logout')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('personal_access_tokens', ['token' => hash('sha256', $token)]);
        $this->assertDatabaseHas('user_sessions', ['is_active' => false]);
    }

    public function test_revoked_token_cannot_access_me(): void
    {
        $token = $this->loginAndGetToken($this->actingAsFreshUser());

        $this->withToken($token)->postJson('/v1/logout')->assertOk();

        $this->withToken($token)->getJson('/v1/me')->assertUnauthorized();
    }

    public function test_me_returns_identity_and_roles(): void
    {
        $user = $this->actingAsFreshUser();

        $token = $this->loginAndGetToken($user);

        $this->withToken($token)->getJson('/v1/me')
            ->assertOk()
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.user.public_id', $user->public_id);
    }

    public function test_unauthenticated_request_is_refused(): void
    {
        $this->getJson('/v1/me')->assertUnauthorized();
    }
}
