<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\DeliveryAddress;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderTracking;
use App\Models\User;
use App\Services\FirebasePushService;
use App\Services\OrderProximityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class OrderProximityServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\Setting::set('enable_location_restrictions', '0', ['label' => 'Test']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    private function order(User $buyer, User $seller, DeliveryAddress $address, string $status = 'shipped'): Order
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
            'delivery_address_id' => $address->id,
            'quantity' => 1,
            'unit_price' => 50,
            'total_amount' => 50,
            'currency' => 'USD',
            'status' => $status,
            'shipping_address' => 'Av. de la Paix',
        ]);
    }

    private function bindPushMock(bool $expectCall = true): Mockery\LegacyMockInterface
    {
        $push = Mockery::mock(FirebasePushService::class);
        $push->shouldReceive('notifyOrderNearBuyer')
            ->{ $expectCall ? 'once' : 'never' }()
            ->andReturnTrue();

        $this->app->instance(FirebasePushService::class, $push);

        return $push;
    }

    public function test_notifies_buyer_when_courier_is_close(): void
    {
        $buyer = User::factory()->create(['fcm_token' => 'token-' . uniqid()]);
        $address = DeliveryAddress::create([
            'user_id' => $buyer->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'latitude' => '-4.324900',
            'longitude' => '15.308100',
        ]);
        $order = $this->order($buyer, User::factory()->create(), $address);

        $tracking = OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'in_transit',
            'latitude' => '-4.324980',
            'longitude' => '15.307900',
            'tracked_at' => now(),
        ]);

        $this->bindPushMock(true);
        $service = app(OrderProximityService::class);

        $result = $service->check($order, $tracking);

        $this->assertTrue($result);
        $this->assertNotNull($order->fresh()->proximity_notified_at);
    }

    public function test_does_not_notify_when_distance_is_far(): void
    {
        $buyer = User::factory()->create(['fcm_token' => 'token-' . uniqid()]);
        $address = DeliveryAddress::create([
            'user_id' => $buyer->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'latitude' => '-4.324900',
            'longitude' => '15.308100',
        ]);
        $order = $this->order($buyer, User::factory()->create(), $address);

        $tracking = OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'in_transit',
            'latitude' => '-4.450000',
            'longitude' => '15.300000',
            'tracked_at' => now(),
        ]);

        $this->bindPushMock(false);
        $service = app(OrderProximityService::class);

        $result = $service->check($order, $tracking);

        $this->assertFalse($result);
        $this->assertNull($order->fresh()->proximity_notified_at);
    }

    public function test_does_not_notify_twice(): void
    {
        $buyer = User::factory()->create(['fcm_token' => 'token-' . uniqid()]);
        $address = DeliveryAddress::create([
            'user_id' => $buyer->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'latitude' => '-4.324900',
            'longitude' => '15.308100',
        ]);
        $order = $this->order($buyer, User::factory()->create(), $address);
        $order->update(['proximity_notified_at' => now()->subMinute()]);

        $tracking = OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'in_transit',
            'latitude' => '-4.324980',
            'longitude' => '15.307900',
            'tracked_at' => now(),
        ]);

        $this->bindPushMock(false);
        $service = app(OrderProximityService::class);

        $this->assertFalse($service->check($order, $tracking));
    }

    public function test_does_not_notify_when_order_is_completed(): void
    {
        $buyer = User::factory()->create(['fcm_token' => 'token-' . uniqid()]);
        $address = DeliveryAddress::create([
            'user_id' => $buyer->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
            'latitude' => '-4.324900',
            'longitude' => '15.308100',
        ]);
        $order = $this->order($buyer, User::factory()->create(), $address, 'completed');

        $tracking = OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'pending',
            'latitude' => '-4.324980',
            'longitude' => '15.307900',
            'tracked_at' => now(),
        ]);

        $this->bindPushMock(false);
        $service = app(OrderProximityService::class);

        $this->assertFalse($service->check($order, $tracking));
        $this->assertNull($order->fresh()->proximity_notified_at);
    }

    public function test_does_not_notify_without_coordinates(): void
    {
        $buyer = User::factory()->create(['fcm_token' => 'token-' . uniqid()]);
        $address = DeliveryAddress::create([
            'user_id' => $buyer->id,
            'full_name' => 'Jean Test',
            'phone' => '0812345678',
            'email' => 'jean@example.com',
            'city' => 'Kinshasa',
            'commune' => 'Gombe',
            'address' => '12 Avenue de la Paix',
        ]);
        $order = $this->order($buyer, User::factory()->create(), $address);

        $tracking = OrderTracking::create([
            'order_id' => $order->id,
            'status' => 'in_transit',
            'tracked_at' => now(),
        ]);

        $this->bindPushMock(false);
        $service = app(OrderProximityService::class);

        $this->assertFalse($service->check($order, $tracking));
    }

    public function test_firebase_push_message_has_order_near_payload(): void
    {
        $user = User::factory()->create(['fcm_token' => 'token-' . uniqid()]);
        $order = (object) [
            'id' => 42,
            'order_number' => 'ORD-2026-TEST42',
        ];

        $push = Mockery::mock(FirebasePushService::class)->makePartial();
        $push->shouldReceive('sendNotification')
            ->once()
            ->withArgs(function ($token, $title, $body, $data, $image, $persist) use ($user) {
                return $token === $user->fcm_token
                    && str_contains($title, 'proche')
                    && str_contains($body, 'ORD-2026-TEST42')
                    && ($data['type'] ?? null) === 'order_near'
                    && ($data['url'] ?? null) === url('/orders/42')
                    && $persist === true;
            })
            ->andReturnTrue();

        $this->assertTrue($push->notifyOrderNearBuyer($user, $order, 0.42));
    }
}