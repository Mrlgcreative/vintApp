<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function notification(User $user, array $overrides = []): Notification
    {
        return Notification::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Titre',
            'message' => 'Contenu',
            'type' => 'info',
        ], $overrides));
    }

    /** @test */
    public function user_can_list_his_notifications()
    {
        $user = User::factory()->create();
        $this->notification($user);
        $this->notification($user);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/notifications')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function user_can_get_unread_count()
    {
        $user = User::factory()->create();
        $this->notification($user);
        $this->notification($user, ['read_at' => now()]);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/notifications/unread/count')
            ->assertOk()
            ->assertJsonPath('data.count', 1);
    }

    /** @test */
    public function user_can_mark_a_notification_as_read()
    {
        $user = User::factory()->create();
        $notification = $this->notification($user);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/notifications/' . $notification->id . '/mark-read')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function user_cannot_mark_someone_elses_notification_as_read()
    {
        $notification = $this->notification(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/notifications/' . $notification->id . '/mark-read')
            ->assertStatus(404);
    }

    /** @test */
    public function user_can_mark_all_as_read()
    {
        $user = User::factory()->create();
        $this->notification($user);
        $this->notification($user);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/notifications/mark-all-read')
            ->assertOk()
            ->assertJsonPath('data.marked_count', 2);

        $this->assertSame(2, Notification::where('user_id', $user->id)->whereNotNull('read_at')->count());
    }
}
