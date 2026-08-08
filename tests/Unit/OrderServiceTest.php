<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\OrderService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OrderService::class);
    }

    private function makeSellerItem(User $seller, int $quantity = 2, float $price = 50, string $status = 'active'): Item
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
            'status' => $status,
            'verification_status' => 'approved',
        ]);
    }

    /** @test */
    public function it_creates_an_order_and_decrements_stock()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeSellerItem($seller, 2, 50);

        $order = $this->service->create([
            'item_id' => $item->id,
            'quantity' => 1,
            'shipping_address' => 'Av. de la Paix',
            'shipping_city' => 'Gombe',
        ], $buyer);

        $this->assertSame('pending', $order->status);
        $this->assertSame(1, $order->quantity);
        $this->assertSame(50.0, (float) $order->total_amount);
        $this->assertSame($seller->id, $order->seller_id);
        $this->assertSame($buyer->id, $order->buyer_id);
        $this->assertSame('Av. de la Paix', $order->shipping_address);
        $this->assertStringStartsWith('ORD-', $order->order_number);
        $this->assertSame(1, $item->fresh()->quantity);
    }

    /** @test */
    public function it_prevents_buying_own_item()
    {
        $seller = User::factory()->create();
        $item = $this->makeSellerItem($seller);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Vous ne pouvez pas acheter votre propre article.');

        $this->service->create(['item_id' => $item->id, 'quantity' => 1], $seller);
    }

    /** @test */
    public function it_prevents_buying_more_than_available_stock()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeSellerItem($seller, 2);

        $this->expectException(DomainException::class);

        $this->service->create(['item_id' => $item->id, 'quantity' => 5], $buyer);
    }

    /** @test */
    public function it_prevents_buying_a_non_active_item()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeSellerItem($seller, 2, 50, 'sold');

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Cet article n'est plus disponible.");

        $this->service->create(['item_id' => $item->id, 'quantity' => 1], $buyer);
    }

    /** @test */
    public function it_cancels_an_order_and_restores_stock()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeSellerItem($seller, 2);

        $order = $this->service->create([
            'item_id' => $item->id,
            'quantity' => 1,
            'shipping_address' => 'Adresse',
        ], $buyer);

        $this->service->cancel($order);

        $this->assertNull(Order::find($order->id));
        $this->assertSame(2, $item->fresh()->quantity);
    }

    /** @test */
    public function it_walks_the_full_lifecycle_and_distributes_funds()
    {
        // user id 1 : utilisé par distributeFunds pour les transactions entreprise
        User::factory()->create(['id' => 1]);

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeSellerItem($seller, 2, 100);

        $order = $this->service->create([
            'item_id' => $item->id,
            'quantity' => 1,
            'shipping_address' => 'Adresse',
        ], $buyer);

        $this->service->confirmPayment($order);
        $this->assertSame('confirmed', $order->status);

        $this->service->markShipped($order);
        $this->assertSame('shipped', $order->status);

        $this->service->markDelivered($order);
        $this->assertSame('delivered', $order->status);

        // Wallets vendeur + entreprise (commission/transport pré-seedés par migration)
        $pending = Wallet::create(['user_id' => $seller->id, 'currency' => 'USD', 'type' => 'pending', 'balance' => 100, 'status' => 'active']);
        $main = Wallet::create(['user_id' => $seller->id, 'currency' => 'USD', 'type' => 'main', 'balance' => 0, 'status' => 'active']);
        $commission = Wallet::where('type', 'enterprise')->whereNull('user_id')->where('currency', 'USD')->where('subtype', 'commission')->firstOrFail();
        $transport = Wallet::where('type', 'enterprise')->whereNull('user_id')->where('currency', 'USD')->where('subtype', 'transport')->firstOrFail();

        // Settings déterministes : 10% commission, 5% transport
        foreach (['platform_commission_percentage' => 10, 'transport_fee_percentage' => 5] as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'type' => 'integer', 'category' => 'platform', 'label' => $key]
            );
        }

        $this->service->confirmDelivery($order);

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->confirmed_by_buyer_at);

        $this->assertSame(0.0, (float) $pending->fresh()->balance);      // débité de 100
        $this->assertSame(85.0, (float) $main->fresh()->balance);        // net après 10% + 5%
        $this->assertSame(10.0, (float) $commission->fresh()->balance);
        $this->assertSame(5.0, (float) $transport->fresh()->balance);

        $this->assertSame(3, Transaction::count());
    }
}
