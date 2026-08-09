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
}
