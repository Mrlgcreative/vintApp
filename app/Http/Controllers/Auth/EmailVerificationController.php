<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;

class EmailVerificationController extends Controller
{
    /**
     * Afficher la page de notification de vérification d'email
     */
    public function notice()
    {
        // Si l'email est déjà vérifié, rediriger vers le dashboard
        if (auth()->user() && auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Vérifier l'email de l'utilisateur
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('info', 'Votre email est déjà vérifié.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Rediriger avec une session spéciale pour afficher la modale
        return redirect()->route('dashboard')->with('email_verified', true);
    }

    /**
     * Renvoyer l'email de vérification
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('info', 'Votre email est déjà vérifié.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Un nouvel email de vérification a été envoyé !');
    }
}
