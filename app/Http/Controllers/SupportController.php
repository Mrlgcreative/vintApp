<?php

namespace App\Http\Controllers;

use App\Models\SupportChat;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\StorageSyncService;
use App\Traits\ApiResponses;

class SupportController extends Controller
{
    use ApiResponses;
    /**
     * Afficher les conversations de support de l'utilisateur
     */
    public function index()
    {
        $chats = SupportChat::where('user_id', Auth::id())
            ->with(['admin', 'lastMessage'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('support.index', compact('chats'));
    }

    /**
     * Créer une nouvelle demande de support
     */
    public function create()
    {
        return view('support.create');
    }

    /**
     * Enregistrer une nouvelle demande de support
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:255',
            'category' => 'required|in:technical,account,payment,order,general',
            'message' => 'required|string|max:5000',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max par fichier
        ]);

        try {
            DB::beginTransaction();

            // Collecter les métadonnées
            $metadata = [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->headers->get('referer'),
                'browser' => $this->getBrowserInfo($request->userAgent()),
                'os' => $this->getOSInfo($request->userAgent())
            ];

            // Créer la conversation
            $chat = SupportChat::createNew(
                Auth::id(),
                $request->subject,
                $request->category,
                $metadata
            );

            // Définir la priorité si spécifiée
            if ($request->priority) {
                $chat->update(['priority' => $request->priority]);
            }

            // Gérer les pièces jointes s'il y en a
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

            // Créer le premier message
            SupportMessage::createNew(
                $chat->id,
                Auth::id(),
                $request->message,
                false, // is_admin
                !empty($attachments) ? $attachments : null
            );

            DB::commit();

            // TODO: Notifier les admins de la nouvelle demande

            return redirect()->route('support.show', $chat)
                ->with('success', 'Votre demande de support a été créée avec succès. Référence: ' . $chat->reference);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la demande de support', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de la création de votre demande.')
                ->withInput();
        }
    }

    /**
     * Afficher une conversation de support
     */
    public function show(SupportChat $supportChat)
    {
        // Vérifier que l'utilisateur peut voir cette conversation
        if ($supportChat->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette conversation.');
        }

        $supportChat->load(['admin', 'messages.user']);

        // Marquer les messages des admins comme lus
        $supportChat->messages()
            ->where('is_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('support.show', compact('supportChat'));
    }

    /**
     * Répondre à une conversation
     */
    public function reply(Request $request, SupportChat $supportChat)
    {
        // Vérifier que l'utilisateur peut répondre à cette conversation
        if ($supportChat->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette conversation.');
        }

        // Ne pas permettre de répondre à une conversation fermée
        if ($supportChat->status === 'closed') {
            return redirect()->back()
                ->with('error', 'Cette conversation est fermée. Vous ne pouvez plus y répondre.');
        }

        $request->validate([
            'message' => 'required|string|max:5000',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max par fichier
        ]);

        try {
            DB::beginTransaction();

            // Gérer les pièces jointes s'il y en a
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

            // Créer le message
            SupportMessage::createNew(
                $supportChat->id,
                Auth::id(),
                $request->message,
                false, // is_admin
                !empty($attachments) ? $attachments : null
            );

            // Mettre à jour le statut de la conversation si elle était "en attente utilisateur"
            if ($supportChat->status === 'waiting_user') {
                $supportChat->update(['status' => 'in_progress']);
            }

            DB::commit();

            // TODO: Notifier l'admin assigné de la nouvelle réponse

            return redirect()->route('support.show', $supportChat)
                ->with('success', 'Votre message a été envoyé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'envoi de la réponse utilisateur', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'envoi de votre message.')
                ->withInput();
        }
    }

    /**
     * Fermer une conversation (côté utilisateur)
     */
    public function close(SupportChat $supportChat)
    {
        // Vérifier que l'utilisateur peut fermer cette conversation
        if ($supportChat->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé à cette conversation.');
        }

        try {
            $supportChat->close();

            return response()->json([
                'success' => true,
                'message' => 'Conversation fermée avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la fermeture de la conversation', [
                'error' => $e->getMessage(),
                'support_chat_id' => $supportChat->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la fermeture de la conversation.'
            ], 500);
        }
    }

    /**
     * Widget support (modal popup)
     */
    public function widget()
    {
        $user = Auth::user();
        $openChats = [];
        
        if ($user) {
            $openChats = SupportChat::where('user_id', $user->id)
                ->whereIn('status', ['open', 'in_progress', 'waiting_user'])
                ->with(['lastMessage'])
                ->orderBy('last_message_at', 'desc')
                ->take(3)
                ->get();
        }

        return view('support.widget', compact('openChats'));
    }

    /**
     * Démarrer un chat rapide depuis le widget
     */
    public function quickChat(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous devez être connecté pour utiliser le support.'
            ], 401);
        }

        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            // Vérifier s'il y a déjà une conversation ouverte récente
            $existingChat = SupportChat::where('user_id', Auth::id())
                ->whereIn('status', ['open', 'in_progress'])
                ->where('created_at', '>=', now()->subHours(24))
                ->first();

            if ($existingChat) {
                // Ajouter le message à la conversation existante
                SupportMessage::createNew(
                    $existingChat->id,
                    Auth::id(),
                    $request->message,
                    false
                );

                $chat = $existingChat;
            } else {
                // Créer une nouvelle conversation
                $metadata = [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->headers->get('referer'),
                    'quick_chat' => true
                ];

                $chat = SupportChat::createNew(
                    Auth::id(),
                    'Assistance rapide',
                    'general',
                    $metadata
                );

                // Créer le premier message
                SupportMessage::createNew(
                    $chat->id,
                    Auth::id(),
                    $request->message,
                    false
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Votre message a été envoyé. Un agent vous répondra bientôt.',
                'chat_id' => $chat->id,
                'reference' => $chat->reference
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du chat rapide', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de votre message.'
            ], 500);
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

    // ==================== API Methods ====================

    /**
     * Get user support chats via API
     */
    public function apiIndex(Request $request)
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
     * Create new support chat via API
     */
    public function apiStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
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
     * Get support chat details via API
     */
    public function apiShow(Request $request, $chatId)
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
     * Reply to support chat via API
     */
    public function apiReply(Request $request, $chatId)
    {
        $validator = \Validator::make($request->all(), [
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

            DB::beginTransaction();

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

            $message = SupportMessage::createNew(
                $chat->id,
                $request->user()->id,
                $request->message,
                false,
                !empty($attachments) ? $attachments : null
            );

            if ($chat->status === 'waiting_user') {
                $chat->update(['status' => 'in_progress']);
            }

            DB::commit();

            return $this->successResponse(
                $message->load('user'),
                'Message envoyé avec succès'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('Erreur lors de l\'envoi', 500);
        }
    }

    /**
     * Close support chat via API
     */
    public function apiClose(Request $request, $chatId)
    {
        try {
            $chat = SupportChat::where('id', $chatId)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $chat->close();

            return $this->successResponse(null, 'Conversation fermée avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la fermeture', 500);
        }
    }

    /**
     * Get support statistics via API
     */
    public function apiStats(Request $request)
    {
        try {
            $userId = $request->user()->id;

            $stats = [
                'total_chats' => SupportChat::where('user_id', $userId)->count(),
                'open_chats' => SupportChat::where('user_id', $userId)
                    ->whereIn('status', ['open', 'in_progress', 'waiting_user'])
                    ->count(),
                'closed_chats' => SupportChat::where('user_id', $userId)
                    ->where('status', 'closed')
                    ->count(),
                'average_response_time' => null, // TODO: Calculate based on message timestamps
            ];

            return $this->successResponse($stats, 'Statistiques de support');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération', 500);
        }
    }
}