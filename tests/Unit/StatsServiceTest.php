<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Item;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Review;
use App\Models\SupportChat;
use App\Models\Transaction;
use App\Models\User;
use App\Services\StatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StatsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StatsService::class);
    }

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeItem(User $user, float $price = 100): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);

        return Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => $price,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'active',
            'verification_status' => 'approved',
            'views' => 50,
        ]);
    }

    private function makeOrder(User $buyer, User $seller, Item $item, string $status = 'completed'): Order
    {
        return Order::create([
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_id' => $item->id,
            'quantity' => 1,
            'unit_price' => 100,
            'total_amount' => 100,
            'status' => $status,
            'shipping_address' => 'Kinshasa',
        ]);
    }

    private function makeTransaction(User $user, float $amount = 50): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'buyer_id' => $user->id,
            'amount' => $amount,
            'currency' => 'USD',
            'status' => 'completed',
            'type' => 'purchase',
            'provider' => 'Test',
            'payment_method' => 'wallet',
            'purpose' => 'test',
            'transaction_id' => 'TXN-' . uniqid(),
        ]);
    }

    private function makeChat(User $user, array $overrides = []): SupportChat
    {
        return SupportChat::create(array_merge([
            'reference' => 'SUP-' . uniqid(),
            'user_id' => $user->id,
            'subject' => 'Demande',
            'status' => 'open',
            'priority' => 'normal',
            'category' => 'general',
            'last_message_at' => now(),
        ], $overrides));
    }

    /** @test */
    public function it_returns_user_stats()
    {
        $seller = $this->makeUser();
        $buyer = $this->makeUser();
        $item = $this->makeItem($seller);
        $order = $this->makeOrder($buyer, $seller, $item);

        Payment::create([
            'user_id' => $seller->id,
            'seller_id' => $seller->id,
            'buyer_id' => $buyer->id,
            'order_id' => $order->id,
            'amount' => 100,
            'currency' => 'USD',
            'status' => 'completed',
        ]);

        Message::create([
            'sender_id' => $buyer->id,
            'receiver_id' => $seller->id,
            'subject' => 'Bonjour',
            'content' => 'Message non lu',
        ]);

        Notification::create([
            'user_id' => $seller->id,
            'type' => 'test',
            'title' => 'Titre',
            'message' => 'Contenu',
        ]);

        Review::create([
            'reviewer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'item_id' => $item->id,
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Parfait',
            'status' => 'approved',
        ]);

        $stats = $this->service->getUserStats($seller->id);

        $this->assertSame(1, $stats['total_items']);
        $this->assertSame(1, $stats['active_items']);
        $this->assertSame(1, $stats['total_sales']);
        $this->assertSame(100.0, (float) $stats['total_revenue']);
        $this->assertSame(1, $stats['unread_messages']);
        $this->assertSame(1, $stats['unread_notifications']);
        $this->assertSame(5.0, (float) $stats['average_rating']);
        $this->assertSame(1, $stats['total_reviews']);
    }

    /** @test */
    public function it_returns_sales_chart_shape()
    {
        $seller = $this->makeUser();
        $buyer = $this->makeUser();
        $item = $this->makeItem($seller);
        $this->makeOrder($buyer, $seller, $item);

        $chart = $this->service->getSalesChart($seller->id, 6);

        $this->assertCount(6, $chart['labels']);
        $this->assertCount(6, $chart['data']);
        $this->assertSame(1, $chart['data'][5]);
    }

    /** @test */
    public function it_returns_user_analytics_structure()
    {
        $seller = $this->makeUser();
        $buyer = $this->makeUser();
        $item = $this->makeItem($seller);
        $this->makeOrder($buyer, $seller, $item);

        $analytics = $this->service->getUserAnalytics($seller->id);

        $this->assertCount(1, $analytics['top_selling_items']);
        $this->assertSame(1, $analytics['top_selling_items']->first()->sales_count);
        $this->assertCount(1, $analytics['revenue_by_category']);
        $this->assertSame(1, (int) $analytics['customer_demographics']->total_orders);
        $this->assertSame(100.0, (float) $analytics['customer_demographics']->average_order_value);
        $this->assertArrayHasKey('total_views', $analytics['conversion_rates']);
        $this->assertArrayHasKey('conversion_rate', $analytics['conversion_rates']);
    }

    /** @test */
    public function it_returns_user_support_stats()
    {
        $user = $this->makeUser();
        $this->makeChat($user);
        $this->makeChat($user, ['status' => 'in_progress']);
        $this->makeChat($user, ['status' => 'closed', 'closed_at' => now()]);

        $stats = $this->service->getUserSupportStats($user->id);

        $this->assertSame(3, $stats['total_support_chats']);
        $this->assertSame(1, $stats['open_support_chats']);
        $this->assertSame(1, $stats['pending_support_chats']);
        $this->assertSame(0, $stats['unassigned_support_chats']);
    }

    /** @test */
    public function it_returns_daily_stats_shape()
    {
        $user = $this->makeUser();
        $this->makeTransaction($user);
        $this->makeItem($user);

        $daily = $this->service->getDailyStats(30);

        $this->assertCount(30, $daily);
        $this->assertArrayHasKey('date', $daily->first());
        $this->assertArrayHasKey('users', $daily->first());
        $this->assertArrayHasKey('transactions', $daily->first());
        $this->assertArrayHasKey('revenue', $daily->first());
        $this->assertArrayHasKey('orders', $daily->first());
        $this->assertSame(1, $daily->last()['users']);
        $this->assertSame(1, $daily->last()['transactions']);
    }

    /** @test */
    public function it_returns_admin_dashboard_stats()
    {
        $user = $this->makeUser();
        $this->makeItem($user);
        $this->makeChat($user);
        $this->makeChat($user, ['status' => 'in_progress']);

        $stats = $this->service->getAdminDashboardStats();

        $this->assertSame(1, $stats['total_users']);
        $this->assertSame(1, $stats['total_items']);
        $this->assertSame(2, $stats['total_support_chats']);
        $this->assertSame(1, $stats['open_support_chats']);
        $this->assertSame(2, $stats['pending_support_chats']);
        $this->assertArrayHasKey('total_transactions', $stats);
        $this->assertArrayHasKey('pending_wallets', $stats);
        $this->assertArrayHasKey('total_revenue_usd', $stats);
    }

    /** @test */
    public function it_returns_admin_api_stats()
    {
        $user = $this->makeUser();
        $this->makeItem($user);
        $this->makeChat($user);

        $stats = $this->service->getAdminApiStats();

        $this->assertSame(1, $stats['total_users']);
        $this->assertSame(1, $stats['total_items']);
        $this->assertSame(1, $stats['total_support_chats']);
        $this->assertSame(1, $stats['open_support_chats']);
        $this->assertArrayHasKey('total_revenue_usd', $stats);
        $this->assertArrayHasKey('pending_wallets', $stats);
    }

    /** @test */
    public function it_returns_stats_summary()
    {
        $user = $this->makeUser();
        $this->makeItem($user);
        $this->makeChat($user);

        $stats = $this->service->getStatsSummary();

        $this->assertSame(1, $stats['users']['total']);
        $this->assertSame(1, $stats['items']['total']);
        $this->assertSame(1, $stats['support']['total']);
        $this->assertArrayHasKey('transactions', $stats);
        $this->assertArrayHasKey('orders', $stats);
        $this->assertArrayHasKey('wallets', $stats);
        $this->assertArrayHasKey('verifications', $stats);
    }
}
