<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Services\AuthService;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FirebaseAuthController extends ApiController
{
    protected FirebaseService $firebaseService;

    protected AuthService $authService;

    public function __construct(FirebaseService $firebaseService, AuthService $authService)
    {
        $this->firebaseService = $firebaseService;
        $this->authService = $authService;
    }

    /**
     * Connexion sociale via Firebase Authentication (Google, Apple, etc.).
     *
     * POST /api/auth/firebase/login
     *
     * @bodyParam idToken string required Token ID Firebase (JWT) émis par le SDK Firebase mobile.
     * @bodyParam fcmToken string Optionnel : token FCM de l'appareil pour les notifications push.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'idToken' => 'required|string',
            'fcmToken' => 'nullable|string',
            'deviceType' => 'nullable|string|in:mobile,tablet,desktop',
        ]);

        try {
            if (!$this->firebaseService->isConfigured()) {
                return $this->errorResponse('Service d\'authentification non disponible.', 503);
            }

            $verifiedIdToken = $this->firebaseService->auth()->verifyIdToken($request->idToken);

            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name') ?? 'Utilisateur';
            $avatar = $verifiedIdToken->claims()->get('picture');

            if (!$email) {
                return $this->errorResponse('Adresse email requise pour la connexion', 400);
            }

            $user = $this->authService->findOrCreateFirebaseUser($firebaseUid, $email, $name, $avatar);

            if ($request->filled('fcmToken')) {
                $this->authService->saveFcmToken($user, $request->fcmToken, $request->input('deviceType', 'desktop'));
            }

            // Si la 2FA est active, émettre un pending_token pour le challenge au lieu du token complet
            if ($user->google2fa_enabled) {
                $pendingToken = $user->createToken('2fa_pending', ['2fa:pending']);

                return response()->json([
                    'success' => true,
                    'message' => 'Code 2FA requis.',
                    'two_factor_required' => true,
                    'pending_token' => $pendingToken->plainTextToken,
                    'token_type' => 'Bearer',
                ], 200);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            Log::info('API Firebase login réussi', ['user_id' => $user->id, 'email' => $email]);

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'email_verified_at' => $user->email_verified_at,
                    'role' => $user->role ?? 'user',
                    'two_factor_enabled' => (bool) $user->google2fa_enabled,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données de connexion invalides',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::warning('API Firebase login échoué', [
                'error' => $e->getMessage(),
            ]);

            $message = 'Token d\'authentification invalide';
            $statusCode = 401;

            // Erreurs de configuration (project_id / credentials absents)
            if (strpos($e->getMessage(), 'project') !== false || strpos($e->getMessage(), 'credential') !== false) {
                $message = 'Service d\'authentification non disponible.';
                $statusCode = 503;
            }

            return $this->errorResponse($message, $statusCode);
        }
    }
}
