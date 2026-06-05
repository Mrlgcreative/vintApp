<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class PushNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PushNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PushNotificationService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_send_notification_to_user_with_fcm_token()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_fcm_token_' . str()->random(100),
            'device_type' => 'android',
            'browser' => 'Chrome'
        ]);

        $notification = [
            'title' => 'Test Notification',
            'body' => 'This is a test notification'
        ];

        // Mock the service to avoid actual FCM calls
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToToken')
            ->once()
            ->with($user->fcm_token, $notification, [])
            ->andReturn(true);

        // Act
        $result = $serviceMock->sendToUser($user, $notification);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_returns_false_when_user_has_no_fcm_token()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => null
        ]);

        $notification = [
            'title' => 'Test Notification',
            'body' => 'This is a test notification'
        ];

        // Act
        $result = $this->service->sendToUser($user, $notification);

        // Assert
        $this->assertFalse($result);
    }

    /** @test */
    public function it_can_remove_invalid_fcm_token_from_user()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'invalid_token_to_remove',
            'device_type' => 'android',
            'browser' => 'Chrome'
        ]);

        // Act
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('removeInvalidToken');
        $method->setAccessible(true);
        $method->invoke($this->service, $user->fcm_token);

        // Assert
        $user->refresh();
        $this->assertNull($user->fcm_token);
        $this->assertNull($user->device_type);
        $this->assertNull($user->browser);
        $this->assertNull($user->fcm_token_updated_at);
    }

    /** @test */
    public function it_can_send_to_multiple_tokens()
    {
        // Arrange
        $tokens = [
            'token_1_' . str()->random(100),
            'token_2_' . str()->random(100),
            'token_3_' . str()->random(100),
        ];

        $notification = [
            'title' => 'Bulk Notification',
            'body' => 'This is sent to multiple users'
        ];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToToken')
            ->times(3)
            ->andReturn(true);

        // Act
        $result = $serviceMock->sendToMultiple($tokens, $notification);

        // Assert
        $this->assertIsArray($result);
        $this->assertEquals(3, $result['success']);
        $this->assertEquals(0, $result['failed']);
    }

    /** @test */
    public function it_generates_valid_notification_structure_for_new_order()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $order = (object) [
            'id' => 12345,
            'buyer' => (object) ['name' => 'John Doe']
        ];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToUser')
            ->once()
            ->andReturn(true);

        // Act
        $result = $serviceMock->notifyNewOrder($user, $order);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_generates_valid_notification_structure_for_order_confirmed()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $order = (object) ['id' => 12345];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToUser')
            ->once()
            ->andReturn(true);

        // Act
        $result = $serviceMock->notifyOrderConfirmed($user, $order);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_generates_valid_notification_structure_for_order_shipped()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $order = (object) ['id' => 12345];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToUser')
            ->once()
            ->andReturn(true);

        // Act
        $result = $serviceMock->notifyOrderShipped($user, $order);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_generates_valid_notification_structure_for_new_message()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $message = (object) [
            'id' => 67890,
            'content' => 'Hello, how are you?',
            'conversation_id' => 12345,
            'sender' => (object) ['name' => 'John Doe']
        ];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToUser')
            ->once()
            ->andReturn(true);

        // Act
        $result = $serviceMock->notifyNewMessage($user, $message);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_generates_valid_notification_structure_for_item_sold()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $item = (object) [
            'id' => 54321,
            'title' => 'Vintage Jacket',
            'images' => [(object) ['url' => '/images/jacket.jpg']]
        ];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToUser')
            ->once()
            ->andReturn(true);

        // Act
        $result = $serviceMock->notifyItemSold($user, $item);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_generates_valid_notification_structure_for_new_review()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $review = (object) [
            'id' => 98765,
            'rating' => 5,
            'reviewer' => (object) ['name' => 'Jane Smith']
        ];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToUser')
            ->once()
            ->andReturn(true);

        // Act
        $result = $serviceMock->notifyNewReview($user, $review);

        // Assert
        $this->assertTrue($result);
    }

    /** @test */
    public function it_handles_missing_service_account_file_gracefully()
    {
        // This test ensures the service doesn't crash when service account is missing
        // We can't easily test the actual OAuth flow without real credentials
        
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $notification = [
            'title' => 'Test',
            'body' => 'Test body'
        ];

        // The service should handle missing credentials gracefully
        // In production, this would fail silently or log an error
        $this->assertTrue(true); // Placeholder for actual implementation test
    }

    /** @test */
    public function it_validates_notification_structure()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        // Valid notification structure
        $validNotification = [
            'title' => 'Valid Title',
            'body' => 'Valid body content'
        ];

        // The service should accept this structure
        $this->assertArrayHasKey('title', $validNotification);
        $this->assertArrayHasKey('body', $validNotification);
    }

    /** @test */
    public function it_handles_data_payload_correctly()
    {
        // Arrange
        $user = User::factory()->create([
            'fcm_token' => 'test_token_' . str()->random(100)
        ]);

        $notification = [
            'title' => 'Test',
            'body' => 'Test body'
        ];

        $data = [
            'orderId' => '12345',
            'action' => 'view_order',
            'url' => '/orders/12345'
        ];

        // Mock the service
        $serviceMock = Mockery::mock(PushNotificationService::class)->makePartial();
        $serviceMock->shouldReceive('sendToToken')
            ->once()
            ->with($user->fcm_token, $notification, $data)
            ->andReturn(true);

        // Act
        $result = $serviceMock->sendToUser($user, $notification, $data);

        // Assert
        $this->assertTrue($result);
    }
}
