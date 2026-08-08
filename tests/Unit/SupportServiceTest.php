<?php

namespace Tests\Unit;

use App\Models\SupportAgent;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use App\Models\User;
use App\Services\SupportService;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SupportService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SupportService::class);
    }

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function makeChat(User $user, array $overrides = []): SupportChat
    {
        return SupportChat::create(array_merge([
            'reference' => 'SUP-' . uniqid(),
            'user_id' => $user->id,
            'subject' => 'Demande',
            'status' => 'open',
            'priority' => 'normal',
            'category' => 'general',
            'last_message_at' => now(),
        ], $overrides));
    }

    private function makeAgent(User $user, array $overrides = []): SupportAgent
    {
        return SupportAgent::create(array_merge([
            'user_id' => $user->id,
            'is_active' => true,
            'max_chats' => 10,
            'specialties' => ['general'],
        ], $overrides));
    }

    /** @test */
    public function it_replies_as_admin_and_assigns_an_unassigned_chat()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();
        $chat = $this->makeChat($user);

        $message = $this->service->replyToChat($chat, $admin->id, 'Bonjour, je vous aide.', true);
        $chat->refresh();

        $this->assertSame($admin->id, $message->user_id);
        $this->assertTrue($message->is_admin);
        $this->assertSame($chat->admin_id, $admin->id);
        $this->assertSame('in_progress', $chat->status);
        $this->assertDatabaseHas('support_messages', ['support_chat_id' => $chat->id, 'message' => 'Bonjour, je vous aide.']);
    }

    /** @test */
    public function it_replies_as_admin_keeping_existing_assignment()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();
        $otherAdmin = $this->makeUser();
        $chat = $this->makeChat($user, ['admin_id' => $otherAdmin->id, 'status' => 'open']);

        $this->service->replyToChat($chat, $admin->id, 'Réponse', true);
        $chat->refresh();

        $this->assertSame($otherAdmin->id, $chat->admin_id);
        $this->assertSame('in_progress', $chat->status);
    }

    /** @test */
    public function it_reactivates_a_waiting_user_chat_on_user_reply()
    {
        $user = $this->makeUser();
        $chat = $this->makeChat($user, ['status' => 'waiting_user']);

        $this->service->replyToChat($chat, $user->id, 'Je reviens vers vous.', false);
        $chat->refresh();

        $this->assertSame('in_progress', $chat->status);
    }

    /** @test */
    public function it_keeps_an_open_chat_status_on_user_reply()
    {
        $user = $this->makeUser();
        $chat = $this->makeChat($user, ['status' => 'open']);

        $this->service->replyToChat($chat, $user->id, 'Nouveau message.', false);
        $chat->refresh();

        $this->assertSame('open', $chat->status);
    }

    /** @test */
    public function it_closes_a_chat()
    {
        $user = $this->makeUser();
        $chat = $this->makeChat($user);

        $this->service->closeChat($chat);
        $chat->refresh();

        $this->assertSame('closed', $chat->status);
        $this->assertNotNull($chat->closed_at);
    }

    /** @test */
    public function it_assigns_a_chat_to_an_admin()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();
        $chat = $this->makeChat($user);

        $this->service->assignChat($chat, $admin->id);
        $chat->refresh();

        $this->assertSame($admin->id, $chat->admin_id);
        $this->assertSame('in_progress', $chat->status);
    }

    /** @test */
    public function it_auto_assigns_to_the_least_loaded_agent()
    {
        $user = $this->makeUser();
        $agentUser = $this->makeUser();
        $this->makeAgent($agentUser);
        $chat = $this->makeChat($user);

        $agent = $this->service->autoAssignChat($chat);
        $chat->refresh();

        $this->assertSame($agentUser->id, $agent->user_id);
        $this->assertSame($agentUser->id, $chat->admin_id);
        $this->assertNotNull($agent->last_assigned_at);
    }

    /** @test */
    public function it_refuses_to_auto_assign_an_already_assigned_chat()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();
        $chat = $this->makeChat($user, ['admin_id' => $admin->id]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Ce ticket est déjà assigné.');

        $this->service->autoAssignChat($chat);
    }

    /** @test */
    public function it_refuses_to_auto_assign_when_no_agent_is_available()
    {
        $user = $this->makeUser();
        $chat = $this->makeChat($user, ['category' => 'order']);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Aucun agent disponible pour ce ticket.');

        $this->service->autoAssignChat($chat);
    }

    /** @test */
    public function it_claims_a_chat_as_agent()
    {
        $user = $this->makeUser();
        $agentUser = $this->makeUser();
        $this->makeAgent($agentUser);
        $chat = $this->makeChat($user);

        $this->service->claimChat($chat, $agentUser->id);
        $chat->refresh();

        $this->assertSame($agentUser->id, $chat->admin_id);
        $this->assertSame('in_progress', $chat->status);
    }

    /** @test */
    public function it_refuses_to_claim_an_already_assigned_chat()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();
        $agentUser = $this->makeUser();
        $this->makeAgent($agentUser);
        $chat = $this->makeChat($user, ['admin_id' => $admin->id]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Ce ticket est déjà assigné.');

        $this->service->claimChat($chat, $agentUser->id);
    }

    /** @test */
    public function it_refuses_to_claim_when_agent_limit_is_reached()
    {
        $user = $this->makeUser();
        $agentUser = $this->makeUser();
        $this->makeAgent($agentUser, ['max_chats' => 0]);
        $chat = $this->makeChat($user);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Vous avez atteint votre limite de tickets.');

        $this->service->claimChat($chat, $agentUser->id);
    }

    /** @test */
    public function it_returns_user_stats()
    {
        $user = $this->makeUser();
        $other = $this->makeUser();

        $this->makeChat($user);
        $this->makeChat($user, ['status' => 'closed', 'closed_at' => now()]);
        $this->makeChat($user, ['status' => 'in_progress']);
        $this->makeChat($other);

        $stats = $this->service->getUserStats($user->id);

        $this->assertSame(3, $stats['total_chats']);
        $this->assertSame(2, $stats['open_chats']);
        $this->assertSame(1, $stats['closed_chats']);
    }

    /** @test */
    public function it_returns_global_stats()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();

        $this->makeChat($user);
        $this->makeChat($user, ['status' => 'closed', 'closed_at' => now()]);
        $this->makeChat($user, ['status' => 'in_progress', 'admin_id' => $admin->id]);

        $stats = $this->service->getGlobalStats();

        $this->assertSame(3, $stats['total']);
        $this->assertSame(1, $stats['open']);
        $this->assertSame(1, $stats['in_progress']);
        $this->assertSame(1, $stats['closed_today']);
        $this->assertSame(1, $stats['unassigned']);
    }

    /** @test */
    public function it_returns_detailed_stats()
    {
        $user = $this->makeUser();
        $admin = $this->makeUser();

        $chat = $this->makeChat($user);
        // created_at n'est pas fillable : forcer la date via query builder
        SupportChat::where('id', $chat->id)->update(['created_at' => now()->subMinutes(5)]);
        $chat->refresh();
        $this->service->replyToChat($chat, $admin->id, 'Première réponse', true);

        $stats = $this->service->getDetailedStats(now()->subDay());

        $this->assertSame(1, $stats['overview']['total_chats']);
        $this->assertSame(0, $stats['overview']['closed_chats']);
        $this->assertCount(1, $stats['by_category']);
        $this->assertCount(1, $stats['by_priority']);
        $this->assertGreaterThanOrEqual(1, count($stats['daily_stats']));
        $this->assertGreaterThanOrEqual(1, $stats['overview']['avg_response_time']);
    }
}
