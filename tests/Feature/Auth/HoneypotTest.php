<?php

namespace Tests\Feature\Auth;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class HoneypotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // forgot-password n'est pas dans les exclusions de CheckGPSCityAccess
        // (le motif 'password/*' ne couvre pas 'forgot-password'), sans ce
        // setting la requête part en redirect vers /location/validate.
        Setting::set('enable_location_restrictions', '0', [
            'label' => 'Restrictions de localisation',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function registrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0102030405',
            'address' => '10 rue de la Paix',
            'password' => 'password',
            'password_confirmation' => 'password',
            'terms' => '1',
            'form_ts' => time() - 30,
        ], $overrides);
    }

    // ---------------------------------------------------------------- register

    public function test_registration_form_renders_the_honeypot_fields(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        foreach ((array) config('honeypot.fields') as $field) {
            $response->assertSee('name="'.$field.'"', false);
        }

        $response->assertSee('name="'.config('honeypot.timestamp_field').'"', false);
    }

    public function test_a_human_submission_is_accepted(): void
    {
        $response = $this->post('/register', $this->registrationPayload());

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_registration_is_rejected_when_a_honeypot_field_is_filled(): void
    {
        foreach ((array) config('honeypot.fields') as $field) {
            $response = $this->post('/register', $this->registrationPayload([
                $field => 'https://spam.example.com',
            ]));

            $response->assertSessionHasErrors('form');
        }

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertGuest();
    }

    public function test_registration_is_rejected_when_the_timestamp_is_missing(): void
    {
        $payload = $this->registrationPayload();
        unset($payload[config('honeypot.timestamp_field')]);

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors('form');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_registration_is_rejected_when_the_timestamp_is_fabricated(): void
    {
        $response = $this->post('/register', $this->registrationPayload([
            config('honeypot.timestamp_field') => time() + 3600,
        ]));

        $response->assertSessionHasErrors('form');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_registration_is_rejected_when_submitted_too_fast(): void
    {
        config()->set('honeypot.min_seconds', 5);

        $response = $this->post('/register', $this->registrationPayload([
            config('honeypot.timestamp_field') => time() - 1,
        ]));

        $response->assertSessionHasErrors('form');
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_the_rejection_message_is_visible_on_the_registration_form(): void
    {
        $this->post('/register', $this->registrationPayload(['website' => 'https://spam.example.com']));

        // Sans ce bloc, un faux positif ne verrait rien et croirait avoir été envoyé.
        $this->get('/register')
            ->assertStatus(200)
            ->assertSee((string) config('honeypot.message'));
    }

    // ----------------------------------------------------------- forgot-password

    public function test_forgot_password_form_renders_the_honeypot_fields(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);

        foreach ((array) config('honeypot.fields') as $field) {
            $response->assertSee('name="'.$field.'"', false);
        }
    }

    public function test_forgot_password_still_sends_the_link_for_a_human(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            config('honeypot.timestamp_field') => time() - 30,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_forgot_password_pretends_success_and_sends_nothing_to_a_bot(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', [
            'email' => $user->email,
            'website' => 'https://spam.example.com',
            config('honeypot.timestamp_field') => time() - 30,
        ]);

        // La réponse est indiscernable d'un succès : pas d'oracle pour le bot.
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
        Notification::assertNothingSent();
    }

    public function test_forgot_password_pretends_success_when_the_timestamp_is_missing(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
        Notification::assertNothingSent();
    }

    // ------------------------------------------------------- la requête est bien
    // sequenced : le piège tourne avant le contrôleur, donc aucun email ni
    // utilisateur n'est créé même si la validation du formulaire échouerait.
}
