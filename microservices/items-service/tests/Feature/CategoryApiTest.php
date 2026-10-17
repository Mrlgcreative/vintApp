<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_lists_only_active_categories_with_counts(): void
    {
        $active = $this->makeCategory(['name' => 'Actives']);
        $inactive = $this->makeCategory(['name' => 'Cachées', 'is_active' => false]);
        $this->makeItem(['category_id' => $active->id]);

        $this->getJson('/v1/categories')
            ->assertOk()
            ->assertJsonFragment(['id' => $active->public_id, 'items_count' => 1])
            ->assertJsonMissing(['id' => $inactive->public_id]);
    }

    public function test_show_returns_category(): void
    {
        $category = $this->makeCategory();

        $this->getJson('/v1/categories/'.$category->public_id)
            ->assertOk()
            ->assertJsonPath('data.category.id', $category->public_id);
    }

    public function test_admin_creates_category_with_generated_slug(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $this->withIdentity()
            ->postJson('/v1/categories', ['name' => 'Sacs à main'])
            ->assertCreated()
            ->assertJsonPath('data.category.slug', 'sacs-a-main');

        $this->assertDatabaseHas('categories', ['slug' => 'sacs-a-main']);
    }

    public function test_generated_slug_is_made_unique(): void
    {
        $this->makeCategory(['name' => 'Sacs', 'slug' => 'sacs']);

        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $this->withIdentity()
            ->postJson('/v1/categories', ['name' => 'Sacs'])
            ->assertCreated()
            ->assertJsonPath('data.category.slug', 'sacs-2');
    }

    public function test_admin_updates_category(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $category = $this->makeCategory(['name' => 'Ancienne']);

        $this->withIdentity()
            ->putJson('/v1/categories/'.$category->public_id, ['name' => 'Nouvelle'])
            ->assertOk()
            ->assertJsonPath('data.category.slug', 'nouvelle');
    }

    public function test_category_cannot_be_its_own_parent(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $category = $this->makeCategory();

        $this->withIdentity()
            ->putJson('/v1/categories/'.$category->public_id, ['parent_id' => $category->id])
            ->assertStatus(422)
            ->assertJsonValidationErrors('parent_id');
    }

    public function test_delete_blocked_when_category_has_items(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $category = $this->makeCategory();
        $this->makeItem(['category_id' => $category->id]);

        $this->withIdentity()
            ->deleteJson('/v1/categories/'.$category->public_id)
            ->assertStatus(409);
    }

    public function test_delete_blocked_when_category_has_children(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $parent = $this->makeCategory();
        $this->makeCategory(['parent_id' => $parent->id]);

        $this->withIdentity()
            ->deleteJson('/v1/categories/'.$parent->public_id)
            ->assertStatus(409);
    }

    public function test_admin_deletes_empty_category(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(1, ['admin']);

        $category = $this->makeCategory();

        $this->withIdentity()
            ->deleteJson('/v1/categories/'.$category->public_id)
            ->assertOk();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
