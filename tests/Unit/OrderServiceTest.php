<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Item;
use App\Models\Order;
use App\Models\Payment;
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

    private function markOrderPaid(Order $order, User $buyer, User $seller, float $amount)
    {
        Payment::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'order_id' => $order->id,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'completed',
            'transaction_id' => 'PAY-' . uniqid(),
        ]);

        $order->update(['payment_status' => 'paid']);
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

        // Wallets vendeur (créés avant confirmation : escrow y sera crédité)
        // + wallet entreprise commission (pré-seedé par migration).
        $pending = Wallet::create(['user_id' => $seller->id, 'currency' => 'USD', 'type' => 'pending', 'balance' => 0, 'status' => 'active']);
        $main = Wallet::create(['user_id' => $seller->id, 'currency' => 'USD', 'type' => 'main', 'balance' => 0, 'status' => 'active']);
        $commission = Wallet::where('type', 'enterprise')->whereNull('user_id')->where('currency', 'USD')->where('subtype', 'commission')->firstOrFail();

        // Settings déterministes : 10% commission (transport payé par l'acheteur, non déduit)
        DB::table('settings')->updateOrInsert(
            ['key' => 'platform_commission_percentage'],
            ['value' => 10, 'type' => 'integer', 'category' => 'platform', 'label' => 'plateforme']
        );

        // La confirmation du paiement crédite l'escrow (wallet pending) du vendeur
        $this->markOrderPaid($order, $buyer, $seller, 100.0);
        $this->service->confirmPayment($order);
        $this->assertSame('confirmed', $order->status);
        $this->assertSame(100.0, (float) $pending->fresh()->balance); // escrow crédité

        $this->service->markShipped($order);
        $this->assertSame('shipped', $order->status);

        $this->service->markDelivered($order);
        $this->assertSame('delivered', $order->status);

        $this->service->confirmDelivery($order);

        $this->assertSame('completed', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->confirmed_by_buyer_at);

        $this->assertSame(0.0, (float) $pending->fresh()->balance);      // escrow débité de 100
        $this->assertSame(90.0, (float) $main->fresh()->balance);        // net après commission 10% (sans transport)
        $this->assertSame(10.0, (float) $commission->fresh()->balance);

        // ESCROW (confirmation paiement) + SELLER + COMMISSION
        $this->assertSame(3, Transaction::count());
    }

    /** @test */
    public function it_credits_the_seller_pending_wallet_on_payment_confirmation()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeSellerItem($seller, 2, 100);

        $order = $this->service->create([
            'item_id' => $item->id,
            'quantity' => 1,
            'shipping_address' => 'Adresse',
        ], $buyer);

        $pending = Wallet::create(['user_id' => $seller->id, 'currency' => 'USD', 'type' => 'pending', 'balance' => 0, 'status' => 'active']);

        $this->markOrderPaid($order, $buyer, $seller, 100.0);
        $this->service->confirmPayment($order);

        // L'escrow est crédité du montant total lors de la confirmation du paiement
        $this->assertSame('confirmed', $order->fresh()->status);
        $this->assertSame(100.0, (float) $pending->fresh()->balance);
        $this->assertSame(1, Transaction::where('purpose', 'like', 'Escrow - Commande #' . $order->id . '%')->count());

        // On ne peut pas re-confirmer (status n'est plus 'pending')
        $this->expectException(DomainException::class);
        $this->service->confirmPayment($order);
    }
}
