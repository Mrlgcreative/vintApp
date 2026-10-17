<?php

namespace Tests\Unit;

use App\Models\Notification;
use App\Models\User;
use App\Services\AiAssistantService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAssistantServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_includes_the_current_users_notifications_and_not_other_users()
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        Notification::create([
            'user_id' => $user->id,
            'type' => 'order',
            'title' => 'Commande expédiée',
            'message' => 'Votre colis est en route.',
        ]);
        Notification::create([
            'user_id' => $other->id,
            'type' => 'order',
            'title' => 'CONFIDENTIEL_AUTRE_UTILISATEUR',
            'message' => 'Ne doit jamais fuiter.',
        ]);

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'ok', 'tool_calls' => []]]],
            ]),
        ]);

        app(AiAssistantService::class)->ask('ai-je des notifications ?', [], $user);

        Http::assertSent(function ($request) {
            $system = $request->data()['messages'][0]['content'] ?? '';

            return str_contains($system, '== Notifications ==')
                && str_contains($system, 'Non lues : 1')
                && str_contains($system, 'Commande expédiée')
                && ! str_contains($system, 'CONFIDENTIEL_AUTRE_UTILISATEUR');
        });
    }

    /** @test */
    public function it_reports_no_notifications_when_the_user_has_none()
    {
        $user = User::factory()->create();

        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [['message' => ['content' => 'ok', 'tool_calls' => []]]],
            ]),
        ]);

        app(AiAssistantService::class)->ask('des notifications ?', [], $user);

        Http::assertSent(function ($request) {
            $system = $request->data()['messages'][0]['content'] ?? '';

            return str_contains($system, 'Non lues : 0')
                && str_contains($system, 'Aucune notification.');
        });
    }
}
