<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAuthenticityTest extends TestCase
{
    use RefreshDatabase;

    private function item(User $seller): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);

        return Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function owner_can_check_verification_eligibility()
    {
        $seller = User::factory()->create();
        $item = $this->item($seller);
        Sanctum::actingAs($seller);

        $this->getJson('/api/v1/items/' . $item->id . '/authenticity/can-verify')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.can_verify', true)
            ->assertJsonPath('data.has_existing_request', false);
    }

    /** @test */
    public function non_owner_cannot_check_eligibility()
    {
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/items/' . $item->id . '/authenticity/can-verify')
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function non_owner_cannot_get_status()
    {
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/items/' . $item->id . '/authenticity/status')
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_get_his_dashboard()
    {
        $seller = User::factory()->create();
        $this->item($seller);
        Sanctum::actingAs($seller);

        $this->getJson('/api/v1/authenticity/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true);
    }
}
