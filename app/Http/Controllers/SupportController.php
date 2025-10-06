<?php

namespace App\Http\Controllers;

use App\Models\SupportChat;
use App\Models\SupportMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupportController extends Controller
{
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
}