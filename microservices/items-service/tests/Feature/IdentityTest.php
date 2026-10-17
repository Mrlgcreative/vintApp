<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * items-service ne possède pas la table users : toute écriture vient d'un
 * appel à auth-service, et l'échec doit être closed.
 */
class IdentityTest extends TestCase
{
    use RefreshDatabase;

    public function test_write_api_refused_when_auth_service_not_configured(): void
    {
        // Ni URL ni secret : fail-closed, pas de mode dégradé.
        config()->set('items.auth_service.url', '');
        config()->set('items.auth_service.shared_secret', null);

        $this->withToken('peu-importe')->postJson('/v1/items', [])->assertStatus(503);
    }

    public function test_write_api_refused_without_token(): void
    {
        $this->configureAuth();

        $this->postJson('/v1/items', [])->assertStatus(401);
    }

    public function test_write_api_refused_when_token_is_rejected(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response(['success' => false], 401)]);

        $this->withToken('token-invalide')->postJson('/v1/items', [])->assertStatus(401);
    }

    public function test_write_api_refused_when_auth_service_is_unreachable(): void
    {
        $this->configureAuth();

        Http::fake(fn () => throw new ConnectionException('connexion refusée'));

        $this->withToken('token-valide')->postJson('/v1/items', [])->assertStatus(503);
    }

    public function test_write_api_refused_when_auth_service_errors(): void
    {
        $this->configureAuth();

        Http::fake(['*/v1/token/introspect' => Http::response(['boom' => true], 500)]);

        $this->withToken('token-valide')->postJson('/v1/items', [])->assertStatus(503);
    }

    public function test_public_catalog_stays_readable_without_token(): void
    {
        $this->configureAuth();
        $this->makeItem();

        $this->getJson('/v1/items')->assertOk();
    }

    public function test_item_creation_uses_identity_not_client_supplied_user_id(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $category = $this->makeCategory();

        // Le client tente d'imposer un user_id : il doit être ignoré.
        $this->withIdentity()
            ->postJson('/v1/items', [
                'name' => 'Sac cuir',
                'description' => 'Sac en cuir véritable',
                'price' => 2500,
                'category_id' => $category->id,
                'user_id' => 999,
            ])
            ->assertCreated()
            ->assertJsonPath('data.item.seller_id', 42);

        $this->assertDatabaseHas('items', ['user_id' => 42, 'name' => 'Sac cuir']);
        $this->assertDatabaseMissing('items', ['user_id' => 999]);
    }

    public function test_user_cannot_update_another_sellers_item(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(42);

        $item = $this->makeItem(['user_id' => 99]);

        $this->withIdentity()
            ->putJson('/v1/items/'.$item->public_id, ['price' => 1])
            ->assertStatus(403);
    }

    public function test_admin_can_update_another_sellers_item(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(7, ['admin']);

        $item = $this->makeItem(['user_id' => 99]);

        $this->withIdentity()
            ->putJson('/v1/items/'.$item->public_id, ['price' => 1])
            ->assertOk();
    }

    public function test_category_creation_is_reserved_to_admins(): void
    {
        $this->configureAuth();
        $this->fakeIdentity(7, ['user']);

        $this->withIdentity()
            ->postJson('/v1/categories', ['name' => 'Sacs'])
            ->assertStatus(403);
    }
}
