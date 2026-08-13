<?php

namespace App\Services;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Connecter un utilisateur et régénérer la session.
     */
    public function loginUser(User $user, Request $request, bool $remember = false): void
    {
        Auth::login($user, $remember);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
    }

    /**
     * Déconnecter l'utilisateur et invalider la session.
     */
    public function logout(Request $request, bool $clearFcm = false): void
    {
        $sessionId = $request->hasSession() ? $request->session()->getId() : null;

        if ($clearFcm && Auth::check()) {
            // fcm_token n'est pas fillable : assignation directe requise
            Auth::user()->fcm_token = null;
            Auth::user()->save();
        }

        // Marquer la session utilisateur comme inactive
        if ($sessionId) {
            \App\Models\UserSession::where('session_id', $sessionId)
                ->where('is_active', true)
                ->update(['is_active' => false, 'logout_at' => now()]);
        }

        Auth::logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }

    /**
     * Trouver ou créer un utilisateur à partir d'un token Firebase,
     * en mettant à jour les informations partagées (firebase_uid, name, avatar).
     *
     * email_verified_at reste null à la création (vérification par code).
     */
    public function findOrCreateFirebaseUser(
        string $firebaseUid,
        string $email,
        string $name,
        ?string $avatar,
        array $createExtra = []
    ): User {
        $user = User::where('email', $email)
            ->orWhere('firebase_uid', $firebaseUid)
            ->first();

        if ($user) {
            $user->update([
                'firebase_uid' => $firebaseUid,
                'name' => $name,
                'avatar' => $avatar,
            ]);

            return $user;
        }

        return User::create(array_merge([
            'name' => $name,
            'email' => $email,
            'firebase_uid' => $firebaseUid,
            'avatar' => $avatar,
            'email_verified_at' => null,
            'password' => Hash::make(Str::random(32)),
        ], $createExtra));
    }

    /**
     * Finaliser une session Firebase (l'utilisateur doit déjà être connecté) :
     * bascule 2FA ou session complète + réponse de redirection.
     */
    public function completeFirebaseLogin(User $user): array
    {
        if ($user->google2fa_enabled) {
            session(['2fa_required' => true, '2fa_user_id' => $user->id]);

            return [
                'success' => true,
                'message' => 'Veuillez entrer votre code d\'authentification à deux facteurs',
                'user' => $this->userPayload($user),
                'redirect' => route('two-factor.challenge'),
            ];
        }

        session(['2fa_verified' => true]);

        return [
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => $this->userPayload($user),
            'redirect' => route('home'),
        ];
    }

    /**
     * Payload utilisateur des réponses JSON d'authentification.
     */
    public function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ];
    }

    /**
     * Enregistrer le token FCM d'un utilisateur.
     */
    public function saveFcmToken(User $user, string $token): void
    {
        // fcm_token n'est pas fillable : assignation directe requise
        $user->fcm_token = $token;
        $user->save();
    }

    /**
     * Générer et envoyer le code de vérification d'email.
     */
    public function sendVerificationCode(User $user): void
    {
        $code = $user->generateVerificationCode();

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email verification: ' . $e->getMessage());
        }
    }
}
