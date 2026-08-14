<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Services\Concerns\SendsExpoPush;

class PushNotificationService
{
    use SendsExpoPush;

    private string $fcmUrl = 'https://fcm.googleapis.com/v1/projects/vintapp-e6fa7/messages:send';
    private string $serviceAccountPath;

    public function __construct()
    {
        $this->serviceAccountPath = storage_path('app/firebase-service-account.json');
    }

    /**
     * Envoyer une notification push à un utilisateur
     */
    public function sendToUser(User $user, array $notification, array $data = []): bool
    {
        if (!$user->fcm_token) {
            Log::info("User {$user->id} n'a pas de FCM token");
            return false;
        }

        return $this->sendToToken($user->fcm_token, $notification, $data);
    }

    /**
     * Envoyer une notification push à un token spécifique
     */
    public function sendToToken(string $token, array $notification, array $data = []): bool
    {
        $title = $notification['title'] ?? 'VintApp';
        $body = $notification['body'] ?? '';

        // Token Expo (expo-notifications) : à envoyer via l'API Expo Push,
        // pas via FCM.
        if ($this->isExpoToken($token)) {
            return $this->sendViaExpoPush($token, $title, $body, $data);
        }

        try {
            $accessToken = $this->getAccessToken();

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => $notification['image'] ?? null,
                    ],
                    'data' => array_merge([
                        'click_action' => $data['url'] ?? '/',
                    ], $data),
                    'webpush' => [
                        'notification' => [
                            'icon' => $notification['icon'] ?? '/images/icons/icon-192x192.png',
                            'badge' => $notification['badge'] ?? '/images/icons/icon-72x72.png',
                            'vibrate' => [200, 100, 200, 100, 200],
                            'requireInteraction' => $notification['requireInteraction'] ?? false,
                            'tag' => $notification['tag'] ?? 'vintapp-notification',
                            'actions' => [
                                [
                                    'action' => 'view',
                                    'title' => '👁️ Voir'
                                ],
                                [
                                    'action' => 'close',
                                    'title' => '❌ Fermer'
                                ]
                            ]
                        ],
                        'fcm_options' => [
                            'link' => $data['url'] ?? '/'
                        ]
                    ],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'color' => '#8B5CF6'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                Log::info('✅ Notification push envoyée', [
                    'token' => substr($token, 0, 20) . '...',
                    'title' => $title
                ]);
                return true;
            }

            Log::error('❌ Erreur envoi notification FCM', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Si le token est invalide, le supprimer
            if ($response->status() === 404 || str_contains($response->body(), 'not-registered')) {
                $this->removeInvalidToken($token);
            }

            return false;

        } catch (\Exception $e) {
            Log::error('❌ Exception envoi notification push', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Envoyer une notification à plusieurs tokens
     */
    public function sendToMultiple(array $tokens, array $notification, array $data = []): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $notification, $data)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Notification: Nouvelle commande
     */
    public function notifyNewOrder(User $user, $order): bool
    {
        return $this->sendToUser($user, [
            'title' => '🛍️ Nouvelle commande!',
            'body' => "Vous avez reçu une commande de {$order->buyer->name}",
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "order-{$order->id}",
            'requireInteraction' => true
        ], [
            'url' => "/orders/{$order->id}",
            'orderId' => (string) $order->id,
            'type' => 'order'
        ]);
    }

    /**
     * Notification: Commande confirmée
     */
    public function notifyOrderConfirmed(User $buyer, $order): bool
    {
        return $this->sendToUser($buyer, [
            'title' => '✅ Commande confirmée',
            'body' => "Votre commande #{$order->id} a été confirmée",
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "order-confirmed-{$order->id}"
        ], [
            'url' => "/orders/{$order->id}",
            'orderId' => (string) $order->id,
            'type' => 'order_confirmed'
        ]);
    }

    /**
     * Notification: Commande expédiée
     */
    public function notifyOrderShipped(User $buyer, $order): bool
    {
        return $this->sendToUser($buyer, [
            'title' => '📦 Commande expédiée',
            'body' => "Votre commande #{$order->id} est en route!",
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "order-shipped-{$order->id}"
        ], [
            'url' => "/orders/{$order->id}",
            'orderId' => (string) $order->id,
            'type' => 'order_shipped'
        ]);
    }

    /**
     * Notification: Nouveau message
     */
    public function notifyNewMessage(User $user, $message): bool
    {
        return $this->sendToUser($user, [
            'title' => '💬 Nouveau message',
            'body' => "{$message->sender->name}: " . substr($message->content, 0, 50) . '...',
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "message-{$message->id}",
            'requireInteraction' => true
        ], [
            'url' => "/messages/{$message->conversation_id}",
            'messageId' => (string) $message->id,
            'type' => 'message'
        ]);
    }

    /**
     * Notification: Article vendu
     */
    public function notifyItemSold(User $seller, $item): bool
    {
        return $this->sendToUser($seller, [
            'title' => '🎉 Article vendu!',
            'body' => "Votre article \"{$item->title}\" a été vendu",
            'icon' => '/images/icons/icon-192x192.png',
            'image' => $item->images[0]->url ?? null,
            'tag' => "item-sold-{$item->id}"
        ], [
            'url' => "/items/{$item->id}",
            'itemId' => (string) $item->id,
            'type' => 'item_sold'
        ]);
    }

    /**
     * Notification: Nouvel avis
     */
    public function notifyNewReview(User $user, $review): bool
    {
        $stars = str_repeat('⭐', $review->rating);
        
        return $this->sendToUser($user, [
            'title' => '⭐ Nouvel avis',
            'body' => "{$review->reviewer->name} vous a laissé un avis {$stars}",
            'icon' => '/images/icons/icon-192x192.png',
            'tag' => "review-{$review->id}"
        ], [
            'url' => "/profile/{$user->id}",
            'reviewId' => (string) $review->id,
            'type' => 'review'
        ]);
    }

    /**
     * Obtenir un access token Firebase via Service Account
     */
    private function getAccessToken(): string
    {
        if (!file_exists($this->serviceAccountPath)) {
            throw new \Exception('Service Account Firebase introuvable. Exécutez: php artisan firebase:setup');
        }

        $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);

        // Créer JWT pour OAuth 2.0
        $now = time();
        $jwt = [
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];

        $jwtHeader = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
        $jwtClaim = json_encode($jwt);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtHeader));
        $base64UrlClaim = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($jwtClaim));

        $signatureInput = $base64UrlHeader . '.' . $base64UrlClaim;
        
        openssl_sign($signatureInput, $signature, $serviceAccount['private_key'], OPENSSL_ALGO_SHA256);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwtToken = $signatureInput . '.' . $base64UrlSignature;

        // Échanger JWT contre access token
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwtToken
        ]);

        if (!$response->successful()) {
            throw new \Exception('Erreur obtention access token Firebase: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    /**
     * Supprimer un token invalide de la base de données
     */
    private function removeInvalidToken(string $token): void
    {
        User::where('fcm_token', $token)->update(['fcm_token' => null]);
        Log::info('🗑️ Token FCM invalide supprimé', ['token' => substr($token, 0, 20) . '...']);
    }
}
