<?php

namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebasePushService;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FcmController extends Controller
{
    private function syncToken(User $user, ?string $token, ?string $deviceType): void
    {
        if ($token) {
            $user->fcm_token = $token;
            $user->device_type = $deviceType ?? 'unknown';
            $user->browser = request()->userAgent();
            $user->fcm_token_updated_at = now();
        } else {
            $user->fcm_token = null;
            $user->device_type = null;
            $user->browser = null;
            $user->fcm_token_updated_at = null;
        }
        $user->save();
    }

    private function fcmUser(Request $request): ?User
    {
        $user = Auth::user();

        if (!$user && session('2fa_user_id')) {
            $user = User::find(session('2fa_user_id'));
        }

        return $user;
    }

    public function registerToken(Request $request)
    {
        try {
            $validated = $request->validate([
                'token' => 'required|string',
                'device_type' => 'nullable|string|max:20'
            ]);

            $user = $this->fcmUser($request);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $this->syncToken($user, $validated['token'], $validated['device_type'] ?? null);

            Log::info('Token FCM enregistré', [
                'user_id' => $user->id,
                'device_type' => $user->device_type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token FCM enregistré avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur enregistrement token FCM', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du token'
            ], 500);
        }
    }

    public function testNotification(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user->fcm_token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun token FCM enregistré. Autorisez d\'abord les notifications.'
                ], 400);
            }

            $type = $request->input('type', 'approved');
            $fcmService = app(FirebasePushService::class);

            if ($type === 'approved') {
                $result = $fcmService->sendItemApprovedNotification($user->fcm_token, [
                    'item_id' => 999,
                    'item_name' => 'Article de Test',
                    'item_image' => 'items/test-image.jpg',
                    'verification_score' => 95
                ]);
            } else {
                $result = $fcmService->sendItemRejectedNotification($user->fcm_token, [
                    'item_id' => 999,
                    'item_name' => 'Article de Test',
                    'item_image' => 'items/test-image.jpg',
                    'reason' => 'Ceci est un test de notification de rejet'
                ]);
            }

            if ($result) {
                Log::info('Notification FCM test envoyée', [
                    'user_id' => $user->id,
                    'type' => $type
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Notification envoyée avec succès ! Vérifiez votre téléphone.'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Échec de l\'envoi de la notification. Vérifiez les logs.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Erreur test notification FCM', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminBroadcast(Request $request)
    {
        try {
            $user = Auth::user();

            $isAdmin = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->where('roles.slug', 'admin')
                ->exists();

            if (!$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé. Administrateur requis.'
                ], 403);
            }

            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string|max:500'
            ]);

            $usersWithTokens = User::whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->get();

            if ($usersWithTokens->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun appareil avec notifications activées trouvé.'
                ]);
            }

            $fcmService = app(FirebasePushService::class);
            $tokens = $usersWithTokens->pluck('fcm_token')->toArray();

            $result = $fcmService->sendMulticast(
                $tokens,
                $validated['title'],
                $validated['message'],
                [
                    'type' => 'admin_broadcast',
                    'timestamp' => now()->toIso8601String()
                ],
                null
            );

            Log::info('Broadcast FCM admin envoyé', [
                'admin_id' => $user->id,
                'total_devices' => count($tokens),
                'success' => $result['success'],
                'failure' => $result['failure']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Notification envoyée à {$result['success']} appareil(s) sur " . count($tokens),
                'stats' => [
                    'total' => count($tokens),
                    'success' => $result['success'],
                    'failure' => $result['failure'],
                    'failed_tokens' => $result['failed_tokens']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur broadcast FCM admin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    public function adminStats(Request $request)
    {
        try {
            $user = Auth::user();

            $isAdmin = DB::table('role_user')
                ->join('roles', 'role_user.role_id', '=', 'roles.id')
                ->where('role_user.user_id', $user->id)
                ->where('roles.slug', 'admin')
                ->exists();

            if (!$isAdmin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $totalUsers = User::count();
            $devicesWithFCM = User::whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_users' => $totalUsers,
                    'devices_with_fcm' => $devicesWithFCM
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
