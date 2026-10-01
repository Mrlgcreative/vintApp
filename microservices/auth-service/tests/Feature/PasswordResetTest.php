<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_link_is_sent(): void
    {
        Notification::fake();

        $user = $this->actingAsFreshUser();

        $this->postJson('/v1/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('success', true);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_updates_credentials(): void
    {
        $user = $this->actingAsFreshUser();

        $token = Password::createToken($user);

        $this->postJson('/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'nouveaumotdepasse',
            'password_confirmation' => 'nouveaumotdepasse',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertTrue(Hash::check('nouveaumotdepasse', $user->fresh()->password));
    }

    public function test_reset_with_invalid_token_is_refused(): void
    {
        $user = $this->actingAsFreshUser();

        $this->postJson('/v1/reset-password', [
            'token' => 'jeton-invalide',
            'email' => $user->email,
            'password' => 'nouveaumotdepasse',
            'password_confirmation' => 'nouveaumotdepasse',
        ])->assertStatus(422)->assertJsonPath('success', false);

        $this->assertTrue(Hash::check('motdepasse123', $user->fresh()->password));
    }

    public function test_reset_revokes_active_sessions(): void
    {
        $user = $this->actingAsFreshUser();

        $this->loginAndGetToken($user);

        $this->assertDatabaseHas('user_sessions', ['user_id' => $user->id, 'is_active' => true]);

        $token = Password::createToken($user);

        $this->postJson('/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'nouveaumotdepasse',
            'password_confirmation' => 'nouveaumotdepasse',
        ])->assertOk();

        // Un mot de passe changé doit couper les sessions ouvertes.
        $this->assertDatabaseMissing('user_sessions', ['user_id' => $user->id, 'is_active' => true]);
    }
}
