<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckGPSCityAccess;
use Tests\TestCase;

class CookieBannerTest extends TestCase
{
    public function test_banner_is_present_but_hidden_until_the_user_decides(): void
    {
        $response = $this->withoutMiddleware(CheckGPSCityAccess::class)->get('/privacy');

        $response->assertOk();
        $response->assertSee('Nous utilisons des cookies', false);
        $response->assertSee('id="cookie-banner"', false);
        $response->assertSee('class="hidden fixed inset-x-0 bottom-0 z-[100]', false);
        $response->assertSee('Tout accepter', false);
        $response->assertSee('Tout refuser', false);
    }

    public function test_banner_links_to_the_privacy_policy_and_is_reachable_from_the_footer(): void
    {
        $response = $this->withoutMiddleware(CheckGPSCityAccess::class)->get('/privacy');

        $response->assertOk();
        $response->assertSee(route('privacy'), false);
        $response->assertSee('data-cookie-settings', false);
    }

    public function test_choice_is_stored_under_a_versioned_key(): void
    {
        $response = $this->withoutMiddleware(CheckGPSCityAccess::class)->get('/privacy');

        $response->assertOk();
        $response->assertSee('vintapp-cookie-consent', false);
        $response->assertSee('POLICY_VERSION = 1', false);
    }
}
