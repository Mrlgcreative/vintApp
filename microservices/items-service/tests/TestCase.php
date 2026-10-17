<?php

namespace Tests;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    protected function configureAuth(string $url = 'http://auth.test', string $secret = 'secret-service'): void
    {
        config()->set('items.auth_service.url', $url);
        config()->set('items.auth_service.shared_secret', $secret);
    }

    /**
     * Fait répondre auth-service avec une identité donnée.
     *
     * @param  array<int, string>  $roles
     */
    protected function fakeIdentity(int $userId = 1, array $roles = ['user']): void
    {
        Http::fake(['*/v1/token/introspect' => Http::response([
            'success' => true,
            'data' => [
                'active' => true,
                'identity' => ['user_id' => $userId, 'roles' => $roles],
            ],
        ])]);
    }

    protected function withIdentity(): static
    {
        return $this->withToken('test-token');
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeCategory(array $attributes = []): Category
    {
        return Category::create(array_merge([
            'name' => 'Montres',
            'slug' => 'montres-'.uniqid(),
            'is_active' => true,
            'sort_order' => 0,
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeBrand(array $attributes = []): Brand
    {
        return Brand::create(array_merge([
            'name' => 'Rolex',
            'slug' => 'rolex-'.uniqid(),
            'is_active' => true,
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeItem(array $attributes = []): Item
    {
        return Item::create(array_merge([
            'user_id' => 1,
            'name' => 'Montre',
            'description' => 'Belle montre',
            'price' => 100,
            'currency' => 'USD',
            'quantity' => 1,
            'condition' => 'good',
            'status' => 'active',
            'category_id' => $this->makeCategory()->id,
        ], $attributes));
    }
}
