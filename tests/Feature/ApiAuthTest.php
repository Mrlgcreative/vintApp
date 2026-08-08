<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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
}
