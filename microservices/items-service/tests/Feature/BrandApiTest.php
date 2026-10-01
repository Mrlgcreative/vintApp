<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_lists_only_active_brands_with_counts(): void
    {
        $active = $this->makeBrand(['name' => 'Rolex']);
        $inactive = $this->makeBrand(['name' => 'Cachée', 'is_active' => false]);
        $this->makeItem(['brand_id' => $active->id]);

        $this->getJson('/v1/brands')
            ->assertOk()
            ->assertJsonFragment(['id' => $active->public_id, 'items_count' => 1])
            ->assertJsonMissing(['id' => $inactive->public_id]);
    }

    public function test_show_returns_brand(): void
    {
        $brand = $this->makeBrand();

        $this->getJson('/v1/brands/'.$brand->public_id)
            ->assertOk()
            ->assertJsonPath('data.brand.id', $brand->public_id);
    }

    public function test_admin_creates_brand_with_generated_slug(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $this->withIdentity()
            ->postJson('/v1/brands', ['name' => 'Louis Vuitton', 'country' => 'France'])
            ->assertCreated()
            ->assertJsonPath('data.brand.slug', 'louis-vuitton')
            ->assertJsonPath('data.brand.country', 'France');

        $this->assertDatabaseHas('brands', ['slug' => 'louis-vuitton']);
    }

    public function test_website_must_be_a_valid_url(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $this->withIdentity()
            ->postJson('/v1/brands', ['name' => 'Marque', 'website' => 'pas-une-url'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('website');
    }

    public function test_admin_updates_brand(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $brand = $this->makeBrand(['name' => 'Ancienne']);

        $this->withIdentity()
            ->putJson('/v1/brands/'.$brand->public_id, ['name' => 'Nouvelle'])
            ->assertOk()
            ->assertJsonPath('data.brand.slug', 'nouvelle');
    }

    public function test_delete_blocked_when_brand_is_used(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $brand = $this->makeBrand();
        $this->makeItem(['brand_id' => $brand->id]);

        $this->withIdentity()
            ->deleteJson('/v1/brands/'.$brand->public_id)
            ->assertStatus(409);
    }

    public function test_admin_deletes_unused_brand(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $brand = $this->makeBrand();

        $this->withIdentity()
            ->deleteJson('/v1/brands/'.$brand->public_id)
            ->assertOk();

        $this->assertDatabaseMissing('brands', ['id' => $brand->id]);
    }
}
