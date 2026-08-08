<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiAdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin', 'slug' => 'admin']);
        $user->roles()->attach($role);

        return $user;
    }

    private function nonAdmin(): User
    {
        return User::factory()->create();
    }

    /** @test */
    public function non_admin_is_rejected()
    {
        Sanctum::actingAs($this->nonAdmin());

        $this->getJson('/api/v1/admin/dashboard')->assertForbidden();
    }

    /** @test */
    public function admin_can_get_dashboard()
    {
        Sanctum::actingAs($this->admin());

        $this->getJson('/api/v1/admin/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    /** @test */
    public function admin_can_list_users()
    {
        Sanctum::actingAs($this->admin());

        $this->getJson('/api/v1/admin/users')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    /** @test */
    public function admin_can_get_enterprise_wallets()
    {
        Sanctum::actingAs($this->admin());

        $this->getJson('/api/v1/admin/enterprise-wallets')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    /** @test */
    public function admin_can_get_monitoring_health()
    {
        Sanctum::actingAs($this->admin());

        $this->getJson('/api/v1/admin/monitoring/health')
            ->assertJsonStructure(['status']);
    }
}
