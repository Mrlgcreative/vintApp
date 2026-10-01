<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_returns_token_and_default_role(): void
    {
        Role::create(['name' => 'Utilisateur', 'slug' => 'user']);

        $response = $this->postJson('/v1/register', [
            'name' => 'Gloire',
            'email' => 'gloire@example.cd',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'gloire@example.cd')
            ->assertJsonPath('data.user.roles', ['user'])
            ->assertJsonStructure(['data' => ['user' => ['id', 'public_id', 'name', 'email', 'roles'], 'token', 'token_type']]);

        $this->assertDatabaseHas('users', ['email' => 'gloire@example.cd']);
        $this->assertSame('Utilisateur', User::where('email', 'gloire@example.cd')->first()->roles()->first()->name);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'gloire@example.cd']);

        $this->postJson('/v1/register', [
            'name' => 'Gloire',
            'email' => 'gloire@example.cd',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $this->postJson('/v1/register', [
            'name' => 'Gloire',
            'email' => 'gloire@example.cd',
            'password' => 'motdepasse123',
            'password_confirmation' => 'autrechose',
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    }
}
