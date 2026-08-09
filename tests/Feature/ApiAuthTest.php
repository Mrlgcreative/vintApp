<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jean',
            'email' => 'jean@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Inscription réussie',
            ]);
        $this->assertDatabaseHas('users', ['email' => 'jean@example.com']);
        $this->assertNotEmpty($response->json('token'));
    }

    /** @test */
    public function a_user_can_login()
    {
        User::factory()->create([
            'email' => 'jean@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jean@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Connexion réussie',
            ]);
        $this->assertSame('jean@example.com', $response->json('user.email'));
        $this->assertNotEmpty($response->json('token'));
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'jean@example.com',
            'password' => Hash::make('password123'),
        ]);

        $this->postJson('/api/login', [
            'email' => 'jean@example.com',
            'password' => 'wrong-password',
        ])->assertStatus(401)->assertJson(['success' => false]);
    }

    /** @test */
    public function an_authenticated_user_can_fetch_his_profile()
    {
        $user = User::factory()->create(['name' => 'Jean']);
        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => 'Jean',
                ],
            ]);
    }

    /** @test */
    public function a_user_can_logout_and_revoke_his_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token');

        $this->postJson('/api/logout', [], [
            'Authorization' => 'Bearer ' . $token->plainTextToken,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    /** @test */
    public function user_can_request_a_password_reset_link()
    {
        $user = User::factory()->create(['email' => 'jean@example.com']);
        Mail::fake();

        $this->postJson('/api/password/email', ['email' => 'jean@example.com'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'jean@example.com']);
    }

    /** @test */
    public function requesting_a_password_reset_link_for_unknown_email_fails()
    {
        $this->postJson('/api/password/email', ['email' => 'inconnu@example.com'])
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /** @test */
    public function user_can_request_a_password_reset_link_via_forgot_endpoint()
    {
        $user = User::factory()->create(['email' => 'jean@example.com']);
        Mail::fake();

        $this->postJson('/api/password/forgot', ['email' => 'jean@example.com'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'jean@example.com']);
    }

    /** @test */
    public function forgot_endpoint_rejects_invalid_email()
    {
        $this->postJson('/api/password/forgot', ['email' => 'pas-un-email'])
            ->assertStatus(422);

        $this->postJson('/api/password/forgot', [])
            ->assertStatus(422);
    }

    /** @test */
    public function user_can_reset_password_with_a_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'jean@example.com',
            'password' => Hash::make('oldpassword123'),
        ]);

        $token = Password::createToken($user);

        $this->postJson('/api/password/reset', [
            'token' => $token,
            'email' => 'jean@example.com',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ])->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('newpassword456', $user->fresh()->password));
    }

    /** @test */
    public function resetting_password_with_an_invalid_token_fails()
    {
        $user = User::factory()->create(['email' => 'jean@example.com']);

        $this->postJson('/api/password/reset', [
            'token' => 'token-invalide',
            'email' => 'jean@example.com',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ])->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /** @test */
    public function login_returns_pending_token_when_2fa_is_enabled()
    {
        User::factory()->create([
            'email' => 'jean@example.com',
            'password' => Hash::make('password123'),
            'google2fa_enabled' => true,
            'google2fa_secret' => (new \PragmaRX\Google2FA\Google2FA())->generateSecretKey(),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'jean@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'two_factor_required' => true,
            ]);
        $this->assertNotEmpty($response->json('pending_token'));
        $this->assertNull($response->json('token'));
    }

    /** @test */
    public function verify_2fa_with_valid_totp_code_returns_access_token()
    {
        $secret = (new \PragmaRX\Google2FA\Google2FA())->generateSecretKey();
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => $secret,
        ]);

        $pending = $user->createToken('2fa_pending', ['2fa:pending']);
        $otp = (new \PragmaRX\Google2FA\Google2FA())->getCurrentOtp($secret);

        $response = $this->postJson('/api/two-factor/verify', [
            'code' => $otp,
        ], [
            'Authorization' => 'Bearer ' . $pending->plainTextToken,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJson(['user' => ['id' => $user->id, 'two_factor_enabled' => true]]);
        $this->assertNotEmpty($response->json('token'));
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $pending->accessToken->id]);
    }

    /** @test */
    public function verify_2fa_with_invalid_code_fails()
    {
        $secret = (new \PragmaRX\Google2FA\Google2FA())->generateSecretKey();
        $user = User::factory()->create([
            'google2fa_enabled' => true,
            'google2fa_secret' => $secret,
        ]);

        $pending = $user->createToken('2fa_pending', ['2fa:pending']);

        $this->postJson('/api/two-factor/verify', [
            'code' => '000000',
        ], [
            'Authorization' => 'Bearer ' . $pending->plainTextToken,
        ])->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    /** @test */
    public function full_2fa_enable_and_confirm_flow_works()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $enable = $this->postJson('/api/two-factor/enable')
            ->assertOk()
            ->assertJson(['success' => true]);
        $secret = $enable->json('data.secret');
        $this->assertNotEmpty($secret);
        $this->assertNotEmpty($enable->json('data.recoveryCodes'));

        $otp = (new \PragmaRX\Google2FA\Google2FA())->getCurrentOtp($secret);

        $this->postJson('/api/two-factor/confirm', ['code' => $otp])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertTrue($user->fresh()->google2fa_enabled);
    }

    /** @test */
    public function disable_2fa_requires_valid_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'google2fa_enabled' => true,
            'google2fa_secret' => 'TESTSECRETTESTSECRETTEST',
        ]);
        Sanctum::actingAs($user);

        $this->postJson('/api/two-factor/disable', ['password' => 'wrong-password'])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->postJson('/api/two-factor/disable', ['password' => 'password123'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $fresh = $user->fresh();
        $this->assertFalse($fresh->google2fa_enabled);
        $this->assertNull($fresh->google2fa_secret);
    }
}
