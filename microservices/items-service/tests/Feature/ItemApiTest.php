<?php

namespace Tests\Feature;

use App\Models\OutboxMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_exposes_only_active_items(): void
    {
        $active = $this->makeItem(['status' => 'active']);
        $inactive = $this->makeItem(['status' => 'inactive']);

        $this->getJson('/v1/items')
            ->assertOk()
            ->assertJsonFragment(['id' => $active->public_id])
            ->assertJsonMissing(['id' => $inactive->public_id]);
    }

    public function test_public_index_filters_by_category_condition_and_price(): void
    {
        $category = $this->makeCategory();

        $matching = $this->makeItem([
            'category_id' => $category->id,
            'condition' => 'new',
            'price' => 150,
        ]);
        $this->makeItem(['category_id' => $category->id, 'condition' => 'poor', 'price' => 50]);
        $this->makeItem(['condition' => 'new', 'price' => 150]);

        $this->getJson('/v1/items?category_id='.$category->id.'&condition=new&min_price=100&max_price=200')
            ->assertOk()
            ->assertJsonFragment(['id' => $matching->public_id])
            ->assertJsonCount(1, 'data.items');
    }

    public function test_public_index_searches_name_and_description(): void
    {
        $match = $this->makeItem(['name' => 'Montre en or', 'description' => 'Édition limitée']);
        $this->makeItem(['name' => 'Sac', 'description' => 'Cuir']);

        $this->getJson('/v1/items?search=limitée')
            ->assertOk()
            ->assertJsonFragment(['id' => $match->public_id])
            ->assertJsonCount(1, 'data.items');
    }

    public function test_per_page_is_capped(): void
    {
        $this->makeItem();
        $this->makeItem();
        $this->makeItem();

        $this->getJson('/v1/items?per_page=100000')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 100);

        $this->getJson('/v1/items?per_page=2')
            ->assertOk()
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('meta.per_page', 2);
    }

    public function test_show_returns_active_item_with_relations(): void
    {
        $brand = $this->makeBrand();
        $item = $this->makeItem(['brand_id' => $brand->id]);

        $this->getJson('/v1/items/'.$item->public_id)
            ->assertOk()
            ->assertJsonPath('data.item.id', $item->public_id)
            ->assertJsonPath('data.item.brand.id', $brand->public_id);
    }

    public function test_show_returns_404_for_inactive_item(): void
    {
        $item = $this->makeItem(['status' => 'inactive']);

        $this->getJson('/v1/items/'.$item->public_id)->assertStatus(404);
    }

    public function test_authenticated_seller_creates_item(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $category = $this->makeCategory();

        $this->withIdentity()
            ->postJson('/v1/items', [
                'name' => 'Montre',
                'description' => 'Belle montre',
                'price' => 320,
                'currency' => 'USD',
                'quantity' => 2,
                'category_id' => $category->id,
            ])
            ->assertCreated()
            ->assertJsonPath('data.item.seller_id', 42)
            ->assertJsonPath('data.item.price', 320);

        $message = OutboxMessage::query()->sole();

        $this->assertSame('item.created', $message->type);
        $this->assertSame(42, $message->payload['seller_id']);
    }

    public function test_store_validates_payload(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $this->withIdentity()
            ->postJson('/v1/items', ['name' => 'Sans catégorie'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['description', 'price', 'category_id']);
    }

    public function test_owner_updates_item_and_emits_event(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $item = $this->makeItem(['user_id' => 42]);

        $this->withIdentity()
            ->putJson('/v1/items/'.$item->public_id, ['price' => 300])
            ->assertOk()
            ->assertJsonPath('data.item.price', 300);

        $message = OutboxMessage::query()->where('type', 'item.updated')->sole();

        $this->assertContains('price', $message->payload['changed']);
        $this->assertEquals(300, $message->payload['price']);
    }

    public function test_owner_deletes_item_and_emits_event(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $item = $this->makeItem(['user_id' => 42]);

        $this->withIdentity()
            ->deleteJson('/v1/items/'.$item->public_id)
            ->assertOk();

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
        $this->assertSame('item.deleted', OutboxMessage::query()->sole()->type);
    }

    public function test_mine_lists_own_items_including_inactive(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $active = $this->makeItem(['user_id' => 42, 'status' => 'active']);
        $inactive = $this->makeItem(['user_id' => 42, 'status' => 'inactive']);
        $other = $this->makeItem(['user_id' => 99, 'status' => 'active']);

        $this->withIdentity()
            ->getJson('/v1/me/items')
            ->assertOk()
            ->assertJsonFragment(['id' => $active->public_id])
            ->assertJsonFragment(['id' => $inactive->public_id])
            ->assertJsonMissing(['id' => $other->public_id]);
    }

    public function test_items_can_be_listed_by_category(): void
    {
        $category = $this->makeCategory();
        $inCategory = $this->makeItem(['category_id' => $category->id]);
        $outside = $this->makeItem();

        $this->getJson('/v1/categories/'.$category->public_id.'/items')
            ->assertOk()
            ->assertJsonFragment(['id' => $inCategory->public_id])
            ->assertJsonMissing(['id' => $outside->public_id]);
    }

    public function test_items_can_be_listed_by_brand(): void
    {
        $brand = $this->makeBrand();
        $withBrand = $this->makeItem(['brand_id' => $brand->id]);
        $without = $this->makeItem();

        $this->getJson('/v1/brands/'.$brand->public_id.'/items')
            ->assertOk()
            ->assertJsonFragment(['id' => $withBrand->public_id])
            ->assertJsonMissing(['id' => $without->public_id]);
    }
}
