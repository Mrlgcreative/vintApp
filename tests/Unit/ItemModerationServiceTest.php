<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\VintPass;
use App\Notifications\ItemModerated;
use App\Services\ItemModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemModerationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ItemModerationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ItemModerationService::class);
    }

    private function pendingItem(User $seller): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);

        return Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Desc',
            'price' => 20,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'pending_verification',
            'verification_status' => 'pending',
        ]);
    }

    /** @test */
    public function it_rejects_an_item_and_notifies_the_seller()
    {
        Notification::fake();
        Storage::fake('public');

        $seller = User::factory()->create();
        $admin = User::factory()->create();
        $item = $this->pendingItem($seller);

        $result = $this->service->rejectItem($item, $admin, 'Mauvaise qualité', 'admin');

        $this->assertSame('inactive', $result->status);
        $this->assertSame('rejected', $result->verification_status);
        $this->assertSame('Mauvaise qualité', $result->rejection_reason);
        $this->assertSame($admin->id, $result->verified_by);

        Notification::assertSentTo($seller, ItemModerated::class);
    }

    /** @test */
    public function it_approves_an_item_and_creates_a_vintpass()
    {
        Notification::fake();
        Storage::fake('public');

        $seller = User::factory()->create();
        $admin = User::factory()->create();
        $item = $this->pendingItem($seller);

        $result = $this->service->approveItem($item, $admin, 'admin');

        $this->assertSame('active', $result->status);
        $this->assertSame('approved', $result->verification_status);
        $this->assertTrue((bool) $result->authenticity_verified);
        $this->assertSame($admin->id, $result->verified_by);

        $this->assertTrue(VintPass::where('item_id', $item->id)->exists());
        Notification::assertSentTo($seller, ItemModerated::class);
    }

    /** @test */
    public function it_does_not_duplicate_the_vintpass_on_re_approval()
    {
        Notification::fake();
        Storage::fake('public');

        $seller = User::factory()->create();
        $admin = User::factory()->create();
        $item = $this->pendingItem($seller);

        $this->service->approveItem($item, $admin, 'admin');
        $this->service->approveItem($item, $admin, 'admin');

        $this->assertSame(1, VintPass::where('item_id', $item->id)->count());
    }
}
