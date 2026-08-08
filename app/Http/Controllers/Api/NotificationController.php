<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Sauvegarder le token FCM d'un utilisateur
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string|in:mobile,tablet,desktop',
            'browser' => 'nullable|string|max:50'
        ]);

        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être connecté pour vous abonner aux notifications'
                ], 401);
            }
            
            $user->fcm_token = $validated['token'];
            $user->device_type = $validated['device_type'] ?? 'desktop';
            $user->browser = $validated['browser'] ?? 'chrome';
            $user->fcm_token_updated_at = now();
            $user->save();

            Log::info('✅ Token FCM sauvegardé', [
                'user_id' => $user->id,
                'device_type' => $validated['device_type'] ?? 'unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notifications activées avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur sauvegarde token FCM', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'activation des notifications'
            ], 500);
        }
    }

    /**
     * Supprimer le token FCM d'un utilisateur
     */
    public function unsubscribe(Request $request)
    {
        try {
            $user = $request->user();
            
            $user->fcm_token = null;
            $user->device_type = null;
            $user->browser = null;
            $user->fcm_token_updated_at = null;
            $user->save();

            Log::info('🔕 Désabonnement notifications', [
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notifications désactivées'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur désabonnement notifications', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la désactivation'
            ], 500);
        }
    }

    /**
     * Tracker les notifications fermées (analytics)
     */
    public function closed(Request $request)
    {
        $validated = $request->validate([
            'tag' => 'required|string',
            'timestamp' => 'required|integer'
        ]);

        Log::info('📊 Notification fermée', [
            'tag' => $validated['tag'],
            'timestamp' => $validated['timestamp']
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Tester l'envoi d'une notification
     */
    public function test(Request $request)
    {
        // Si GET, retourner les infos
        if ($request->isMethod('get')) {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'user_id' => $user?->id,
                'fcm_token' => $user?->fcm_token ? 'Enregistré (' . substr($user->fcm_token, 0, 20) . '...)' : 'Non enregistré',
                'device_type' => $user?->device_type,
                'browser' => $user?->browser,
                'message' => $user?->fcm_token 
                    ? 'Utilisez POST pour envoyer une notification test' 
                    : 'Aucun token FCM enregistré. Activez les notifications d\'abord.',
                'endpoint' => '/api/notifications/test',
                'method' => 'POST',
                'auth_required' => true
            ]);
        }

        // Si POST, envoyer la notification
        try {
            $user = $request->user();

            if (!$user->fcm_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun token FCM enregistré. Activez les notifications d\'abord.'
                ], 400);
            }

            // Vérifier si le Service Account existe
            $serviceAccountPath = storage_path('app/firebase-service-account.json');
            
            if (!file_exists($serviceAccountPath)) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Service Account Firebase non configuré. L\'abonnement fonctionne mais l\'envoi backend nécessite la configuration du Service Account.',
                    'instructions' => [
                        '1. Téléchargez le Service Account JSON depuis Firebase Console',
                        '2. Placez-le dans storage/app/firebase-service-account.json',
                        '3. Consultez FIREBASE_SERVICE_ACCOUNT_SETUP.md pour les détails'
                    ],
                    'token_saved' => true,
                    'next_step' => 'Configuration du Service Account pour envoi backend'
                ]);
            }

            $notificationService = app(\App\Services\PushNotificationService::class);

            $success = $notificationService->sendToUser($user, [
                'title' => '🧪 Notification test',
                'body' => 'Vos notifications fonctionnent parfaitement!',
                'icon' => '/images/icons/icon-192x192.png',
                'tag' => 'test-notification'
            ], [
                'url' => '/',
                'type' => 'test'
            ]);

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification test envoyée avec succès!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de la notification test'
            ], 500);

        } catch (\Exception $e) {
            Log::error('❌ Erreur test notification', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoyer une notification test à tous les utilisateurs avec token FCM
     */
    public function broadcastTest(Request $request)
    {
        try {
            $users = \App\Models\User::whereNotNull('fcm_token')->get();
            
            if ($users->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun utilisateur avec token FCM trouvé'
                ], 404);
            }

            $notificationService = app(\App\Services\PushNotificationService::class);
            $results = [
                'total' => $users->count(),
                'success' => 0,
                'failed' => 0,
                'users_notified' => []
            ];

            foreach ($users as $user) {
                $success = $notificationService->sendToUser($user, [
                    'title' => '📢 Notification de test',
                    'body' => "Bonjour {$user->name}! Ceci est un test de notification VintApp.",
                    'icon' => '/images/icons/icon-192x192.png',
                    'tag' => 'broadcast-test-' . time(),
                    'requireInteraction' => true
                ], [
                    'url' => '/',
                    'type' => 'broadcast_test'
                ]);

                if ($success) {
                    $results['success']++;
                    $results['users_notified'][] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'device' => $user->device_type,
                        'browser' => $user->browser
                    ];
                } else {
                    $results['failed']++;
                }
            }

            Log::info('📢 Broadcast test notification', $results);

            return response()->json([
                'success' => true,
                'message' => "Notifications envoyées à {$results['success']}/{$results['total']} utilisateurs",
                'details' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur broadcast test notification', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
