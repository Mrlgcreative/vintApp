<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AdminRedirection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'avatar_url' => $googleUser->getAvatar(),
                ]);
            } else {
                // Créer un nouvel utilisateur
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'avatar_url' => $googleUser->getAvatar(),
                    'email_verified_at' => now(), // Vérification automatique car Google a vérifié
                    'password' => Hash::make(Str::random(24)), // Mot de passe aléatoire
                ]);
            }

            // Connecter l'utilisateur
            Auth::login($user, true);

            // Utilisation du trait pour la redirection basée sur le rôle
            return $this->redirectBasedOnRole('/dashboard', 'Connexion réussie avec Google');

        } catch (Exception $e) {
            // En cas d'erreur, rediriger vers la page de connexion avec un message d'erreur
            return redirect('/login')->with('error', 'Erreur lors de la connexion avec Google: ' . $e->getMessage());
        }
    }

    /**
     * Déconnecter et révoquer les tokens Google (optionnel)
     */
    public function revokeGoogleAccess()
    {
        $user = Auth::user();

        if ($user && $user->google_token) {
            try {
                // Révoquer le token Google
                $client = new \Google_Client();
                $client->revokeToken($user->google_token);

                // Supprimer les informations Google de la base de données
                $user->update([
                    'google_id' => null,
                    'google_token' => null,
                    'google_refresh_token' => null,
                ]);

                return back()->with('success', 'Accès Google révoqué avec succès.');
            } catch (Exception $e) {
                return back()->with('error', 'Erreur lors de la révocation: ' . $e->getMessage());
            }
        }

        return back()->with('error', 'Aucun compte Google lié.');
    }
}
