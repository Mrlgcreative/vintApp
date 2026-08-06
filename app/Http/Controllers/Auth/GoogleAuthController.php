<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AdminRedirection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    use AdminRedirection;
    /**
     * Rediriger vers Google pour l'authentification
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Gérer le callback de Google
     */
    public function handleGoogleCallback()
    {
        try {
            // Récupérer les informations de l'utilisateur depuis Google
            $googleUser = Socialite::driver('google')->user();

            // Vérifier si l'utilisateur existe déjà avec cet email
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Mettre à jour les informations Google de l'utilisateur existant
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
                $user->google_token = $googleUser->token;
                $user->google_refresh_token = $googleUser->refreshToken;
                $user->save();
            } else {
                // Créer un nouvel utilisateur
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar_url' => $googleUser->getAvatar(),
                    'email_verified_at' => now(), // Vérification automatique car Google a vérifié
                    'password' => Hash::make(Str::random(24)), // Mot de passe aléatoire
                ]);
                $user->google_token = $googleUser->token;
                $user->google_refresh_token = $googleUser->refreshToken;
                $user->save();
            }

            // Connecter l'utilisateur
            Auth::login($user, true);
            session()->regenerate();

            // Utilisation du trait pour la redirection basée sur le rôle
            return $this->redirectBasedOnRole('/dashboard', 'Connexion réussie avec Google');

        } catch (Exception $e) {
            // En cas d'erreur, rediriger vers la page de connexion avec un message d'erreur
            return redirect('/login')->with('error', 'Erreur lors de la connexion avec Google: ' . $e->getMessage());
        }
    }
}
