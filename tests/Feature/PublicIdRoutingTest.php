<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Route as RouteInstance;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\TestCase;

class PublicIdRoutingTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin', 'slug' => 'admin']);
        $user->roles()->attach($role);

        return $user;
    }

    private function walletFor(User $user): Wallet
    {
        return Wallet::create([
            'user_id' => $user->id,
            'currency' => 'USD',
            'type' => 'enterprise',
            'status' => 'active',
            'balance' => 0,
            'is_active' => true,
        ]);
    }

    private function bind(string $routeName, string $value): RouteInstance
    {
        $route = Route::getRoutes()->getByName($routeName);
        $route->bind(Request::create('/admin/wallets/' . $value, 'GET'));
        app('router')->substituteImplicitBindings($route);

        return $route;
    }

    public function test_creating_a_model_assigns_a_ulid_public_id(): void
    {
        $wallet = $this->walletFor(User::factory()->create());

        $this->assertNotNull($wallet->public_id);
        $this->assertSame(26, strlen($wallet->public_id));
        $this->assertMatchesRegularExpression('/^[0-9A-HJKMNP-TV-Z]{26}$/', $wallet->public_id);
    }

    public function test_public_ids_are_unique_across_rows(): void
    {
        $user = User::factory()->create();

        $ids = collect(range(1, 5))
            ->map(fn () => $this->walletFor($user)->public_id)
            ->all();

        $this->assertCount(5, array_unique($ids));
    }

    public function test_public_id_is_stored_and_reloadable_from_database(): void
    {
        $wallet = $this->walletFor(User::factory()->create());

        $reloaded = Wallet::find($wallet->id);

        $this->assertSame($wallet->public_id, $reloaded->public_id);
    }

    public function test_route_generation_uses_the_public_id_not_the_numeric_id(): void
    {
        $wallet = $this->walletFor(User::factory()->create());

        $url = route('admin.wallets.show', $wallet);

        $this->assertStringContainsString($wallet->public_id, $url);
        $this->assertStringNotContainsString('/' . $wallet->id, $url);
    }

    public function test_route_model_binding_resolves_the_public_id(): void
    {
        $wallet = $this->walletFor(User::factory()->create());

        $bound = $this->bind('admin.wallets.show', $wallet->public_id)->parameter('wallet');

        $this->assertInstanceOf(Wallet::class, $bound);
        $this->assertTrue($bound->is($wallet));
    }

    public function test_numeric_id_is_no_longer_accepted_by_the_router(): void
    {
        $wallet = $this->walletFor(User::factory()->create());

        $this->expectException(ModelNotFoundException::class);

        $this->bind('admin.wallets.show', (string) $wallet->id);
    }

    public function test_unknown_public_id_yields_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->bind('admin.wallets.show', '01AAAAAAAAAAAAAAAAAAAAAAAA');
    }

    public function test_public_id_is_the_route_key_for_models(): void
    {
        $wallet = $this->walletFor(User::factory()->create());

        $this->assertSame('public_id', $wallet->getRouteKeyName());
        $this->assertSame($wallet->public_id, $wallet->getRouteKey());
    }
}
