<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryBrandCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_creation_form_sees_newly_created_categories_and_brands(): void
    {
        Cache::flush();

        $cacheService = app(CacheService::class);

        $cacheService->getCategories(true);
        $cacheService->getBrands(true);

        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $brand = Brand::create([
            'name' => 'Test Brand',
            'slug' => 'test-brand',
            'is_active' => true,
        ]);

        $categories = $cacheService->getCategories(true);
        $brands = $cacheService->getBrands(true);

        $this->assertTrue($categories->contains('id', $category->id));
        $this->assertTrue($brands->contains('id', $brand->id));
    }
}
