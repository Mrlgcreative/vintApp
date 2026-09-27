<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0102030405',
            'address' => '10 rue de la Paix',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
            // Piège anti-robot : une soumission navigateur porte toujours ce
            // timestamp, rendu au moment de l'affichage du formulaire.
            config('honeypot.timestamp_field') => time() - 30,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.code', absolute: false));
    }
}
