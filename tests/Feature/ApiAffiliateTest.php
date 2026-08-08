<?php

namespace Tests\Feature;

use App\Models\ReferralCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAffiliateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_affiliate_dashboard()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/affiliate/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['points', 'stats']]);
    }

    /** @test */
    public function user_can_create_a_referral_code()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/affiliate/referral-codes', [
            'title' => 'Mon code',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['code']]);

        $this->assertDatabaseHas('referral_codes', ['user_id' => $user->id]);
    }

    /** @test */
    public function user_can_generate_a_referral_link()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/affiliate/generate-link')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['link', 'code']);
    }

    /** @test */
    public function referral_code_is_validated()
    {
        $user = User::factory()->create();
        $code = ReferralCode::create([
            'user_id' => $user->id,
            'code' => 'TEST123',
            'title' => 'Test',
            'status' => 'active',
        ]);
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/validate-referral-code', ['referral_code' => 'TEST123'])
            ->assertOk()
            ->assertJsonPath('valid', true);

        $this->postJson('/api/validate-referral-code', ['referral_code' => 'INVALID'])
            ->assertOk()
            ->assertJsonPath('valid', false);
    }
}
