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
        $request->validate([
            'idToken' => 'required|string'
        ]);

        try {
            // Vérifier le token Firebase
            $firebaseAuth = $this->firebaseService->auth();
            $verifiedIdToken = $firebaseAuth->verifyIdToken($request->idToken);
            
            // Récupérer les informations utilisateur depuis Firebase
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $firebaseAuth->getUser($firebaseUid);

            // Extraire les informations utilisateur
            $email = $firebaseUser->email ?? null;
            $name = $firebaseUser->displayName ?? 'Utilisateur';
            $avatar = $firebaseUser->photoUrl ?? null;
            $emailVerified = $firebaseUser->emailVerified ?? false;

            if (!$email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Adresse email requise pour la connexion'
                ], 400);
            }

            // Trouver ou créer l'utilisateur local
            $user = User::where('email', $email)
                       ->orWhere('firebase_uid', $firebaseUid)
                       ->first();

            if ($user) {
                // Mettre à jour les informations Firebase si nécessaire
                $user->update([
                    'firebase_uid' => $firebaseUid,
                    'name' => $name,
                    'avatar' => $avatar,
                    'email_verified_at' => $emailVerified ? now() : $user->email_verified_at,
                ]);
            } else {
                // Créer un nouvel utilisateur
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'firebase_uid' => $firebaseUid,
                    'avatar' => $avatar,
                    'email_verified_at' => $emailVerified ? now() : null,
                    'password' => Hash::make(Str::random(32)), // Mot de passe aléatoire
                ]);
            }

            // Enregistrer le token FCM si fourni
            if ($request->filled('fcmToken')) {
                $user->update(['fcm_token' => $request->fcmToken]);
            }

            // Connecter l'utilisateur
            Auth::login($user, true);

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

        } catch (\Kreait\Firebase\Exception\Auth\InvalidIdToken $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token d\'authentification invalide'
            ], 401);

        } catch (\Exception $e) {
            logger('Firebase auth error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur de connexion. Veuillez réessayer.'
            ], 500);
        }
    }

    /**
     * Inscription avec Firebase
     */
    public function registerWithFirebase(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'newsletter' => 'boolean',
            'referral_code' => 'nullable|string|exists:referral_codes,code',
        ]);

        try {
            // Vérifier le token Firebase
            $firebaseAuth = $this->firebaseService->auth();
            $verifiedIdToken = $firebaseAuth->verifyIdToken($request->idToken);
            
            // Récupérer les informations utilisateur depuis Firebase
            $firebaseUid = $verifiedIdToken->claims()->get('sub');
            $firebaseUser = $firebaseAuth->getUser($firebaseUid);

            // Extraire les informations utilisateur
            $email = $firebaseUser->email ?? null;
            $name = $request->name ?? $firebaseUser->displayName ?? 'Utilisateur';
            $avatar = $firebaseUser->photoUrl ?? null;
            $emailVerified = $firebaseUser->emailVerified ?? false;

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
                    $existingUser->update(['fcm_token' => $request->fcmToken]);
                }

                Auth::login($existingUser, true);

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
                $user->update(['fcm_token' => $request->fcmToken]);
            }

            // Connecter l'utilisateur
            Auth::login($user, true);

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
            logger('Firebase register error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription. Veuillez réessayer.'
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
                Auth::user()->update(['fcm_token' => null]);
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
