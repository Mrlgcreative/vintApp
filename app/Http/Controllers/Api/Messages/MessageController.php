<?php

namespace App\Http\Controllers\Api\Messages;

use App\Events\MessageSent;
use App\Http\Controllers\Api\ApiController;
use App\Models\Message;
use App\Models\User;
use App\Services\DiscountService;
use App\Services\NotificationService;
use App\Services\StorageSyncService;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessageController extends ApiController
{
    private $notificationService;
    private $discountService;

    public function __construct(NotificationService $notificationService, DiscountService $discountService)
    {
        $this->notificationService = $notificationService;
        $this->discountService = $discountService;
    }

    /**
     * API: Liste des conversations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Récupérer tous les utilisateurs avec qui on a échangé des messages
            $conversations = collect();

            // Messages envoyés
            $sentMessages = Message::where('sender_id', $user->id)
                ->select('receiver_id')
                ->distinct()
                ->pluck('receiver_id');

            // Messages reçus
            $receivedMessages = Message::where('receiver_id', $user->id)
                ->select('sender_id')
                ->distinct()
                ->pluck('sender_id');

            // Fusionner et obtenir les IDs uniques
            $contactIds = $sentMessages->merge($receivedMessages)->unique();

            foreach ($contactIds as $contactId) {
                // Récupérer le dernier message de la conversation
                $lastMessage = Message::where(function($query) use ($user, $contactId) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', $contactId);
                })->orWhere(function($query) use ($user, $contactId) {
                    $query->where('sender_id', $contactId)
                          ->where('receiver_id', $user->id);
                })->with(['item'])
                  ->orderBy('created_at', 'desc')
                  ->first();

                if (!$lastMessage) continue;

                // Compter les messages non lus
                $unreadCount = Message::where('sender_id', $contactId)
                                     ->where('receiver_id', $user->id)
                                     ->where('is_read', false)
                                     ->count();

                // Récupérer l'utilisateur contact
                $contact = User::find($contactId);
                if (!$contact) continue;

                $conversations->push([
                    'id' => $contactId,
                    'contact_id' => $contactId,
                    'contact' => [
                        'id' => $contact->id,
                        'name' => $contact->name,
                        'avatar' => $contact->avatar,
                        'avatar_url' => $contact->avatar ? asset('storage/' . $contact->avatar) : null,
                    ],
                    'last_message' => [
                        'id' => $lastMessage->id,
                        'content' => $lastMessage->content,
                        'created_at' => $lastMessage->created_at,
                        'is_mine' => $lastMessage->sender_id === $user->id,
                        'is_read' => $lastMessage->is_read,
                    ],
                    'item' => $lastMessage->item ? [
                        'id' => $lastMessage->item->id,
                        'name' => $lastMessage->item->name,
                        'image' => $lastMessage->item->first_image_url,
                    ] : null,
                    'unread_count' => $unreadCount,
                    'updated_at' => $lastMessage->created_at,
                ]);
            }

            // Trier par dernier message
            $sortedConversations = $conversations->sortByDesc('updated_at')->values();

            return response()->json([
                'success' => true,
                'message' => 'Conversations récupérées avec succès',
                'data' => $sortedConversations
            ], 200, [], JSON_UNESCAPED_UNICODE);

        } catch (\Exception $e) {
            Log::error('apiIndex messages error: ' . $e->getMessage());
            return $this->errorResponse('Erreur lors de la récupération des conversations: ' . $e->getMessage(), 500);
        }
    }

    /**
     * API: Messages d'une conversation
     */
    public function show(Request $request, $userId): JsonResponse
    {
        try {
            $currentUser = $request->user();

            // Vérifier que l'utilisateur cible existe
            $otherUser = User::find($userId);
            if (!$otherUser) {
                return $this->errorResponse('Utilisateur introuvable', 404);
            }

            $messages = Message::where(function($query) use ($currentUser, $userId) {
                $query->where('sender_id', $currentUser->id)
                      ->where('receiver_id', $userId);
            })->orWhere(function($query) use ($currentUser, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $currentUser->id);
            })->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();

            // Marquer les messages comme lus
            Message::where('sender_id', $userId)
                   ->where('receiver_id', $currentUser->id)
                   ->where('is_read', false)
                   ->update(['read_at' => now(), 'is_read' => true]);

            return $this->successResponse([
                'messages' => $messages,
                'other_user' => $otherUser->only(['id', 'name', 'avatar', 'avatar_url'])
            ], 'Messages récupérés avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des messages', 500);
        }
    }

    /**
     * API: Envoyer un message
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'recipient_id' => 'required|exists:users,id',
                'content' => 'nullable|string|max:5000',
                'item_id' => 'nullable|exists:items,id'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $user = $request->user();
            $recipientId = $request->recipient_id;

            $attachmentPath = null;
            $messageType = 'text';
            $duration = null;

            if ($request->hasFile('voice') && $request->file('voice')->isValid()) {
                $validator = Validator::make($request->all(), [
                    'voice' => 'file|mimes:webm,mp3,ogg,wav,mp4|max:5120',
                ]);
                if ($validator->fails()) {
                    return $this->errorResponse('Audio invalide', 422, $validator->errors());
                }
                $attachmentPath = $request->file('voice')->store('messages', 'public');
                StorageSyncService::syncFile($attachmentPath);
                $messageType = 'audio';
                $duration = (float) ($request->duration ?? 0);
            } elseif ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $validator = Validator::make($request->all(), [
                    'attachment' => 'file|max:10240',
                ]);
                if ($validator->fails()) {
                    return $this->errorResponse('Fichier invalide', 422, $validator->errors());
                }
                $attachmentPath = $request->file('attachment')->store('messages', 'public');
                StorageSyncService::syncFile($attachmentPath);
            }

            if (empty(trim($request->content ?? '')) && !$attachmentPath) {
                return $this->errorResponse('Message vide', 422);
            }

            $message = Message::create([
                'sender_id' => $user->id,
                'receiver_id' => $recipientId,
                'content' => trim($request->content ?? ''),
                'attachment' => $attachmentPath,
                'type' => $messageType,
                'duration' => $duration,
                'item_id' => $request->item_id
            ]);

            try {
                MessageSent::dispatch($message);
            } catch (\Exception $e) {
                \Log::error('Erreur broadcast message api: ' . $e->getMessage());
            }

            // Créer une notification pour le destinataire
            $this->notificationService->createMessageNotification(
                $user->id,
                $recipientId,
                $request->content ?? 'Fichier joint'
            );

            return $this->successResponse($message->load(['sender', 'receiver']), 'Message envoyé avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de l\'envoi du message', 500);
        }
    }

    /**
     * API: Marquer un message comme lu
     */
    public function markAsRead(Request $request, $messageId): JsonResponse
    {
        try {
            $message = Message::findOrFail($messageId);

            if ($message->receiver_id !== $request->user()->id) {
                return $this->errorResponse('Non autorisé', 403);
            }

            $message->update(['read_at' => now(), 'is_read' => true]);

            return $this->successResponse($message, 'Message marqué comme lu');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors du marquage du message', 500);
        }
    }

    /**
     * API: Nombre de messages non lus
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $count = Message::where('receiver_id', $request->user()->id)
                           ->where('is_read', false)
                           ->count();

            return $this->successResponse(['count' => $count], 'Nombre de messages non lus récupéré');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération du nombre de messages', 500);
        }
    }

    /**
     * API: Appliquer une réduction
     */
    public function applyDiscount(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'item_id' => 'required|exists:items,id',
                'buyer_id' => 'required|exists:users,id',
                'discount_percentage' => 'required|numeric|min:1|max:50',
                'expires_hours' => 'nullable|integer|min:1|max:168'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('Erreurs de validation', 422, $validator->errors());
            }

            $discount = $this->discountService->applyDiscount(
                (int) $request->item_id,
                (int) $request->buyer_id,
                (float) $request->discount_percentage,
                $request->user(),
                null,
                (int) ($request->expires_hours ?? 24)
            );

            return $this->successResponse($discount, 'Réduction appliquée avec succès');
        } catch (DomainException $e) {
            return $this->errorResponse($e->getMessage(), $this->discountService->errorStatusCode($e));
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de l\'application de la réduction', 500);
        }
    }

    /**
     * API: Réductions disponibles pour un item
     */
    public function getAvailableDiscounts(Request $request, $itemId): JsonResponse
    {
        try {
            $discounts = $this->discountService->getAvailableDiscounts((int) $itemId, (int) $request->user()->id);

            return $this->successResponse($discounts, 'Réductions récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des réductions', 500);
        }
    }
}
