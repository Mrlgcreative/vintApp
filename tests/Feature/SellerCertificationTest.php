<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckGPSCityAccess;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerCertificationTest extends TestCase
{
    use RefreshDatabase;

    private function sellerWithItem(array $itemOverrides = []): array
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $category = Category::create([
            'name' => 'Montres',
            'slug' => 'montres-' . uniqid(),
            'is_active' => true,
        ]);

        $item = Item::create(array_merge([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'Rolex Datejust',
            'description' => 'Montre en bon etat, garantie 1 an.',
            'price' => 250,
            'status' => 'active',
        ], $itemOverrides));

        return [$user, $item];
    }

    public function test_seller_sees_certification_action(): void
    {
        [$user, $item] = $this->sellerWithItem();

        $response = $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get('/my-items');

        $response->assertOk();
        $response->assertSee(route('authenticity.request', $item), false);
        $response->assertSee('Certifier');
    }

    public function test_seller_can_open_request_form(): void
    {
        [$user, $item] = $this->sellerWithItem();

        $response = $this->actingAs($user)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.request', $item));

        $response->assertOk();
    }

    public function test_seller_cannot_request_for_inactive_category(): void
    {
        [$user, $item] = $this->sellerWithItem();
        $item->category->update(['is_active' => false]);
        $item->unsetRelation('category');

        $this->assertFalse($item->fresh()->canRequestVerification());
    }

    public function test_other_seller_gets_403(): void
    {
        [, $item] = $this->sellerWithItem();
        $other = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($other)
            ->withoutMiddleware(CheckGPSCityAccess::class)
            ->get(route('authenticity.request', $item))
            ->assertForbidden();
    }
}
