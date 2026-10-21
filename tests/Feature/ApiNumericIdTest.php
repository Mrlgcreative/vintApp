<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * L'app mobile publiée envoie les clés primaires numériques (`model.id`) alors
 * que le web utilise les `public_id` (ULID). Ces tests verrouillent les deux
 * formats sur l'API.
 */
class ApiNumericIdTest extends TestCase
{
    use RefreshDatabase;

    private function item(User $seller, ?Category $category = null, ?Brand $brand = null): Item
    {
        return Item::create([
            'user_id'     => $seller->id,
            'category_id' => ($category ?? Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]))->id,
            'brand_id'    => $brand?->id,
            'name'        => 'Article',
            'description' => 'Description',
            'price'       => 50,
            'currency'    => 'USD',
            'quantity'    => 5,
            'status'      => 'active',
        ]);
    }

    /** @test */
    public function catalog_accepts_the_numeric_id()
    {
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Mode', 'slug' => 'mode-' . uniqid()]);
        $brand = Brand::create(['name' => 'Nike', 'slug' => 'nike-' . uniqid()]);
        $this->item($seller, $category, $brand);
        Sanctum::actingAs($seller);

        $this->getJson('/api/v1/categories/' . $category->id)
            ->assertOk()
            ->assertJsonPath('data.id', $category->id);

        // Le public_id doit continuer de fonctionner.
        $this->getJson('/api/v1/categories/' . $category->public_id)
            ->assertOk()
            ->assertJsonPath('data.id', $category->id);

        // Filtre `brand_id` reçu en public_id.
        $this->getJson('/api/v1/categories/' . $category->id . '/items?brand_id=' . $brand->public_id)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function items_index_accepts_a_category_public_id()
    {
        $seller = User::factory()->create();
        $category = Category::create(['name' => 'Mode', 'slug' => 'mode-' . uniqid()]);
        $this->item($seller, $category);

        $this->getJson('/api/v1/items?category_id=' . $category->public_id)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function category_can_be_created_with_a_numeric_parent_id()
    {
        $parent = Category::create(['name' => 'Parent', 'slug' => 'parent-' . uniqid()]);
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/categories', [
            'name' => 'Enfant',
            'parent_id' => $parent->id,
        ])
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('categories', ['name' => 'Enfant', 'parent_id' => $parent->id]);
    }

    /** @test */
    public function reviews_accept_the_numeric_item_id()
    {
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/reviews/item/' . $item->id)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->getJson('/api/v1/reviews/item/' . $item->public_id)
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    /** @test */
    public function seller_location_accepts_the_numeric_user_id()
    {
        $seller = User::factory()->create();

        $this->getJson('/api/v1/seller-location/' . $seller->id)
            ->assertOk()
            ->assertJsonPath('data.user_id', $seller->id);
    }

    /** @test */
    public function kpay_status_accepts_the_numeric_transaction_id()
    {
        $user = User::factory()->create();
        $transaction = Transaction::create([
            'user_id'        => $user->id,
            'buyer_id'       => $user->id,
            'transaction_id' => 'KPAY-' . strtoupper(uniqid()),
            'amount'         => 1000,
            'currency'       => 'CDF',
            'provider'       => 'kpay',
            'status'         => 'pending',
            'purpose'        => 'Paiement VintApp',
        ]);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/payments/kpay/status/' . $transaction->id)
            ->assertOk()
            ->assertJsonPath('data.transaction_id', $transaction->id);
    }

    /** @test */
    public function cart_accepts_the_public_id_of_an_item()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/cart', ['item_id' => $item->public_id, 'quantity' => 2])
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('carts', [
            'user_id' => $buyer->id,
            'item_id' => $item->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function item_can_be_updated_through_its_public_id()
    {
        $seller = User::factory()->create();
        $item = $this->item($seller);
        $category = Category::create(['name' => 'Chaussures', 'slug' => 'chaussures-' . uniqid()]);
        Sanctum::actingAs($seller);

        $this->putJson('/api/v1/items/' . $item->public_id, [
            'name' => 'Nom modifié',
            'category_id' => $category->public_id,
        ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Nom modifié',
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function order_can_be_created_with_the_public_id_of_the_item()
    {
        $buyer = User::factory()->create();
        $item = $this->item(User::factory()->create());
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/orders', [
            'item_id' => $item->public_id,
            'quantity' => 1,
            'payment_method' => 'cash_on_delivery',
            'delivery_address' => 'Av. de la Paix, Gombe',
            'delivery_city' => 'Gombe',
            'delivery_phone' => '+243810000000',
        ])
            ->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('orders', [
            'buyer_id' => $buyer->id,
            'item_id' => $item->id,
        ]);
    }

    /** @test */
    public function item_ids_are_normalized_to_primary_keys()
    {
        $first = $this->item(User::factory()->create());
        $second = $this->item(User::factory()->create());

        $this->assertSame([$first->id], Item::resolveIds([$first->public_id]));
        $this->assertSame([$first->id, $second->id], Item::resolveIds([$first->public_id, $second->id]));

        // Un identifiant inconnu est conservé pour que la validation le refuse.
        $this->assertSame(['inconnu'], Item::resolveIds(['inconnu']));
    }
}
