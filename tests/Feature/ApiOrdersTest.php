<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiOrdersTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $buyer, User $seller): Order
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);
        $item = Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => 50,
            'currency' => 'USD',
            'quantity' => 2,
            'status' => 'active',
        ]);

        return Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => 50,
            'total_amount' => 50,
            'currency' => 'USD',
            'status' => 'pending',
            'shipping_address' => 'Av. de la Paix',
            'shipping_city' => 'Gombe',
        ]);
    }

    /** @test */
    public function user_can_list_his_orders()
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create());
        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/orders')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function user_cannot_see_someone_elses_order()
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/orders/' . $order->id)
            ->assertStatus(403)
            ->assertJsonPath('success', false);
    }
}
