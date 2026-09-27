<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckGPSCityAccess;
use App\Models\Category;
use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerSpaceCertificationTest extends TestCase
{
    use RefreshDatabase;

    private function sellerWithItem(): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $role = Role::firstOrCreate(['slug' => 'vendeur'], ['name' => 'Vendeur']);
        $user->roles()->attach($role->id);

        $category = Category::create([
            'name' => 'Montres',
            'slug' => 'montres-' . uniqid(),
            'is_active' => true,
        ]);

        $item = Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Rolex Datejust',
            'description' => 'Montre en bon etat, garantie 1 an.',
            'price' => 250,
            'status' => 'active',
        ]);

        return [$user, $item];
    }

    public function test_seller_space_shows_certification_action(): void
    {
        [$user, $item] = $this->sellerWithItem();

        $response = $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get('/seller/items');

        $response->assertOk();
        $response->assertSee(route('authenticity.request', $item), false);
        $response->assertSee('Certifier');
    }

    public function test_seller_space_reaches_request_form(): void
    {
        [$user, $item] = $this->sellerWithItem();

        $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.request', $item))
            ->assertOk();
    }
}
