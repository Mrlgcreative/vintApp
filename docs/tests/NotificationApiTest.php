<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function authenticated_user_can_subscribe_to_push_notifications()
    {
        // Arrange
        $user = User::factory()->create();
        $fcmToken = 'test_fcm_token_' . str()->random(100);

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/subscribe', [
            'fcm_token' => $fcmToken,
            'device_type' => 'android',
            'browser' => 'Chrome'
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Abonnement aux notifications réussi'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => $fcmToken,
            'device_type' => 'android',
            'browser' => 'Chrome'
        ]);
    }

    /** @test */
    public function guest_cannot_subscribe_to_push_notifications()
    {
        // Act
        $response = $this->postJson('/api/notifications/subscribe', [
            'fcm_token' => 'test_token',
            'device_type' => 'android'
        ]);

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function subscribe_requires_fcm_token()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/subscribe', [
            'device_type' => 'android'
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['fcm_token']);
    }

    /** @test */
    public function authenticated_user_can_unsubscribe_from_push_notifications()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'existing_token',
            'device_type' => 'android',
            'browser' => 'Chrome'
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/unsubscribe');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Désabonnement réussi'
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'fcm_token' => null,
            'device_type' => null,
            'browser' => null
        ]);
    }

    /** @test */
    public function guest_cannot_unsubscribe_from_push_notifications()
    {
        // Act
        $response = $this->postJson('/api/notifications/unsubscribe');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_send_test_notification()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100),
            'device_type' => 'android'
        ]);

        // Mock the PushNotificationService
        $this->mock(PushNotificationService::class, function ($mock) use ($user) {
            $mock->shouldReceive('sendToUser')
                ->once()
                ->with(
                    Mockery::on(fn($u) => $u->id === $user->id),
                    Mockery::type('array'),
                    Mockery::type('array')
                )
                ->andReturn(true);
        });

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/test');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Notification de test envoyée avec succès'
            ]);
    }

    /** @test */
    public function test_notification_fails_when_user_has_no_fcm_token()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => null
        ]);

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/test');

        // Assert
        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Aucun token FCM enregistré pour cet utilisateur'
            ]);
    }

    /** @test */
    public function guest_can_view_test_notification_info()
    {
        // Act
        $response = $this->getJson('/api/notifications/test');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'endpoints',
                'instructions'
            ]);
    }

    /** @test */
    public function authenticated_admin_can_broadcast_test_notification()
    {
        // Arrange
        $admin = User::factory()->create();
        $users = User::factory()->count(3)->create([
            'fcm_token' => 'token_' . str()->random(100),
            'device_type' => 'android'
        ]);

        // Mock the PushNotificationService
        $this->mock(PushNotificationService::class, function ($mock) {
            $mock->shouldReceive('sendToUser')
                ->times(3)
                ->andReturn(true);
        });

        // Act
        $response = $this->actingAs($admin)->postJson('/api/notifications/broadcast-test');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'stats' => [
                    'total_users',
                    'success',
                    'failed'
                ]
            ]);
    }

    /** @test */
    public function notification_closed_tracking_works()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/closed', [
            'tag' => 'test-notification-123',
            'timestamp' => now()->timestamp
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function guest_cannot_track_notification_closed()
    {
        // Act
        $response = $this->postJson('/api/notifications/closed', [
            'tag' => 'test-notification-123'
        ]);

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function subscribe_updates_existing_token_if_user_already_subscribed()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'old_token',
            'device_type' => 'ios',
            'browser' => 'Safari'
        ]);

        $newToken = 'new_token_' . str()->random(100);

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/subscribe', [
            'fcm_token' => $newToken,
            'device_type' => 'android',
            'browser' => 'Chrome'
        ]);

        // Assert
        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals($newToken, $user->fcm_token);
        $this->assertEquals('android', $user->device_type);
        $this->assertEquals('Chrome', $user->browser);
        $this->assertNotNull($user->fcm_token_updated_at);
    }

    /** @test */
    public function subscribe_handles_empty_device_type_and_browser()
    {
        // Arrange
        $user = User::factory()->create();
        $fcmToken = 'test_token_' . str()->random(100);

        // Act
        $response = $this->actingAs($user)->postJson('/api/notifications/subscribe', [
            'fcm_token' => $fcmToken,
            'device_type' => '',
            'browser' => ''
        ]);

        // Assert
        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals($fcmToken, $user->fcm_token);
        $this->assertNull($user->device_type);
        $this->assertNull($user->browser);
    }

    /** @test */
    public function broadcast_test_returns_zero_when_no_users_have_tokens()
    {
        // Arrange
        $admin = User::factory()->create();
        User::factory()->count(3)->create([
            'fcm_token' => null
        ]);

        // Act
        $response = $this->actingAs($admin)->postJson('/api/notifications/broadcast-test');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'stats' => [
                    'total_users' => 0,
                    'success' => 0,
                    'failed' => 0
                ]
            ]);
    }
}
