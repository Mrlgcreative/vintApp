<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\VintPass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiVintPassTest extends TestCase
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
            'authenticity_verified' => true,
        ]);
    }

    private function pass(User $owner, Item $item): VintPass
    {
        return VintPass::create([
            'pass_id' => 'VP-' . strtoupper(uniqid()),
            'short_code' => strtoupper(substr(uniqid(), 0, 8)),
            'item_id' => $item->id,
            'current_owner_id' => $owner->id,
            'final_score' => 0,
        ]);
    }

    /** @test */
    public function user_can_list_his_passes()
    {
        $user = User::factory()->create();
        $item = $this->item($user);
        $this->pass($user, $item);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/vintpass')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.data');
    }

    /** @test */
    public function user_cannot_see_someone_elses_pass()
    {
        $owner = User::factory()->create();
        $item = $this->item($owner);
        $pass = $this->pass($owner, $item);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/vintpass/' . $pass->id)
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function user_cannot_request_pass_for_someone_elses_item()
    {
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/vintpass/request/' . $item->id)
            ->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_list_passes()
    {
        $this->getJson('/api/v1/vintpass')->assertUnauthorized();
    }
}
