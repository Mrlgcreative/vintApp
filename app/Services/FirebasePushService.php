<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Support\Facades\Log;

class FirebasePushService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $factory = (new Factory)
                ->withServiceAccount(storage_path('firebase/serviceAccountKey.json'));
            
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Erreur initialisation Firebase Messaging', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Envoyer une notification push à un utilisateur
     *
     * @param string $fcmToken Token FCM de l'utilisateur
     * @param string $title Titre de la notification
     * @param string $body Corps de la notification
     * @param array $data Données additionnelles
     * @param string|null $imageUrl URL de l'image
     * @return bool
     */
    public function sendNotification(string $fcmToken, string $title, string $body, array $data = [], ?string $imageUrl = null): bool
    {
        if (!$this->messaging) {
            Log::error('Firebase Messaging non initialisé');
            return false;
        }

        try {
            // Créer la notification de base
            $notification = FirebaseNotification::create($title, $body);
            
            if ($imageUrl) {
                $notification = $notification->withImageUrl($imageUrl);
            }

            // Configuration Android
            $androidConfig = AndroidConfig::fromArray([
                'ttl' => '3600s',
                'priority' => 'high',
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/images/icons/icon-192x192.png',
                    'color' => '#7c3aed', // Couleur Vinted Violet
                    'sound' => 'default',
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'channel_id' => 'vintapp_notifications',
                ]
            ]);

            // Configuration Web Push
            $webPushConfig = WebPushConfig::fromArray([
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/images/icons/icon-192x192.png',
                    'badge' => '/images/icons/icon-96x96.png',
                    'vibrate' => [200, 100, 200],
                    'requireInteraction' => false,
                ],
                'fcm_options' => [
                    'link' => $data['url'] ?? '/'
                ]
            ]);

            // Créer le message
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData($data)
                ->withAndroidConfig($androidConfig)
                ->withWebPushConfig($webPushConfig);

            // Envoyer le message
            $this->messaging->send($message);

            Log::info('Notification FCM envoyée avec succès', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'title' => $title
            ]);

            return true;

        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            Log::warning('Token FCM invalide ou expiré', [
                'token' => substr($fcmToken, 0, 20) . '...',
                'error' => $e->getMessage()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification FCM', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Envoyer des notifications à plusieurs utilisateurs
     *
     * @param array $fcmTokens Tableau des tokens FCM
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @return array Résultat avec succès et échecs
     */
    public function sendMulticast(array $fcmTokens, string $title, string $body, array $data = [], ?string $imageUrl = null): array
    {
        if (!$this->messaging || empty($fcmTokens)) {
            return ['success' => 0, 'failure' => count($fcmTokens)];
        }

        try {
            $notification = FirebaseNotification::create($title, $body);
            
            if ($imageUrl) {
                $notification = $notification->withImageUrl($imageUrl);
            }

            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $fcmTokens);

            Log::info('Notification FCM multicast envoyée', [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count()
            ]);

            return [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count(),
                'failed_tokens' => $report->invalidTokens()
            ];

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification multicast FCM', [
                'error' => $e->getMessage()
            ]);

            return ['success' => 0, 'failure' => count($fcmTokens)];
        }
    }

    /**
     * Envoyer notification d'approbation d'article
     */
    public function sendItemApprovedNotification(string $fcmToken, array $itemData): bool
    {
        return $this->sendNotification(
            $fcmToken,
            '✅ Article Approuvé !',
            "Votre article \"{$itemData['item_name']}\" a été approuvé et est maintenant en ligne !",
            [
                'type' => 'item_approved',
                'item_id' => (string) $itemData['item_id'],
                'item_name' => $itemData['item_name'],
                'verification_score' => (string) ($itemData['verification_score'] ?? 0),
                'url' => url("/items/{$itemData['item_id']}")
            ],
            $itemData['item_image'] ? asset("storage/{$itemData['item_image']}") : null
        );
    }

    /**
     * Envoyer notification de rejet d'article
     */
    public function sendItemRejectedNotification(string $fcmToken, array $itemData): bool
    {
        return $this->sendNotification(
            $fcmToken,
            '❌ Article Rejeté',
            "Votre article \"{$itemData['item_name']}\" a été rejeté. Raison : {$itemData['reason']}",
            [
                'type' => 'item_rejected',
                'item_id' => (string) $itemData['item_id'],
                'item_name' => $itemData['item_name'],
                'reason' => $itemData['reason'],
                'url' => url("/items/{$itemData['item_id']}/edit")
            ],
            $itemData['item_image'] ? asset("storage/{$itemData['item_image']}") : null
        );
    }
}
