<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForbiddenPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('enable_location_restrictions', false, [
            'label' => 'Activer les restrictions de localisation',
        ]);
    }

    public function test_guest_is_redirected_to_login_instead_of_seeing_403(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_non_admin_gets_a_403_page_on_admin_routes(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
        $response->assertSee('Accès refusé', false);
        $response->assertSee("Vous n'avez pas les droits", false);
    }

    public function test_403_page_only_shows_the_two_messages(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
        $response->assertSee('Accès refusé', false);
        $response->assertSee("Vous n'avez pas les droits nécessaires pour consulter cette page.", false);

        $response->assertDontSee('Erreur 403</span>', false);
        $response->assertDontSee('Mon tableau de bord', false);
        $response->assertDontSee('Se connecter', false);
        $response->assertDontSee('Contactez le support', false);
    }

    public function test_403_page_uses_custom_view_not_symfony_fallback(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(403);
        $response->assertDontSee('Whoops', false);
        $response->assertDontSee('symfony', false);
    }
}
