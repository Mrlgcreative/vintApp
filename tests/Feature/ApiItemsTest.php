<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiItemsTest extends TestCase
{
    use RefreshDatabase;

    private function category(): Category
    {
        return Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);
    }

    private function item(User $user, Category $category): Item
    {
        return Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Article Test',
            'description' => 'Description',
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function public_can_list_active_items()
    {
        $user = User::factory()->create();
        $item = $this->item($user, $this->category());

        $this->getJson('/api/v1/items')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $item->id);
    }

    /** @test */
    public function public_can_show_an_item()
    {
        $user = User::factory()->create();
        $item = $this->item($user, $this->category());

        $this->getJson('/api/v1/items/' . $item->id)
            ->assertOk()
            ->assertJsonPath('data.id', $item->id);
    }

    /** @test */
    public function authenticated_user_can_toggle_favorite()
    {
        $user = User::factory()->create();
        $item = $this->item(User::factory()->create(), $this->category());
        Sanctum::actingAs($user);

        $this->postJson('/api/items/' . $item->id . '/favorite')
            ->assertOk()
            ->assertJson(['success' => true, 'is_favorite' => true]);

        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);

        $this->postJson('/api/items/' . $item->id . '/favorite')
            ->assertOk()
            ->assertJson(['success' => true, 'is_favorite' => false]);
    }

    /** @test */
    public function search_returns_json()
    {
        $user = User::factory()->create();
        $item = $this->item($user, $this->category());
        Sanctum::actingAs($user);

        $this->getJson('/api/items/search?q=Article')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $item->id);
    }
}
