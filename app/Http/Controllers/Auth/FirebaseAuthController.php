<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AuthService;
use App\Services\FirebaseService;

class FirebaseAuthController extends Controller
{
    protected $firebaseService;
    protected $authService;

    public function __construct(FirebaseService $firebaseService, AuthService $authService)
    {
        $this->firebaseService = $firebaseService;
        $this->authService = $authService;
    }

    /**
     * Authentification avec Firebase ID Token
     */
    public function loginWithFirebase(Request $request)
    {
        // Force JSON response
        $request->headers->set('Accept', 'application/json');
        
        try {
            Log::info('🔥 Firebase login attempt', [
                'has_token' => $request->has('idToken'),
                'ip' => $request->ip()
            ]);

            $request->validate([
                'idToken' => 'required|string'
            ]);

            // Vérifier que Firebase est configuré
            if (!$this->firebaseService->isConfigured()) {
                Log::error('Firebase non configuré lors de la tentative de connexion');
                return response()->json([
                    'success' => false,
                    'message' => 'Service d\'authentification non disponible. Contactez l\'administrateur.'
                ], 503);
            }

            Log::info('Tentative de connexion Firebase', ['idToken_length' => strlen($request->idToken)]);

            // Vérifier le token Firebase
            $firebaseAuth = $this->firebaseService->auth();
            $verifiedIdToken = $firebaseAuth->verifyIdToken($request->idToken);
            
            // Récupérer les informations utilisateur depuis le token (pas besoin d'appel API)
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $verifiedIdToken->claims()->get('name') ?? $request->input('name') ?? 'Utilisateur';
            $avatar = $verifiedIdToken->claims()->get('picture') ?? $request->input('photo_url');
            // IMPORTANT: Ne pas utiliser email_verified de Firebase, on force notre système de code
            // $emailVerified = $verifiedIdToken->claims()->get('email_verified', false);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adresse email requise pour la connexion'
                ], 400);
            }

            Log::info('🔥 Token Firebase vérifié', [
                'uid' => $firebaseUid,
                'email' => $email,
                'name' => $name
            ]);

            // Trouver ou créer l'utilisateur local
            // IMPORTANT: email_verified_at n'est jamais mis à jour depuis Firebase ici
            // Laisser la vérification d'email à notre système de code
            $user = $this->authService->findOrCreateFirebaseUser($firebaseUid, $email, $name, $avatar);

            // Enregistrer le token FCM si fourni
            if ($request->filled('fcmToken')) {
                $this->authService->saveFcmToken($user, $request->fcmToken);
            }

            // Connecter l'utilisateur (remember=on comme avant : sessions persistantes Firebase)
            $this->authService->loginUser($user, $request, true);

            // Vérifier si l'email doit être vérifié (TOUJOURS pour Firebase!)
            if (!$user->email_verified_at) {
                // Générer et envoyer un code de vérification
                $this->authService->sendVerificationCode($user);

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie ! Veuillez vérifier votre email.',
                    'user' => $this->authService->userPayload($user),
                    'redirect' => route('verification.code')
                ]);
            }

            return response()->json($this->authService->completeFirebaseLogin($user));

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('🔥 Firebase validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Données de connexion invalides',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Log détaillé de l'erreur
            Log::error('🔥 Firebase auth error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Déterminer le message d'erreur approprié
            $message = 'Erreur de connexion. Veuillez réessayer.';
            $statusCode = 500;
            
            if (strpos($e->getMessage(), 'Invalid') !== false || strpos($e->getMessage(), 'token') !== false) {
                $message = 'Token d\'authentification invalide';
                $statusCode = 401;
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : null,
                'exception' => config('app.debug') ? get_class($e) : null
            ], $statusCode);
        }
    }

    /**
     * Inscription avec Firebase
     */
    public function registerWithFirebase(Request $request)
    {
        // Force JSON response
        $request->headers->set('Accept', 'application/json');
        
        try {
            $request->validate([
                'idToken' => 'required|string',
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'newsletter' => 'boolean',
                'referral_code' => 'nullable|string|exists:referral_codes,code',
            ]);

            Log::info('🔥 Firebase register attempt', [
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip()
            ]);
            // Vérifier le token Firebase
            $firebaseAuth = $this->firebaseService->auth();
            $verifiedIdToken = $firebaseAuth->verifyIdToken($request->idToken);
            
            // Récupérer les informations utilisateur depuis le token (pas besoin d'appel API)
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email');
            $name = $request->name ?? $verifiedIdToken->claims()->get('name') ?? 'Utilisateur';
            $avatar = $verifiedIdToken->claims()->get('picture') ?? $request->input('photo_url');
            $emailVerified = $verifiedIdToken->claims()->get('email_verified', false);

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adresse email requise pour l\'inscription'
                ], 400);
            }

            // Trouver ou créer l'utilisateur local
            $user = $this->authService->findOrCreateFirebaseUser($firebaseUid, $email, $name, $avatar, [
                'phone' => $request->phone ?? '',
                'newsletter_subscribed' => $request->boolean('newsletter'),
            ]);

            $isNewUser = $user->wasRecentlyCreated;

            // Divergence historique : le register peut marquer l'email vérifié depuis Firebase,
            // contrairement au login (vérification par code uniquement)
            if (!$isNewUser && $emailVerified) {
                $user->update(['email_verified_at' => now()]);
            }

            // Générer et envoyer le code de vérification pour un nouveau compte
            if ($isNewUser) {
                $this->authService->sendVerificationCode($user);
            }

            // Appliquer le code de parrainage s'il est fourni
            $referralMessage = '';
            if ($request->filled('referral_code')) {
                $referral = $user->applyReferralCode($request->referral_code);
                if ($referral) {
                    $referralMessage = ' Code de parrainage appliqué avec succès !';
                }
            }

            // Enregistrer le token FCM si fourni
            if ($request->filled('fcmToken')) {
                $this->authService->saveFcmToken($user, $request->fcmToken);
            }

            // Connecter l'utilisateur (remember=on comme avant : sessions persistantes Firebase)
            $this->authService->loginUser($user, $request, true);

            if ($isNewUser) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inscription réussie ! Vérifiez votre email pour un code de vérification.' . $referralMessage,
                    'user' => $this->authService->userPayload($user),
                    'redirect' => route('verification.code')
                ]);
            }

            return response()->json($this->authService->completeFirebaseLogin($user));

        } catch (\Exception $e) {
            Log::error('🔥 Firebase register error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Déconnexion Firebase
     */
    public function logout(Request $request)
    {
        try {
            // Supprimer le token FCM puis déconnecter
            $this->authService->logout($request, true);

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie',
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            logger('Firebase logout error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion'
            ], 500);
        }
    }

    /**
     * Enregistrer le token FCM pour les notifications
     */
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string'
        ]);

        if (Auth::check()) {
            $this->authService->saveFcmToken(Auth::user(), $request->fcm_token);

            return response()->json([
                'success' => true,
                'message' => 'Token FCM enregistré'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Utilisateur non authentifié'
        ], 401);
    }

    /**
     * Vérifier le statut d'authentification
     */
    public function checkAuthStatus()
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => $this->authService->userPayload(Auth::user())
            ]);
        }

        return response()->json([
            'authenticated' => false
        ]);
    }
}
