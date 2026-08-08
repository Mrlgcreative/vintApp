<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiCatalogTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function public_can_list_categories()
    {
        Cache::flush();
        $category = Category::create(['name' => 'Électronique', 'slug' => 'electronique', 'is_active' => true]);

        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $category->id);
    }

    /** @test */
    public function public_can_list_active_brands()
    {
        Cache::flush();
        $brand = Brand::create(['name' => 'Nike', 'slug' => 'nike', 'is_active' => true]);

        $this->getJson('/api/v1/brands')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.0.id', $brand->id);
    }

    /** @test */
    public function category_items_only_include_active_items()
    {
        $category = Category::create(['name' => 'Mode', 'slug' => 'mode']);
        $user = \App\Models\User::factory()->create();
        \Laravel\Sanctum\Sanctum::actingAs($user);
        \App\Models\Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Actif',
            'description' => 'D',
            'price' => 10,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'active',
        ]);
        \App\Models\Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Inactif',
            'description' => 'D',
            'price' => 10,
            'currency' => 'USD',
            'quantity' => 1,
            'status' => 'inactive',
        ]);

        $this->getJson('/api/v1/categories/' . $category->id . '/items')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
