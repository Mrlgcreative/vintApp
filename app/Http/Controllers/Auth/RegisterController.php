<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Events\UserRegisteredWithReferral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /**
     * Afficher la page d'inscription
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Traiter l'inscription d'un nouvel utilisateur
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => 'required|accepted',
            'referral_code' => 'nullable|string|exists:referral_codes,code',
        ], [
            'name.required' => 'Le nom est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'phone.required' => 'Le numéro de téléphone est requis.',
            'address.required' => 'L\'adresse est requise.',
            'password.required' => 'Le mot de passe est requis.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'terms.required' => 'Vous devez accepter les conditions d\'utilisation.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
            'referral_code.exists' => 'Le code de parrainage saisi n\'existe pas.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'newsletter_subscribed' => $request->boolean('newsletter'),
        ]);

        // Appliquer le code de parrainage s'il est fourni
        $referral = null;
        $referralCode = $request->filled('referral_code') ? $request->referral_code : session('referral_code');
        
        if ($referralCode) {
            $referral = $user->applyReferralCode($referralCode);
            if ($referral) {
                session()->flash('referral_success', 'Code de parrainage appliqué avec succès ! Vous avez reçu des points de bienvenue.');
                // Supprimer le code de la session maintenant qu'il a été utilisé
                session()->forget('referral_code');
            }
        }

        // Déclencher les événements
        event(new Registered($user));
        event(new UserRegisteredWithReferral($user, $referral));

        // Connecter automatiquement l'utilisateur
        Auth::login($user);

        // Rediriger vers la page de notification de vérification d'email
        return redirect()->route('verification.notice')
            ->with('success', 'Compte créé avec succès ! Veuillez vérifier votre email pour activer votre compte.');
    }
}
