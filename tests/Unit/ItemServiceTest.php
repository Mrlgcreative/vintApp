<?php

namespace Tests\Unit;

use App\Events\ItemCreated;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Services\ItemService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ItemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ItemService::class);
    }

    private function category(): Category
    {
        return Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);
    }

    private function asRequest(User $user, array $data, string $method = 'POST'): Request
    {
        $request = Request::create('/items', $method, $data);
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    /** @test */
    public function it_creates_an_item_pending_manual_verification()
    {
        Event::fake([ItemCreated::class]);

        $user = User::factory()->create();
        $category = $this->category();

        $item = $this->service->createItem($this->asRequest($user, [
            'name' => 'Sac à main',
            'description' => 'Très bon état',
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 2,
            'condition' => 'new',
            'category_id' => $category->id,
        ]));

        $this->assertSame('pending_verification', $item->status);
        $this->assertSame('pending', $item->verification_status);
        $this->assertSame($user->id, $item->user_id);
        $this->assertSame(100.0, (float) $item->price);

        Event::assertDispatched(ItemCreated::class, fn ($event) => $event->item->id === $item->id);
    }

    /** @test */
    public function it_updates_only_the_present_fields()
    {
        $user = User::factory()->create();
        $category = $this->category();
        $item = Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Avant',
            'description' => 'Description initiale',
            'price' => 10,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'pending_verification',
            'verification_status' => 'pending',
        ]);

        $updated = $this->service->updateItem($item, $this->asRequest($user, ['name' => 'Après', 'price' => 20], 'PUT'));

        $this->assertSame('Après', $updated->name);
        $this->assertSame(20.0, (float) $updated->price);
        $this->assertSame('Description initiale', $updated->description);
    }

    /** @test */
    public function it_deletes_an_item_and_its_images()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $category = $this->category();
        Storage::disk('public')->put('items/photo1.jpg', 'content');

        $item = Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'À supprimer',
            'description' => 'Desc',
            'price' => 10,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'inactive',
            'images' => ['items/photo1.jpg'],
        ]);

        $this->service->deleteItem($item);

        $this->assertNull(Item::find($item->id));
        Storage::disk('public')->assertMissing('items/photo1.jpg');
    }
}
