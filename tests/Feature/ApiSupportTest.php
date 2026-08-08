<?php

namespace Tests\Feature;

use App\Models\SupportChat;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSupportTest extends TestCase
{
    use RefreshDatabase;

    private function chat(User $user, array $overrides = []): SupportChat
    {
        return SupportChat::create(array_merge([
            'user_id' => $user->id,
            'reference' => 'SUP-' . strtoupper(uniqid()),
            'category' => 'general',
            'status' => 'open',
        ], $overrides));
    }

    /** @test */
    public function user_can_create_a_support_request()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/support', [
            'category' => 'account',
            'message' => 'Problème de connexion',
        ])->assertStatus(201)
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('support_chats', ['user_id' => $user->id]);
        $this->assertDatabaseHas('support_messages', ['message' => 'Problème de connexion']);
    }

    /** @test */
    public function user_cannot_create_support_without_category()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/support', [
            'message' => 'Test',
        ])->assertStatus(422);
    }

    /** @test */
    public function user_can_list_his_support_chats()
    {
        $user = User::factory()->create();
        $this->chat($user);
        $this->chat($user);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/support')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function user_cannot_see_someone_elses_support_chat()
    {
        $chat = $this->chat(User::factory()->create());
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/support/' . $chat->id)
            ->assertStatus(404);
    }

    /** @test */
    public function user_can_close_his_support_chat()
    {
        $user = User::factory()->create();
        $chat = $this->chat($user);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/support/' . $chat->id . '/close')
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('closed', $chat->fresh()->status);
    }
}
