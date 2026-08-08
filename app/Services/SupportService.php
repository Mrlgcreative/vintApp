<?php

namespace App\Services;

use App\Models\SupportAgent;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class SupportService
{
    /**
     * Répondre à une conversation de support.
     *
     * Le message admin assigne le chat si nécessaire puis le passe en cours ;
     * le message utilisateur réactive un chat en attente de réponse utilisateur.
     *
     * @param array $files Fichiers joints (UploadedFile[])
     */
    public function replyToChat(SupportChat $chat, int $senderId, string $message, bool $isAdmin, array $files = []): SupportMessage
    {
        return DB::transaction(function () use ($chat, $senderId, $message, $isAdmin, $files) {
            $attachments = $this->handleAttachments($files);

            $supportMessage = SupportMessage::createNew(
                $chat->id,
                $senderId,
                $message,
                $isAdmin,
                !empty($attachments) ? $attachments : null
            );

            if ($isAdmin) {
                if (!$chat->admin_id) {
                    $chat->update(['admin_id' => $senderId, 'status' => 'in_progress']);
                } elseif ($chat->status === 'open' || $chat->status === 'waiting_user') {
                    $chat->update(['status' => 'in_progress']);
                }
            } elseif ($chat->status === 'waiting_user') {
                $chat->update(['status' => 'in_progress']);
            }

            return $supportMessage;
        });
    }

    /**
     * Fermer une conversation de support.
     */
    public function closeChat(SupportChat $chat): void
    {
        $chat->close();
    }

    /**
     * Assigner une conversation à un admin/agent.
     */
    public function assignChat(SupportChat $chat, int $adminId): void
    {
        $chat->assignToAdmin($adminId);
    }

    /**
     * Auto-assigner un ticket à l'agent le moins chargé.
     *
     * @throws DomainException si déjà assigné ou aucun agent disponible
     */
    public function autoAssignChat(SupportChat $chat): SupportAgent
    {
        if ($chat->admin_id) {
            throw new DomainException('Ce ticket est déjà assigné.');
        }

        $agent = SupportAgent::leastLoaded($chat->category);

        if (!$agent) {
            throw new DomainException('Aucun agent disponible pour ce ticket.');
        }

        $chat->assignToAdmin($agent->user_id);
        $agent->update(['last_assigned_at' => now()]);

        return $agent;
    }

    /**
     * Prendre un ticket (self-assign) pour un agent.
     *
     * @throws DomainException si déjà assigné ou limite de tickets atteinte
     */
    public function claimChat(SupportChat $chat, int $userId): void
    {
        if ($chat->admin_id) {
            throw new DomainException('Ce ticket est déjà assigné.');
        }

        $agent = SupportAgent::where('user_id', $userId)->first();
        if ($agent && !$agent->canAcceptChats()) {
            throw new DomainException('Vous avez atteint votre limite de tickets.');
        }

        $chat->assignToAdmin($userId);
        if ($agent) {
            $agent->update(['last_assigned_at' => now()]);
        }
    }

    /**
     * Statistiques de support d'un utilisateur (API).
     */
    public function getUserStats(int $userId): array
    {
        return [
            'total_chats' => SupportChat::where('user_id', $userId)->count(),
            'open_chats' => SupportChat::where('user_id', $userId)
                ->whereIn('status', ['open', 'in_progress', 'waiting_user'])
                ->count(),
            'closed_chats' => SupportChat::where('user_id', $userId)
                ->where('status', 'closed')
                ->count(),
            'average_response_time' => null,
        ];
    }

    /**
     * Statistiques globales (dashboard admin / API).
     */
    public function getGlobalStats(): array
    {
        return [
            'total' => SupportChat::count(),
            'open' => SupportChat::where('status', 'open')->count(),
            'in_progress' => SupportChat::where('status', 'in_progress')->count(),
            'waiting_user' => SupportChat::where('status', 'waiting_user')->count(),
            'closed_today' => SupportChat::where('status', 'closed')
                ->whereDate('closed_at', today())
                ->count(),
            'unassigned' => SupportChat::whereNull('admin_id')
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
        ];
    }

    /**
     * Statistiques détaillées du support (page stats admin).
     */
    public function getDetailedStats($startDate): array
    {
        return [
            'overview' => [
                'total_chats' => SupportChat::count(),
                'open_chats' => SupportChat::where('status', 'open')->count(),
                'in_progress_chats' => SupportChat::where('status', 'in_progress')->count(),
                'closed_chats' => SupportChat::where('status', 'closed')->count(),
                'avg_response_time' => $this->calculateAverageResponseTime($startDate),
                'avg_resolution_time' => $this->calculateAverageResolutionTime($startDate),
            ],
            'by_category' => SupportChat::select('category', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('category')
                ->get(),
            'by_priority' => SupportChat::select('priority', DB::raw('count(*) as count'))
                ->where('created_at', '>=', $startDate)
                ->groupBy('priority')
                ->get(),
            'daily_stats' => $this->getDailyStats($startDate),
            'admin_performance' => $this->getAdminPerformance($startDate),
        ];
    }

    /**
     * Uploader les pièces jointes et les synchroniser sur le stockage public.
     */
    private function handleAttachments(array $files): array
    {
        $attachments = [];

        foreach ($files as $file) {
            $path = $file->store('support/attachments', 'public');
            StorageSyncService::syncFile($path);
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
            ];
        }

        return $attachments;
    }

    /**
     * Calculer le temps de réponse moyen (création du chat → 1re réponse admin).
     */
    private function calculateAverageResponseTime($startDate)
    {
        $chats = SupportChat::where('created_at', '>=', $startDate)
            ->whereNotNull('admin_id')
            ->with(['messages' => function ($query) {
                $query->where('is_admin', true)->orderBy('created_at', 'asc')->limit(1);
            }])
            ->get();

        $totalTime = 0;
        $count = 0;

        foreach ($chats as $chat) {
            $firstAdminMessage = $chat->messages->first();
            if ($firstAdminMessage) {
                $totalTime += $chat->created_at->diffInMinutes($firstAdminMessage->created_at);
                $count++;
            }
        }

        return $count > 0 ? round($totalTime / $count, 2) : 0;
    }

    /**
     * Calculer le temps de résolution moyen (création → fermeture).
     */
    private function calculateAverageResolutionTime($startDate)
    {
        $closedChats = SupportChat::where('status', 'closed')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('closed_at')
            ->get();

        $totalTime = 0;
        $count = $closedChats->count();

        foreach ($closedChats as $chat) {
            $totalTime += $chat->created_at->diffInHours($chat->closed_at);
        }

        return $count > 0 ? round($totalTime / $count, 2) : 0;
    }

    /**
     * Statistiques quotidiennes jour par jour depuis startDate.
     */
    private function getDailyStats($startDate)
    {
        $stats = [];
        $currentDate = $startDate->copy();

        while ($currentDate <= now()) {
            $date = $currentDate->format('Y-m-d');
            $stats[] = [
                'date' => $date,
                'new_chats' => SupportChat::whereDate('created_at', $currentDate)->count(),
                'closed_chats' => SupportChat::whereDate('closed_at', $currentDate)->count(),
                'messages' => SupportMessage::whereDate('created_at', $currentDate)->count(),
            ];
            $currentDate->addDay();
        }

        return $stats;
    }

    /**
     * Performances des admins (tickets assignés / résolus).
     */
    private function getAdminPerformance($startDate)
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->where('slug', 'admin');
        });

        // Fallback : si aucun admin avec rôle, tous les utilisateurs pour les stats
        if ($admins->count() == 0) {
            $admins = User::query();
        }

        return $admins->withCount([
            'assignedSupportChats as total_assigned' => function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            },
            'assignedSupportChats as closed_chats' => function ($query) use ($startDate) {
                $query->where('status', 'closed')
                    ->where('created_at', '>=', $startDate);
            },
        ])->get();
    }
}
