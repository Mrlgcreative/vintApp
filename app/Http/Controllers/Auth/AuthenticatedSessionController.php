<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        // Debug: Log pour voir le rôle de l'utilisateur
        Log::info('User authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->roles->pluck('slug')->toArray(),
            'isAdmin' => $user->isAdmin()
        ]);

        // Rediriger l'admin vers son panel
        if ($user->isAdmin()) {
            Log::info('Redirecting admin to admin dashboard');
            return redirect()->intended('/admin');
        }

        // Rediriger l'utilisateur normal vers son dashboard  
        Log::info('Redirecting user to normal dashboard');
        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
