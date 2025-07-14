<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Afficher la page principale de messagerie
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer toutes les conversations de l'utilisateur
        $conversations = $this->getConversations($user);
        
        // Récupérer tous les utilisateurs pour le modal de nouveau message
        $users = User::where('id', '!=', $user->id)->get();
        
        return view('messages.index', compact('conversations', 'users'));
    }

    /**
     * Afficher une conversation spécifique
     */
    public function show($conversationId): JsonResponse
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        $messages = Message::where(function($query) use ($user, $conversationId) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $conversationId);
        })->orWhere(function($query) use ($user, $conversationId) {
            $query->where('sender_id', $conversationId)
                  ->where('receiver_id', $user->id);
        })->orderBy('created_at', 'asc')->get();
        
        // Marquer les messages comme lus
        $messages->where('receiver_id', $user->id)->each(function($message) {
            $message->update(['read_at' => now(), 'is_read' => true]);
        });
        
        // Récupérer les informations de l'autre utilisateur
        $otherUser = User::find($conversationId);
        
        return response()->json([
            'messages' => $messages,
            'user' => $otherUser
        ]);
    }

    /**
     * Créer un nouveau message
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240' // 10 Mo max
        ]);

        $user = Auth::user();
        $recipientId = $request->recipient_id;

        // Restriction : un vendeur ne peut écrire qu'à ses clients
        // Un vendeur = utilisateur qui a vendu au moins un article (Order où il est seller)
        $isSeller = \App\Models\Order::where('seller_id', $user->id)->exists();
        if ($isSeller) {
            // Récupérer les IDs des acheteurs de ce vendeur
            $clientIds = \App\Models\Order::where('seller_id', $user->id)->pluck('buyer_id')->unique();
            if (!$clientIds->contains($recipientId)) {
                return response()->json(['success' => false, 'error' => 'Vous ne pouvez écrire qu’à vos clients.'], 403);
            }
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
        }

        // On autorise l'envoi d'un message vide si un fichier est joint
        if (empty($request->content) && !$attachmentPath) {
            return response()->json(['success' => false, 'error' => 'Message vide.'], 422);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $recipientId,
            'content' => $request->content,
            'attachment' => $attachmentPath
        ]);

        // Créer une notification pour le destinataire
        $this->notificationService->createMessageNotification(
            $user->id,
            $recipientId,
            $request->content ?? ($attachmentPath ? 'Fichier joint' : '')
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->back()->with('success', 'Message envoyé avec succès');
    }

    /**
     * Marquer un message comme lu
     */
    public function markAsRead(Message $message): JsonResponse
    {
        if ($message->receiver_id === Auth::id()) {
            $message->update(['read_at' => now(), 'is_read' => true]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Compter les messages non lus
     */
    public function unreadCount(): JsonResponse
    {
        $count = Message::where('receiver_id', Auth::id())
                       ->where('is_read', false)
                       ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Récupérer les notifications en temps réel
     */
    public function getNotifications(): JsonResponse
    {
        $user = Auth::user();
        $notifications = $this->notificationService->getUnreadNotifications($user->id, 5);
        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }

    /**
     * Marquer une notification comme lue
     */
    public function markNotificationAsRead($id): JsonResponse
    {
        $this->notificationService->markAsRead($id, Auth::id());
        return response()->json(['success' => true]);
    }

    /**
     * Récupérer les conversations de l'utilisateur
     */
    private function getConversations($user)
    {
        // Récupérer tous les utilisateurs avec qui l'utilisateur a échangé des messages
        $conversationUsers = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->get()
            ->map(function($message) use ($user) {
                return $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;
            })
            ->unique();

        $conversations = collect();

        foreach ($conversationUsers as $otherUserId) {
            $otherUser = User::find($otherUserId);
            
            // Récupérer le dernier message
            $lastMessage = Message::where(function($query) use ($user, $otherUserId) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $otherUserId);
            })->orWhere(function($query) use ($user, $otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('receiver_id', $user->id);
            })->orderBy('created_at', 'desc')->first();

            // Compter les messages non lus
            $unreadCount = Message::where('sender_id', $otherUserId)
                                 ->where('receiver_id', $user->id)
                                 ->where('is_read', false)
                                 ->count();

            $conversations->push((object) [
                'id' => $otherUserId,
                'other_user' => $otherUser,
                'last_message' => $lastMessage,
                'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                'unread_count' => $unreadCount
            ]);
        }

        // Trier par dernier message
        return $conversations->sortByDesc('last_message.created_at');
    }
}
