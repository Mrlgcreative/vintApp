<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiMessagesTest extends TestCase
{
    use RefreshDatabase;

    private function message(User $sender, User $receiver, string $content = 'Salut'): Message
    {
        return Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => $content,
            'type' => 'text',
        ]);
    }

    /** @test */
    public function user_can_list_his_conversations()
    {
        $user = User::factory()->create();
        $contact = User::factory()->create();
        $this->message($contact, $user);
        $this->message($user, $contact, 'Réponse');
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/messages')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.contact_id', $contact->id);
    }

    /** @test */
    public function user_can_send_a_message()
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/messages', [
            'recipient_id' => $receiver->id,
            'content' => 'Bonjour',
        ])->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.sender_id', $user->id)
            ->assertJsonPath('data.receiver_id', $receiver->id);

        $this->assertDatabaseHas('messages', [
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'content' => 'Bonjour',
        ]);
    }

    /** @test */
    public function user_cannot_send_empty_message()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/messages', [
            'recipient_id' => User::factory()->create()->id,
            'content' => '  ',
        ])->assertStatus(422);
    }

    /** @test */
    public function user_cannot_mark_someone_elses_message_as_read()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $message = $this->message($sender, $receiver);
        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/v1/messages/' . $message->id . '/mark-read')
            ->assertStatus(403);
    }

    /** @test */
    public function user_can_get_unread_count()
    {
        $user = User::factory()->create();
        $this->message(User::factory()->create(), $user);
        $this->message(User::factory()->create(), $user);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/messages/unread/count')
            ->assertOk()
            ->assertJsonPath('data.count', 2);
    }
}
