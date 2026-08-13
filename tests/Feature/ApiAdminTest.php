<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\UserSession;
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

    /** @test */
    public function mobile_user_is_tracked_and_last_seen_updated()
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/user/profile')->assertOk();

        $session = UserSession::first();
        $this->assertNotNull($session);
        $this->assertSame($user->id, $session->user_id);
        $this->assertSame('sanctum-' . $user->tokens()->first()->id, $session->session_id);
        $this->assertTrue($session->is_active);
        $this->assertNotNull($user->fresh()->last_seen);
    }

    /** @test */
    public function api_logout_marks_session_inactive()
    {
        $user = User::factory()->create();
        $token = $user->createToken('mobile')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/user/profile')->assertOk();
        $this->assertTrue(UserSession::first()->is_active);

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertFalse(UserSession::first()->is_active);
        $this->assertNotNull(UserSession::first()->logout_at);
    }

    /** @test */
    public function admin_can_get_online_users_with_valid_structure()
    {
        $admin = $this->admin();
        $user = User::factory()->create();
        $user->createToken('mobile');
        $user->userSessions()->create([
            'session_id' => 'sanctum-123',
            'ip_address' => '127.0.0.1',
            'device_type' => 'mobile',
            'browser' => 'Chrome',
            'os' => 'Android',
            'last_activity' => now(),
            'is_active' => true,
        ]);

        Sanctum::actingAs($admin);

        $this->getJson('/api/v1/admin/online-users')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'users' => [[
                        'user_id',
                        'user_name',
                        'user_email',
                        'device_type',
                        'browser',
                        'os',
                        'last_activity',
                        'ip_address',
                        'location',
                    ]],
                    'stats' => ['total_online', 'unique_users'],
                ],
            ]);
    }
}
