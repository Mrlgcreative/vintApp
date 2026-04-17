<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\FirebaseService;

class FirebaseAuthController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Afficher la page de connexion Firebase
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.firebase-login');
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
            $user = User::where('email', $email)
                       ->orWhere('firebase_uid', $firebaseUid)
                       ->first();

            if ($user) {
                // Mettre à jour les informations Firebase si nécessaire
                // IMPORTANT: Ne pas mettre à jour email_verified_at depuis Firebase
                // Laisser la vérification d'email à notre système de code
                $user->update([
                    'firebase_uid' => $firebaseUid,
                    'name' => $name,
                    'avatar' => $avatar,
                    // email_verified_at n'est pas modifié ici
                ]);
            } else {
                // Créer un nouvel utilisateur
                // IMPORTANT: email_verified_at reste NULL - l'utilisateur doit vérifier via notre code
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'firebase_uid' => $firebaseUid,
                    'avatar' => $avatar,
                    'email_verified_at' => null, // FORCER NULL - Vérification via code requis
                    'password' => Hash::make(Str::random(32)), // Mot de passe aléatoire
                ]);
            }

            // Enregistrer le token FCM si fourni
            if ($request->filled('fcmToken')) {
                $user->fcm_token = $request->fcmToken;
                $user->save();
            }

            // Connecter l'utilisateur
            Auth::login($user, true);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            // Vérifier si l'email doit être vérifié (TOUJOURS pour Firebase!)
            if (!$user->email_verified_at) {
                // Générer et envoyer un code de vérification
                $this->sendVerificationCode($user);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie ! Veuillez vérifier votre email.',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                    ],
                    'redirect' => route('verification.code')
                ]);
            }

            // Vérifier si 2FA est activé pour cet utilisateur
            if ($user->google2fa_enabled) {
                // Marquer que l'utilisateur doit passer par 2FA
                session(['2fa_required' => true, '2fa_user_id' => $user->id]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Veuillez entrer votre code d\'authentification à deux facteurs',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => $user->avatar,
                    ],
                    'redirect' => route('two-factor.challenge')
                ]);
            }

            // Marquer la session comme complètement authentifiée
            session(['2fa_verified' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ],
                'redirect' => route('home')
            ]);

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

            // Vérifier si l'utilisateur existe déjà
            $existingUser = User::where('email', $email)
                               ->orWhere('firebase_uid', $firebaseUid)
                               ->first();

            if ($existingUser) {
                // Utilisateur existant - connexion
                $existingUser->update([
                    'firebase_uid' => $firebaseUid,
                    'name' => $name,
                    'avatar' => $avatar,
                    'email_verified_at' => $emailVerified ? now() : $existingUser->email_verified_at,
                ]);

                // Enregistrer le token FCM si fourni
                if ($request->filled('fcmToken')) {
                    $existingUser->fcm_token = $request->fcmToken;
                    $existingUser->save();
                }

                Auth::login($existingUser, true);
                if ($request->hasSession()) {
                    $request->session()->regenerate();
                }

                // Vérifier si 2FA est activé
                if ($existingUser->google2fa_enabled) {
                    session(['2fa_required' => true, '2fa_user_id' => $existingUser->id]);
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Veuillez entrer votre code d\'authentification à deux facteurs',
                        'user' => [
                            'id' => $existingUser->id,
                            'name' => $existingUser->name,
                            'email' => $existingUser->email,
                            'avatar' => $existingUser->avatar,
                        ],
                        'redirect' => route('two-factor.challenge')
                    ]);
                }

                session(['2fa_verified' => true]);

                return response()->json([
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'user' => [
                        'id' => $existingUser->id,
                        'name' => $existingUser->name,
                        'email' => $existingUser->email,
                        'avatar' => $existingUser->avatar,
                    ],
                    'redirect' => route('home')
                ]);
            }

            // Nouveau utilisateur - inscription
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'phone' => $request->phone ?? '',
                'firebase_uid' => $firebaseUid,
                'avatar' => $avatar,
                'email_verified_at' => null, // Forcer verification par code pour tous
                'newsletter_subscribed' => $request->boolean('newsletter'),
                'password' => Hash::make(Str::random(32)), // Mot de passe aléatoire
            ]);

            // Générer et envoyer le code de vérification
            $this->sendVerificationCode($user);

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
                $user->fcm_token = $request->fcmToken;
                $user->save();
            }

            // Connecter l'utilisateur
            Auth::login($user, true);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! Vérifiez votre email pour un code de vérification.' . $referralMessage,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ],
                'redirect' => route('verification.code')
            ]);

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
            // Supprimer le token FCM
            if (Auth::check()) {
                $authUser = Auth::user();
                $authUser->fcm_token = null;
                $authUser->save();
            }

            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();

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
            Auth::user()->update([
                'fcm_token' => $request->fcm_token
            ]);

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
     * Obtenir les informations de configuration Firebase pour le frontend
     */
    public function getFirebaseConfig()
    {
        return response()->json([
            'config' => config('firebase.web_config')
        ]);
    }

    /**
     * Vérifier le statut d'authentification
     */
    public function checkAuthStatus()
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => Auth::user()->id,
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'avatar' => Auth::user()->avatar,
                ]
            ]);
        }

        return response()->json([
            'authenticated' => false
        ]);
    }

    /**
     * Envoyer le code de vérification par email
     */
    private function sendVerificationCode($user)
    {
        $code = $user->generateVerificationCode();
        
        try {
            Mail::to($user->email)->send(new \App\Mail\VerificationCodeMail($user, $code));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email verification: ' . $e->getMessage());
            // En cas d'erreur d'envoi, on peut quand même continuer
        }
    }
}
