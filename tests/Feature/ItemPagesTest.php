<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckGPSCityAccess;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemPagesTest extends TestCase
{
    use RefreshDatabase;

    private function makeItem(Category $category, string $name): Item
    {
        return Item::create([
            'user_id' => User::factory()->create()->id,
            'category_id' => $category->id,
            'name' => $name,
            'description' => 'Description de test',
            'price' => 100,
            'status' => 'active',
        ]);
    }

    private function category(string $name): Category
    {
        return Category::create(['name' => $name, 'slug' => strtolower($name) . '-' . uniqid()]);
    }

    private function guest()
    {
        return $this->withoutMiddleware(CheckGPSCityAccess::class);
    }

    /**
     * La requete des articles similaires restreint ses colonnes : si public_id
     * n'y figure pas, getRouteKey() renvoyait une chaine vide et la page 500ait.
     */
    public function test_show_page_renders_when_a_similar_item_exists(): void
    {
        $category = $this->category('Montres');
        $item = $this->makeItem($category, 'Rolex classique');
        $similar = $this->makeItem($category, 'Omega vintage');

        $response = $this->actingAs(User::factory()->create(['email_verified_at' => now()]))
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get('/items/' . $item->public_id);

        $response->assertOk();
        $response->assertSee($similar->public_id, false);
    }

    public function test_show_page_renders_when_a_similar_item_has_no_public_id(): void
    {
        $category = $this->category('Sacs');
        $item = $this->makeItem($category, 'Sac cuir');
        $similar = $this->makeItem($category, 'Sac toile');

        // Simule les enregistrements historiques : public_id jamais renseigne.
        $similar->forceFill(['public_id' => null])->saveQuietly();

        $response = $this->actingAs(User::factory()->create(['email_verified_at' => now()]))
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get('/items/' . $item->public_id);

        $response->assertOk();
        $response->assertSee('/items/' . $similar->id, false);
    }

    /**
     * Le catalogue restreint aussi ses colonnes, et ses cartes sont des liens
     * vers items.show.
     */
    public function test_catalog_page_renders_item_cards(): void
    {
        $item = $this->makeItem($this->category('Telephones'), 'iPhone 13');

        $response = $this->guest()->get('/items');

        $response->assertOk();
        $response->assertSee($item->public_id, false);
    }
}
