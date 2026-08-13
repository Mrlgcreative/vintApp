<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiCartTest extends TestCase
{
    use RefreshDatabase;

    private function item(User $seller, int $quantity = 5, float $price = 100): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);

        return Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => $price,
            'currency' => 'USD',
            'quantity' => $quantity,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function authenticated_user_can_add_item_to_cart()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/cart', ['item_id' => $item->id, 'quantity' => 2])
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('carts', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function user_cannot_add_own_item_to_cart()
    {
        $seller = User::factory()->create();
        $item = $this->item($seller);
        Sanctum::actingAs($seller);

        $this->postJson('/api/v1/cart', ['item_id' => $item->id, 'quantity' => 1])
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function quantity_cannot_exceed_stock()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create(), quantity: 3);
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/cart', ['item_id' => $item->id, 'quantity' => 5])
            ->assertStatus(400)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function user_can_list_and_summarize_cart()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create(), price: 100);

        Cart::create([
            'session_id' => 'api-' . $buyer->id,
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 3,
            'image' => null,
        ]);

        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');

        $this->getJson('/api/v1/cart/summary')
            ->assertOk()
            ->assertJsonPath('data.subtotal', 300)
            ->assertJsonPath('data.transport_fee', 15)
            ->assertJsonPath('data.total', 315);
    }

    /** @test */
    public function user_can_update_and_remove_cart_item()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create());

        Cart::create([
            'session_id' => 'api-' . $buyer->id,
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 2,
            'image' => null,
        ]);

        Sanctum::actingAs($buyer);

        $this->putJson('/api/v1/cart/' . $item->id, ['quantity' => 4])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('carts', ['item_id' => $item->id, 'quantity' => 4]);

        $this->deleteJson('/api/v1/cart/' . $item->id)
            ->assertOk();

        $this->assertDatabaseMissing('carts', ['item_id' => $item->id]);
    }

    /** @test */
    public function user_can_clear_cart()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create());

        Cart::create([
            'session_id' => 'api-' . $buyer->id,
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 1,
            'image' => null,
        ]);

        Sanctum::actingAs($buyer);

        $this->deleteJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('carts', ['item_id' => $item->id]);
    }

    /** @test */
    public function guest_cannot_access_cart()
    {
        $this->getJson('/api/v1/cart')->assertStatus(401);
        $this->postJson('/api/v1/cart', ['item_id' => 1, 'quantity' => 1])->assertStatus(401);
    }
}
