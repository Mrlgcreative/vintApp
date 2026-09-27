<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * La récupération de compte ne doit jamais dépendre de la géolocalisation.
 *
 * CheckGPSCityAccess redirigeait vers /location/validate toute requête dont
 * la session n'avait pas de ville validée, y compris forgot-password et
 * reset-password : le motif d'exclusion 'password/*' ne matche pas ces chemins.
 */
class GeoRestrictionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Restrictions ACTIVES : c'est précisément le cas qui bloquait avant.
        // Le paramètre est stocké en base, pas lu dans le .env.
        Setting::set('enable_location_restrictions', '1', [
            'label' => 'Restrictions de localisation',
        ]);
    }

    protected function assertNotRedirectedToLocationGate($response): void
    {
        $location = route('location.validate');

        $this->assertNotSame(
            $location,
            $response->headers->get('Location'),
            'La requête a été détournée vers la page de localisation.'
        );
    }

    public function test_forgot_password_page_is_reachable_with_location_restrictions_enabled(): void
    {
        $response = $this->get('/forgot-password');

        $this->assertNotRedirectedToLocationGate($response);
        $response->assertStatus(200);
    }

    public function test_reset_password_page_is_reachable_with_location_restrictions_enabled(): void
    {
        $response = $this->get('/reset-password/un-token-quelconque');

        $this->assertNotRedirectedToLocationGate($response);
        $response->assertStatus(200);
    }

    public function test_forgot_password_post_reaches_the_controller(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            config('honeypot.timestamp_field') => time() - 30,
        ]);

        $this->assertNotRedirectedToLocationGate($response);
        $response->assertSessionHasNoErrors();
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_login_and_register_remain_excluded(): void
    {
        $this->assertNotRedirectedToLocationGate($this->get('/login'));
        $this->assertNotRedirectedToLocationGate($this->get('/register'));
    }
}
