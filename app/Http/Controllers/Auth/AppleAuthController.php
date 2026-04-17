<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AppleAuthController extends Controller
{
    /**
     * Rediriger vers Apple pour l'authentification
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToApple()
    {
        return Socialite::driver('apple')
            ->scopes(['name', 'email'])
            ->redirect();
    }

    /**
     * Gérer le callback d'Apple après authentification
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleAppleCallback()
    {
        try {
            // Récupérer les informations de l'utilisateur depuis Apple
            $appleUser = Socialite::driver('apple')->user();

            // Rechercher l'utilisateur par apple_id
            $user = User::where('apple_id', $appleUser->getId())->first();

            if ($user) {
                // L'utilisateur existe déjà avec ce compte Apple
                // Mettre à jour l'avatar si disponible
                if ($appleUser->getAvatar() && !$user->avatar_url) {
                    $user->avatar_url = $appleUser->getAvatar();
                    $user->save();
                }
            } else {
                // Vérifier si un utilisateur existe avec cet email
                $existingUser = User::where('email', $appleUser->getEmail())->first();

                if ($existingUser) {
                    // Lier le compte Apple à l'utilisateur existant
                    $existingUser->apple_id = $appleUser->getId();
                    
                    // Mettre à jour l'avatar si l'utilisateur n'en a pas
                    if ($appleUser->getAvatar() && !$existingUser->avatar_url) {
                        $existingUser->avatar_url = $appleUser->getAvatar();
                    }
                    
                    // Vérifier automatiquement l'email pour OAuth
                    if (!$existingUser->email_verified_at) {
                        $existingUser->email_verified_at = now();
                    }
                    
                    $existingUser->save();
                    $user = $existingUser;
                } else {
                    // Créer un nouvel utilisateur
                    // Apple peut ne pas fournir le nom lors des connexions suivantes
                    $name = $appleUser->getName() ?? 'Utilisateur Apple';
                    
                    $user = User::create([
                        'name' => $name,
                        'email' => $appleUser->getEmail(),
                        'apple_id' => $appleUser->getId(),
                        'avatar_url' => $appleUser->getAvatar(),
                        'password' => Hash::make(Str::random(24)), // Mot de passe aléatoire
                        'email_verified_at' => now(), // Vérifier automatiquement l'email pour OAuth
                    ]);
                }
            }

            // Connecter l'utilisateur
            Auth::login($user, true);
            session()->regenerate();

            // Rediriger vers le dashboard
            return redirect()->intended('/dashboard')->with('success', 'Connexion réussie avec Apple !');

        } catch (\Exception $e) {
            // En cas d'erreur, rediriger vers la page de connexion
            return redirect('/login')->with('error', 'Échec de la connexion avec Apple. Veuillez réessayer.');
        }
    }

    /**
     * Révoquer l'accès Apple (optionnel)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function revokeAppleAccess(Request $request)
    {
        $user = Auth::user();
        
        if ($user && $user->apple_id) {
            // Supprimer l'association avec Apple
            $user->apple_id = null;
            $user->save();
            
            return redirect()->back()->with('success', 'L\'accès Apple a été révoqué avec succès.');
        }
        
        return redirect()->back()->with('error', 'Aucun compte Apple associé.');
    }
}
