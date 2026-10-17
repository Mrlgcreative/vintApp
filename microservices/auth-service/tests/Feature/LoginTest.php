<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'gloire@example.cd',
            'password' => Hash::make('motdepasse123'),
        ], $attrs));
    }

    public function test_login_returns_token(): void
    {
        $user = $this->user();

        $response = $this->postJson('/v1/login', [
            'email' => 'gloire@example.cd',
            'password' => 'motdepasse123',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonStructure(['data' => ['token', 'token_type']]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'auth_token',
        ]);
    }

    public function test_login_with_wrong_password_is_refused(): void
    {
        $this->user();

        $this->postJson('/v1/login', [
            'email' => 'gloire@example.cd',
            'password' => 'mauvais',
        ])->assertStatus(401)->assertJsonPath('success', false);
    }

    public function test_login_with_unknown_email_returns_same_message(): void
    {
        $this->user();

        $inconnu = $this->postJson('/v1/login', [
            'email' => 'inconnu@example.cd',
            'password' => 'motdepasse123',
        ]);

        $mauvais = $this->postJson('/v1/login', [
            'email' => 'gloire@example.cd',
            'password' => 'mauvais',
        ]);

        // Ne pas révéler quelles adresses existent.
        $this->assertSame($inconnu->json('message'), $mauvais->json('message'));
    }

    public function test_login_with_two_factor_enabled_returns_pending_token(): void
    {
        $this->user(['google2fa_enabled' => true]);

        $response = $this->postJson('/v1/login', [
            'email' => 'gloire@example.cd',
            'password' => 'motdepasse123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.two_factor_required', true)
            ->assertJsonStructure(['data' => ['pending_token', 'token_type']]);

        // Aucun token complet tant que la 2FA n'est pas validée.
        $this->assertDatabaseMissing('personal_access_tokens', ['name' => 'auth_token']);
        $this->assertDatabaseHas('personal_access_tokens', ['name' => '2fa_pending']);
    }
}
