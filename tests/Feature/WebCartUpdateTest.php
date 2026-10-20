<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le panier web adresse les articles par `public_id` (comme cart.add / cart.buy).
 * update/remove résolvaient par `public_id` mais la vue envoyait l'id numérique
 * → 404 (firstOrFail).
 */
class WebCartUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Le middleware GPS redirige vers /location/validate : hors sujet ici.
        // On utilise le vrai interrupteur lu par le middleware.
        \App\Models\Setting::create([
            'key' => 'enable_location_restrictions',
            'label' => 'Restrictions de localisation',
            'value' => false,
        ]);
    }

    private function makeItem(User $seller): Item
    {
        $category = Category::create(['name' => 'Cat', 'slug' => 'cat-' . uniqid()]);

        return Item::create([
            'user_id' => $seller->id,
            'category_id' => $category->id,
            'name' => 'Article',
            'description' => 'Description',
            'price' => 100,
            'currency' => 'CDF',
            'quantity' => 10,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function cart_page_targets_items_by_public_id()
    {
        $buyer = User::factory()->create();
        $item = $this->makeItem(User::factory()->create());

        $this->actingAs($buyer)->post(route('cart.add', $item->public_id));

        $this->actingAs($buyer)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee(route('cart.update', $item->public_id), false)
            ->assertSee(route('cart.remove', $item->public_id), false);
    }

    /** @test */
    public function update_changes_quantity_instead_of_404()
    {
        $buyer = User::factory()->create();
        $item = $this->makeItem(User::factory()->create());

        $this->actingAs($buyer)->post(route('cart.add', $item->public_id));

        $this->actingAs($buyer)
            ->post(route('cart.update', $item->public_id), ['quantity' => 3])
            ->assertRedirect(route('cart.index'));

        $this->assertSame(3, Cart::where('item_id', $item->id)->first()->quantity);
    }

    /** @test */
    public function remove_deletes_the_row_instead_of_404()
    {
        $buyer = User::factory()->create();
        $item = $this->makeItem(User::factory()->create());

        $this->actingAs($buyer)->post(route('cart.add', $item->public_id));

        $this->actingAs($buyer)
            ->post(route('cart.remove', $item->public_id))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseMissing('carts', ['item_id' => $item->id]);
    }
}
