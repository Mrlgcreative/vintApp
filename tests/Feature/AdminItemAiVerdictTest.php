<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminItemAiVerdictTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_page_shows_the_vision_model_verdict_not_the_local_heuristic_score(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Administrateur']));

        $category = Category::create(['name' => 'Informatique', 'slug' => 'informatique']);

        $item = Item::create([
            'user_id' => User::factory()->create()->id,
            'category_id' => $category->id,
            'name' => 'Accessoire informatique',
            'description' => 'Description courte.',
            'price' => 500,
            'currency' => 'CDF',
            'quantity' => 1,
            'status' => 'pending_verification',
            'images' => ['items/a.jpg', 'items/b.jpg', 'items/c.jpg'],
            'verification_score' => 84,
            'verification_details' => [
                'images' => ['score' => 70, 'issues' => ['Image très floue']],
                'text' => ['score' => 85, 'issues' => []],
                'coherence' => ['score' => 100, 'issues' => []],
                'ai' => [
                    'score' => 10,
                    'recommendation' => 'reject',
                    'real_product_photos' => false,
                    'photo_matches_description' => false,
                    'issues' => ['Photos marketing/stock et non réelles'],
                    'summary' => 'Annonce suspecte.',
                    'model' => 'qwen/qwen3-vl-plus:free',
                    'analyzed_at' => '2026-10-19T18:32:19+01:00',
                ],
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.items.show', $item));

        $response->assertOk();
        $response->assertSee('Verdict du modèle');
        $response->assertSee('À rejeter');
        $response->assertSee('10/100');
        $response->assertSee('Photos marketing/stock et non réelles');
        $response->assertSee('Analyse locale (heuristique, indicative)');
    }
}
