<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Traits\AdminRedirection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AdminRedirection;

    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Afficher la page de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la tentative de connexion
     *
     * Utilise LoginRequest qui gère :
     *  - la validation,
     *  - la protection brute force (RateLimiter par email+IP),
     *  - l'événement Lockout après trop de tentatives.
     */
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Récupérer l'utilisateur connecté pour la redirection basée sur le rôle
        $user = $request->user();

        // Utilisation du trait pour la redirection basée sur le rôle
        return $this->redirectBasedOnRole(route('dashboard'), 'Connexion réussie');
    }

    /**
     * Déconnecter l'utilisateur
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
