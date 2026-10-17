<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    /**
     * Utilisateur de référence pour les tests d'authentification.
     */
    protected function actingAsFreshUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'email' => 'gloire@example.cd',
            'password' => Hash::make('motdepasse123'),
        ], $attributes));
    }

    /**
     * Connexion réelle via l'API, pour obtenir un Bearer token exploitable.
     */
    protected function loginAndGetToken(User $user): string
    {
        $token = $this->postJson('/v1/login', [
            'email' => $user->email,
            'password' => 'motdepasse123',
        ])->json('data.token');

        $this->assertIsString($token, 'La connexion doit renvoyer un token.');

        return $token;
    }
}
