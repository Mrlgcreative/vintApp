<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiReviewsTest extends TestCase
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
            'status' => 'completed',
            'shipping_address' => 'Av. de la Paix',
            'shipping_city' => 'Gombe',
        ]);
    }

    /** @test */
    public function user_can_review_a_completed_order()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $order = $this->order($buyer, $seller);
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Excellent',
        ])->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.reviewer_id', $buyer->id)
            ->assertJsonPath('data.rating', 5);

        $this->assertDatabaseHas('reviews', ['order_id' => $order->id, 'reviewer_id' => $buyer->id]);
    }

    /** @test */
    public function user_cannot_review_someone_elses_order()
    {
        $order = $this->order(User::factory()->create(), User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/reviews', [
            'order_id' => $order->id,
            'rating' => 4,
        ])->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    /** @test */
    public function user_cannot_review_same_order_twice()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $order = $this->order($buyer, $seller);
        Review::create([
            'reviewer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_id' => $order->item_id,
            'order_id' => $order->id,
            'rating' => 5,
            'status' => 'approved',
        ]);
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/reviews', [
            'order_id' => $order->id,
            'rating' => 3,
        ])->assertStatus(422);
    }

    /** @test */
    public function user_can_get_item_reviews_with_average()
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $order = $this->order($buyer, $seller);
        Review::create([
            'reviewer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_id' => $order->item_id,
            'order_id' => $order->id,
            'rating' => 4,
            'status' => 'approved',
        ]);
        Sanctum::actingAs($buyer);

        $this->getJson('/api/v1/reviews/item/' . $order->item_id)
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.average_rating', 4)
            ->assertJsonPath('data.total_reviews', 1);
    }

    /** @test */
    public function user_cannot_update_someone_elses_review()
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create());
        $review = Review::create([
            'reviewer_id' => $buyer->id,
            'seller_id' => $order->seller_id,
            'item_id' => $order->item_id,
            'order_id' => $order->id,
            'rating' => 5,
            'status' => 'approved',
        ]);
        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/v1/reviews/' . $review->id, ['rating' => 1])
            ->assertStatus(403);
    }
}
