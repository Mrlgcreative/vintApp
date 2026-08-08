<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiUsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_get_his_profile()
    {
        $user = User::factory()->create(['name' => 'Alice']);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/user/profile')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.name', 'Alice')
            ->assertJsonStructure(['data' => ['user', 'stats']]);
    }

    /** @test */
    public function user_can_update_his_profile()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->putJson('/api/v1/user/profile', ['name' => 'Nouveau Nom'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nouveau Nom');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nouveau Nom']);
    }

    /** @test */
    public function user_can_get_his_stats()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/user/stats')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['total_items', 'favorites_count']]);
    }

    /** @test */
    public function user_can_delete_his_account()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/v1/user/account')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function unauthenticated_user_is_rejected()
    {
        $this->getJson('/api/v1/user/profile')->assertUnauthorized();
    }
}
