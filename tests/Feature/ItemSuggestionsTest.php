<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckGPSCityAccess;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeItem(string $name): Item
    {
        $category = Category::create([
            'name' => 'Montres',
            'slug' => 'montres-' . uniqid(),
        ]);

        return Item::create([
            'user_id' => User::factory()->create()->id,
            'category_id' => $category->id,
            'name' => $name,
            'description' => 'Description de test',
            'price' => 100,
            'status' => 'active',
        ]);
    }

    public function test_suggestions_expose_public_id_url(): void
    {
        $item = $this->makeItem('Rolex classique');

        $response = $this->withoutMiddleware(CheckGPSCityAccess::class)
            ->getJson('/items/suggestions?q=Rolex');

        $response->assertOk();
        $response->assertJsonPath('success', true);

        $data = $response->json('data');
        $this->assertNotEmpty($data, 'Aucune suggestion renvoyee.');
        $this->assertStringContainsString($item->public_id, $data[0]['url']);
    }

    public function test_suggestions_empty_without_match(): void
    {
        $this->makeItem('Rolex classique');

        $response = $this->withoutMiddleware(CheckGPSCityAccess::class)
            ->getJson('/items/suggestions?q=zzzzzz');

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $this->assertSame([], $response->json('data'));
    }
}
