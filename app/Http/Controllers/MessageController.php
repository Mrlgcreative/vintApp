<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Discount;
use App\Services\NotificationService;
use App\Services\DiscountService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\StorageSyncService;
use App\Traits\ApiResponses;
use DomainException;

class MessageController extends Controller
{
    use ApiResponses;
    
    private $notificationService;
    private $discountService;

    public function __construct(NotificationService $notificationService, DiscountService $discountService)
    {
        $this->notificationService = $notificationService;
        $this->discountService = $discountService;
    }

    /**
     * Afficher la page principale de messagerie avec les vendeurs contactés
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les vendeurs contactés et les conversations reçues
        $vendorContacts = $this->getVendorContacts($user);
        $receivedConversations = $this->getReceivedConversations($user);
        
        // Fusionner les deux collections en évitant les doublons
        $allContacts = $vendorContacts->merge($receivedConversations)->unique(function($contact) {
            return $contact->vendor_id . '-' . ($contact->item_id ?? 'general');
        })->sortByDesc('last_message.created_at');
        
        return view('messages.index', [
            'vendorContacts' => $allContacts
        ]);
    }

    /**
     * Afficher une conversation spécifique
     */
    public function show(Request $request, $user)
    {
        $currentUser = Auth::user();
        
        // Le paramètre peut être soit l'ID d'un utilisateur, soit directement l'instance User
        $conversationId = is_object($user) ? $user->id : $user;
        
        // Vérifier que l'utilisateur fait partie de la conversation
        $messages = Message::where(function($query) use ($currentUser, $conversationId) {
            $query->where('sender_id', $currentUser->id)
                  ->where('receiver_id', $conversationId);
        })->orWhere(function($query) use ($currentUser, $conversationId) {
            $query->where('sender_id', $conversationId)
                  ->where('receiver_id', $currentUser->id);
        })->orderBy('created_at', 'asc')->get();
        
        // Marquer les messages comme lus
        $messages->where('receiver_id', $currentUser->id)->each(function($message) {
            $message->update(['read_at' => now(), 'is_read' => true]);
        });
        
        // Récupérer les informations de l'autre utilisateur
        $otherUser = is_object($user) ? $user : User::find($conversationId);
        
        // Si c'est une requête AJAX/JSON, retourner JSON
        if ($request->expectsJson()) {
            return response()->json([
                'messages' => $messages,
                'user' => $otherUser
            ]);
        }
        
        // Sinon, retourner la vue de conversation
        $itemId = $request->get('item_id'); // Pour récupérer l'ID du produit si fourni
        $item = null;
        if ($itemId) {
            $item = \App\Models\Item::find($itemId);
        }
        
        return view('messages.show', compact('messages', 'otherUser', 'item'));
    }

