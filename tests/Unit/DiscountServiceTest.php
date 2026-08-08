<?php

namespace Tests\Unit;

use App\Events\NewNotification;
use App\Models\Category;
use App\Models\Discount;
use App\Models\Item;
use App\Models\User;
use App\Services\DiscountService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DiscountServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DiscountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DiscountService::class);
    }

    private function makeItem(User $seller, float $price = 100): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);

        return Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => $price,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'active',
            'verification_status' => 'approved',
        ]);
    }

    /** @test */
    public function it_applies_a_discount_and_calculates_amounts()
    {
        Event::fake([NewNotification::class]);

        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeItem($seller, 100);

        $discount = $this->service->applyDiscount($item->id, $buyer->id, 10, $seller, null, 24);

        $this->assertSame(Discount::STATUS_APPROVED, $discount->status);
        $this->assertSame($seller->id, $discount->seller_id);
        $this->assertSame($buyer->id, $discount->user_id);
        $this->assertSame(100.0, (float) $discount->original_price);
        $this->assertSame(10.0, (float) $discount->discount_amount);
        $this->assertSame(90.0, (float) $discount->final_price);
        $this->assertTrue($discount->expires_at->greaterThan(now()->addHours(23)));

        // Notification créée pour l'acheteur
        $this->assertDatabaseHas('notifications', [
            'user_id' => $buyer->id,
            'type' => 'discount_applied',
        ]);
    }

    /** @test */
    public function it_rejects_a_non_owner_seller()
    {
        $seller = User::factory()->create();
        $intruder = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeItem($seller);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage("Vous n'êtes pas le propriétaire de cet article.");

        $this->service->applyDiscount($item->id, $buyer->id, 10, $intruder);
    }

    /** @test */
    public function it_rejects_a_duplicate_active_discount()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeItem($seller);

        $this->service->applyDiscount($item->id, $buyer->id, 10, $seller);
        $this->assertTrue($this->service->hasActiveDiscount($item->id, $buyer->id));

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Une réduction est déjà active pour ce client sur cet article.');

        $this->service->applyDiscount($item->id, $buyer->id, 20, $seller);
    }

    /** @test */
    public function it_returns_only_valid_discounts()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeItem($seller);

        $valid = $this->service->applyDiscount($item->id, $buyer->id, 15, $seller);

        // Réduction expirée : ne doit pas remonter
        Discount::create([
            'item_id' => $item->id,
            'user_id' => $buyer->id,
            'seller_id' => $seller->id,
            'original_price' => 100,
            'discount_percentage' => 5,
            'discount_amount' => 5,
            'final_price' => 95,
            'status' => Discount::STATUS_APPROVED,
            'expires_at' => now()->subHour(),
        ]);

        $discounts = $this->service->getAvailableDiscounts($item->id, $buyer->id);

        $this->assertCount(1, $discounts);
        $this->assertSame($valid->id, $discounts->first()->id);
    }

    /** @test */
    public function it_builds_the_buyer_message()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = $this->makeItem($seller, 100);

        $discount = $this->service->applyDiscount($item->id, $buyer->id, 10, $seller);

        $message = $this->service->buildDiscountMessage($discount);

        $this->assertStringContainsString('réduction de 10%', $message);
        $this->assertStringContainsString('Prix original: $ 100.00', $message);
        $this->assertStringContainsString('Prix avec réduction: $ 90.00', $message);
        $this->assertStringContainsString('Cette offre expire le', $message);
    }

    /** @test */
    public function it_maps_business_errors_to_http_codes()
    {
        $this->assertSame(403, $this->service->errorStatusCode(new DomainException("Vous n'êtes pas le propriétaire de cet article.")));
        $this->assertSame(409, $this->service->errorStatusCode(new DomainException('Une réduction est déjà active pour ce client sur cet article.')));
        $this->assertSame(400, $this->service->errorStatusCode(new DomainException('Autre erreur')));
    }
}
