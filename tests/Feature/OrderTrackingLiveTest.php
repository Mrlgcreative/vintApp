<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryAddress;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTrackingLiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
    }

    private function order(User $buyer, User $seller, string $status = 'shipped', ?DeliveryAddress $address = null): Order
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
            'delivery_address_id' => $address?->id,
            'quantity' => 1,
            'unit_price' => 50,
            'total_amount' => 50,
            'currency' => 'USD',
            'status' => $status,
            'shipping_address' => 'Av. de la Paix',
            'shipping_city' => 'Gombe',
        ]);
    }

    public function test_buyer_sees_live_tracking_map_and_data_url_on_shipped_order(): void
    {
        $buyer = User::factory()->create();
        $seller = User::factory()->create();
        $address = DeliveryAddress::create([
            'user_id' => $buyer->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'latitude' => '-4.325000',
            'longitude' => '15.308000',
            'is_default' => true,
        ]);

        $order = $this->order($buyer, $seller, 'shipped', $address);

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'in_transit',
            'latitude' => '-4.320000',
            'longitude' => '15.295000',
            'address' => 'Avenue de la Libération',
            'city' => 'Kinshasa',
            'tracked_at' => now(),
        ]);

        $response = $this->actingAs($buyer)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertViewIs('orders.show');
        $response->assertSee('Suivi de la livraison en direct', false);
        $response->assertSee('id="order-tracking-map"', false);
        $response->assertSee(route('orders.tracking-data', $order), false);
        $response->assertSee('En transit', false);
    }

    public function test_shipped_order_without_tracking_shows_map_and_preparation_notice(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create(), 'shipped');

        $response = $this->actingAs($buyer)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertSee('Suivi de la livraison en direct', false);
        $response->assertSee('id="order-tracking-map"', false);
        $response->assertSee('en cours de préparation', false);
    }

    public function test_pending_order_without_tracking_hides_map(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create(), 'pending');

        $response = $this->actingAs($buyer)->get(route('orders.show', $order));

        $response->assertOk();
        $response->assertDontSee('Suivi de la livraison en direct', false);
        $response->assertDontSee('id="order-tracking-map"', false);
    }

    public function test_completed_order_with_stale_pending_tracking_shows_delivered(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create(), 'completed');

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'latitude' => '-4.320000',
            'longitude' => '15.295000',
            'address' => 'Avenue de la Libération',
            'city' => 'Kinshasa',
            'tracked_at' => now(),
        ]);

        $response = $this->actingAs($buyer)->get(route('orders.show', $order));

        $response->assertOk();

        $html = $response->getContent();
        preg_match('/<p[^>]*id="order-tracking-status"[^>]*>(.*?)<\/p>/s', $html, $statusMatch);
        preg_match('/<p[^>]*id="order-tracking-distance"[^>]*>(.*?)<\/p>/s', $html, $distanceMatch);

        $this->assertEquals('Livrée', trim(strip_tags($statusMatch[1] ?? '')));
        $this->assertEquals('Livrée', trim(strip_tags($distanceMatch[1] ?? '')));
        $this->assertStringNotContainsString('km restants', $html);
    }

    public function test_tracking_data_endpoint_returns_latest_position(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create(), 'shipped');

        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'in_transit',
            'latitude' => '-4.320000',
            'longitude' => '15.295000',
            'address' => 'Avenue ok',
            'city' => 'Kinshasa',
            'tracked_at' => now()->subMinutes(5),
        ]);
        OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'out_for_delivery',
            'latitude' => '-4.322000',
            'longitude' => '15.298000',
            'address' => 'Avenue nouvelle',
            'city' => 'Kinshasa',
            'tracked_at' => now(),
        ]);

        $response = $this->actingAs($buyer)->getJson(route('orders.tracking-data', $order));

        $response->assertOk()
            ->assertJsonPath('available', true)
            ->assertJsonPath('latitude', -4.322)
            ->assertJsonPath('longitude', 15.298)
            ->assertJsonPath('status', 'out_for_delivery')
            ->assertJsonPath('distance_km', null);
    }

    public function test_tracking_data_endpoint_returns_false_when_no_tracking(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create(), 'shipped');

        $this->actingAs($buyer)
            ->getJson(route('orders.tracking-data', $order))
            ->assertOk()
            ->assertJsonPath('available', false);
    }

    public function test_tracking_data_endpoint_rejects_unauthorized_user(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order($buyer, User::factory()->create(), 'shipped');

        $this->actingAs(User::factory()->create())
            ->getJson(route('orders.tracking-data', $order))
            ->assertStatus(403);
    }
}