    /**
     * Créer un nouveau message
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();
        $recipientId = $request->recipient_id;

        // Vérifier si c'est une conversation existante ou concernant un produit
        $hasExistingConversation = Message::where(function($query) use ($user, $recipientId) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $recipientId);
        })->orWhere(function($query) use ($user, $recipientId) {
            $query->where('sender_id', $recipientId)
                  ->where('receiver_id', $user->id);
        })->exists();

        // Si une conversation existe déjà ou si le destinataire a déjà acheté, autoriser le message
        if (!$hasExistingConversation) {
            $hasOrder = \App\Models\Order::where(function($query) use ($user, $recipientId) {
                $query->where('seller_id', $user->id)
                      ->where('buyer_id', $recipientId);
            })->orWhere(function($query) use ($user, $recipientId) {
                $query->where('seller_id', $recipientId)
                      ->where('buyer_id', $user->id);
            })->exists();
            
            if (!$hasOrder && !$request->has('item_id')) {
                return response()->json(['success' => false, 'error' => 'Vous devez d\'abord initier une conversation à propos d\'un produit.'], 403);
            }
        }

        $attachmentPath = null;
        $messageType = 'text';
        $duration = null;

        if ($request->hasFile('voice') && $request->file('voice')->isValid()) {
            $request->validate(['voice' => 'file|mimes:webm,mp3,ogg,wav,mp4|max:5120']);
            $attachmentPath = $request->file('voice')->store('messages', 'public');
            StorageSyncService::syncFile($attachmentPath);
            $messageType = 'audio';
            $duration = (float) ($request->duration ?? 0);
        } elseif ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $request->validate(['attachment' => 'file|max:10240']);
            $attachmentPath = $request->file('attachment')->store('messages', 'public');
            StorageSyncService::syncFile($attachmentPath);
        }

        // On autorise l'envoi d'un message vide si un fichier est joint
        if (empty(trim($request->content ?? '')) && !$attachmentPath) {
            return response()->json(['success' => false, 'error' => 'Message vide.'], 422);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $recipientId,
            'content' => trim($request->content ?? ''),
            'attachment' => $attachmentPath,
            'type' => $messageType,
            'duration' => $duration,
        ]);

        // Diffuser le message en temps reel
        try {
            \App\Events\MessageSent::dispatch($message);
        } catch (\Exception $e) {
            \Log::error('Erreur broadcast message: ' . $e->getMessage());
        }

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
     * Appliquer une réduction prédéfinie sur un produit
     */
    public function applyDiscount(Request $request)
    {
        try {
            Log::info('applyDiscount called', $request->all());
            
            $request->validate([
                'item_id' => 'required|exists:items,id',
                'buyer_id' => 'required|exists:users,id',
                'discount_percentage' => 'required|numeric|min:1|max:50',
                'message_id' => 'nullable|exists:messages,id',
                'expires_hours' => 'nullable|integer|min:1|max:168' // Max 7 jours
            ]);

            $seller = Auth::user();

            $discount = $this->discountService->applyDiscount(
                (int) $request->item_id,
                (int) $request->buyer_id,
                (float) $request->discount_percentage,
                $seller,
                $request->filled('message_id') ? (int) $request->message_id : null,
                (int) ($request->expires_hours ?? 24)
            );

            // Message automatique au client (spécifique au canal web)
            Message::create([
                'sender_id' => $seller->id,
                'receiver_id' => $request->buyer_id,
                'content' => $this->discountService->buildDiscountMessage($discount),
                'subject' => 'Réduction appliquée',
                'item_id' => $request->item_id
            ]);

            return response()->json([
                'success' => true,
                'discount' => $discount,
                'message' => 'Réduction appliquée avec succès !'
            ]);
        } catch (DomainException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], $this->discountService->errorStatusCode($e));
        } catch (\Exception $e) {
            Log::error('Erreur dans applyDiscount: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les taux de réduction prédéfinis
     */
    public function getPredefinedDiscountRates(): JsonResponse
    {
        $rates = [
            ['value' => 5, 'label' => '5%', 'description' => 'Petite réduction'],
            ['value' => 10, 'label' => '10%', 'description' => 'Réduction standard'],
            ['value' => 15, 'label' => '15%', 'description' => 'Bonne réduction'],
            ['value' => 20, 'label' => '20%', 'description' => 'Réduction attractive'],
            ['value' => 25, 'label' => '25%', 'description' => 'Réduction importante'],
            ['value' => 30, 'label' => '30%', 'description' => 'Grande réduction'],
        ];

        return response()->json(['rates' => $rates]);
    }

    /**
     * Récupérer les réductions disponibles pour un article et l'utilisateur connecté
     */
    public function getAvailableDiscounts($itemId): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([]);
        }

        $discounts = $this->discountService->getAvailableDiscounts((int) $itemId, (int) Auth::id());

        return response()->json($discounts);
    }

    /**
     * Récupérer les demandes de réduction pour un vendeur
     */
    public function getDiscountRequests(): JsonResponse
    {
        $seller = Auth::user();
        
        // Récupérer les messages avec subject "Demande de réduction" reçus par le vendeur
        $discountRequests = Message::where('receiver_id', $seller->id)
                                  ->where('subject', 'like', '%réduction%')
                                  ->whereNotNull('item_id')
                                  ->with(['sender', 'item'])
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        // Ajouter les informations de réduction existante si applicable
        foreach ($discountRequests as $request) {
            $existingDiscount = \App\Models\Discount::where('item_id', $request->item_id)
                                                   ->where('user_id', $request->sender_id)
                                                   ->where('seller_id', $seller->id)
                                                   ->latest()
                                                   ->first();
            
            $request->existing_discount = $existingDiscount;
            $request->has_active_discount = $existingDiscount && $existingDiscount->isValid();
        }

        return response()->json([
            'requests' => $discountRequests,
            'predefined_rates' => $this->getPredefinedDiscountRates()->getData()->rates
        ]);
    }

    /**
     * Récupérer les vendeurs contactés par l'utilisateur avec contexte produit
     */
    private function getVendorContacts($user)
    {
        // Récupérer les messages envoyés par l'utilisateur avec un subject (demandes de réduction)
        $contactMessages = Message::where('sender_id', $user->id)
            ->whereNotNull('subject')
            ->whereNotNull('item_id')
            ->with(['receiver', 'item'])
            ->orderBy('created_at', 'desc')
            ->get();

        $vendorContacts = collect();

        foreach ($contactMessages as $message) {
            // Éviter les doublons de vendeur-produit
            $existingContact = $vendorContacts->first(function($contact) use ($message) {
                return $contact->vendor_id === $message->receiver_id && 
                       $contact->item_id === $message->item_id;
            });

            if (!$existingContact) {
                // Récupérer le dernier message de la conversation avec ce vendeur
                $lastMessage = Message::where(function($query) use ($user, $message) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', $message->receiver_id);
                })->orWhere(function($query) use ($user, $message) {
                    $query->where('sender_id', $message->receiver_id)
                          ->where('receiver_id', $user->id);
                })->orderBy('created_at', 'desc')->first();

                // Compter les messages non lus de ce vendeur
                $unreadCount = Message::where('sender_id', $message->receiver_id)
                                     ->where('receiver_id', $user->id)
                                     ->where('is_read', false)
                                     ->count();

                // Vérifier s'il y a une réduction active pour ce produit
                $hasDiscount = \App\Models\Discount::forUser($user->id)
                                                  ->where('item_id', $message->item_id)
                                                  ->valid()
                                                  ->exists();

                $vendorContacts->push((object) [
                    'vendor_id' => $message->receiver_id,
                    'vendor' => $message->receiver,
                    'item_id' => $message->item_id,
                    'item' => $message->item,
                    'initial_message' => $message,
                    'last_message' => $lastMessage,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $unreadCount,
                    'has_discount' => $hasDiscount,
                    'contact_date' => $message->created_at
                ]);
            }
        }

        return $vendorContacts->sortByDesc('last_message.created_at');
    }

    /**
     * Récupérer les conversations reçues par l'utilisateur (quand il est vendeur)
     */
    private function getReceivedConversations($user)
    {
        // Récupérer les messages reçus par l'utilisateur avec un subject et item_id
        $receivedMessages = Message::where('receiver_id', $user->id)
            ->whereNotNull('subject')
            ->whereNotNull('item_id')
            ->with(['sender', 'item'])
            ->orderBy('created_at', 'desc')
            ->get();

        $receivedContacts = collect();

        foreach ($receivedMessages as $message) {
            // Éviter les doublons de client-produit
            $existingContact = $receivedContacts->first(function($contact) use ($message) {
                return $contact->vendor_id === $message->sender_id && 
                       $contact->item_id === $message->item_id;
            });

            if (!$existingContact) {
                // Récupérer le dernier message de la conversation avec ce client
                $lastMessage = Message::where(function($query) use ($user, $message) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', $message->sender_id);
                })->orWhere(function($query) use ($user, $message) {
                    $query->where('sender_id', $message->sender_id)
                          ->where('receiver_id', $user->id);
                })->orderBy('created_at', 'desc')->first();

                // Compter les messages non lus de ce client
                $unreadCount = Message::where('sender_id', $message->sender_id)
                                     ->where('receiver_id', $user->id)
                                     ->where('is_read', false)
                                     ->count();

                // Vérifier s'il y a une réduction active pour ce produit
                $hasDiscount = \App\Models\Discount::forUser($message->sender_id)
                                                  ->where('item_id', $message->item_id)
                                                  ->valid()
                                                  ->exists();

                $receivedContacts->push((object) [
                    'vendor_id' => $message->sender_id, // Dans ce cas, c'est le client qui nous a contacté
                    'vendor' => $message->sender, // Le client qui nous a contacté
                    'item_id' => $message->item_id,
                    'item' => $message->item,
                    'initial_message' => $message,
                    'last_message' => $lastMessage,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : null,
                    'unread_count' => $unreadCount,
                    'has_discount' => $hasDiscount,
                    'contact_date' => $message->created_at
                ]);
            }
        }

        return $receivedContacts->sortByDesc('last_message.created_at');
    }

    // ==================== API Methods ====================

    /**
     * Get all conversations for API
     */
    public function apiIndex(Request $request)
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
     * Get conversation messages with a specific user for API
     */
    public function apiShow(Request $request, $userId)
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
     * Send a message via API
     */
    public function apiStore(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
                $validator = \Validator::make($request->all(), [
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
                $validator = \Validator::make($request->all(), [
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
                \App\Events\MessageSent::dispatch($message);
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
     * Mark message as read via API
     */
    public function apiMarkAsRead(Request $request, $messageId)
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
     * Get unread messages count via API
     */
    public function apiUnreadCount(Request $request)
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
     * Apply discount via API
     */
    public function apiApplyDiscount(Request $request)
    {
        try {
            $validator = \Validator::make($request->all(), [
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
     * Get available discounts for an item via API
     */
    public function apiGetAvailableDiscounts(Request $request, $itemId)
    {
        try {
            $discounts = $this->discountService->getAvailableDiscounts((int) $itemId, (int) $request->user()->id);

            return $this->successResponse($discounts, 'Réductions récupérées avec succès');
        } catch (\Exception $e) {
            return $this->errorResponse('Erreur lors de la récupération des réductions', 500);
        }
    }
}
