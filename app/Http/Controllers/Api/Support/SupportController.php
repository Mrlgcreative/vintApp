<?php

namespace App\Http\Controllers\Api\Support;

use App\Http\Controllers\Api\ApiController;
use App\Models\SupportChat;
use App\Models\SupportMessage;
use App\Services\StorageSyncService;
use App\Services\SupportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SupportController extends ApiController
{
    public function __construct(
        private readonly SupportService $supportService
    ) {
    }

    /**
     * API: Conversations de support de l'utilisateur
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $chats = SupportChat::where('user_id', $request->user()->id)
                ->with(['admin', 'lastMessage'])
                ->orderBy('created_at', 'desc')
                ->paginate($request->per_page ?? 15);

            return $this->paginatedResponse($chats, 'Conversations de support récupérées');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    /**
     * API: Créer une demande de support
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'nullable|string|max:255',
            'category' => 'required|in:technical,account,payment,order,general',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            DB::beginTransaction();

            $metadata = [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'browser' => $this->getBrowserInfo($request->userAgent()),
                'os' => $this->getOSInfo($request->userAgent())
            ];

            $chat = SupportChat::createNew(
                $request->user()->id,
                $request->subject,
                $request->category,
                $metadata
            );

            if ($request->priority) {
                $chat->update(['priority' => $request->priority]);
            }

            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('support/attachments', 'public');
                    StorageSyncService::syncFile($path);
                    $attachments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path,
                        'size' => $file->getSize()
                    ];
                }
            }

            SupportMessage::createNew(
                $chat->id,
                $request->user()->id,
                $request->message,
                false,
                !empty($attachments) ? $attachments : null
            );

            DB::commit();

            return $this->successResponse(
                $chat->load(['admin', 'messages.user']),
                'Demande de support créée avec succès. Référence: ' . $chat->reference,
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API Support creation error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Erreur lors de la création', 500);
        }
    }

    /**
     * API: Détails d'une conversation de support
     */
    public function show(Request $request, $chatId): JsonResponse
    {
        try {
            $chat = SupportChat::where('id', $chatId)
                ->where('user_id', $request->user()->id)
                ->with(['admin', 'messages.user'])
                ->firstOrFail();

            // Mark admin messages as read
            $chat->messages()
                ->where('is_admin', true)
                ->where('is_read', false)
                ->update(['is_read' => true, 'read_at' => now()]);

            return $this->successResponse($chat, 'Conversation récupérée');
        } catch (\Exception $e) {
            return $this->errorResponse('Conversation introuvable', 404);
        }
    }

    /**
     * API: Répondre à une conversation de support
     */
    public function reply(Request $request, $chatId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:5000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
        }

        try {
            $chat = SupportChat::where('id', $chatId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            if ($chat->status === 'closed') {
                return $this->errorResponse('Conversation fermée', 400);
            }

            $message = $this->supportService->replyToChat(
                $chat,
                $request->user()->id,
                $request->message,
                false,
                $request->file('attachments') ?? []
            );

            return $this->successResponse(
                $message->load('user'),
                'Message envoyé avec succès'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de l\'envoi', 500);
        }
    }

    /**
     * API: Fermer une conversation de support
     */
    public function close(Request $request, $chatId): JsonResponse
    {
        try {
            $chat = SupportChat::where('id', $chatId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $this->supportService->closeChat($chat);

            return $this->successResponse(null, 'Conversation fermée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la fermeture', 500);
        }
    }

    /**
     * API: Statistiques de support de l'utilisateur
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $stats = $this->supportService->getUserStats($request->user()->id);

            return $this->successResponse($stats, 'Statistiques de support');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }

    /**
     * Obtenir les informations du navigateur
     */
    private function getBrowserInfo($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        if (strpos($userAgent, 'Opera') !== false) return 'Opera';
        return 'Inconnu';
    }

    /**
     * Obtenir les informations du système d'exploitation
     */
    private function getOSInfo($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'macOS';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iOS') !== false) return 'iOS';
        return 'Inconnu';
    }
}
