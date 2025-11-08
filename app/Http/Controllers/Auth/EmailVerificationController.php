<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Mail\VerificationCodeMail;
use App\Models\User;

class EmailVerificationController extends Controller
{
    /**
     * Afficher la page de notification de vérification d'email
     */
    public function notice()
    {
        // Si l'email est déjà vérifié, rediriger vers le dashboard
        if (Auth::user() && !is_null(Auth::user()->email_verified_at)) {
            return redirect()->route('dashboard');
        }

        return view('auth.verify-email');
    }

    /**
     * Afficher la page de saisie du code de vérification
     */
    public function showCodeForm()
    {
        // Si l'email est déjà vérifié, rediriger vers le dashboard
        if (Auth::user() && !is_null(Auth::user()->email_verified_at)) {
            return redirect()->route('dashboard');
        }

        // Si pas de code généré ou expiré, en générer un nouveau
        $user = Auth::user();
        if (!$user->verification_code || ($user->verification_code_expires_at && $user->verification_code_expires_at->isPast())) {
            $this->sendVerificationCode($user);
        }

        return view('auth.verify-code');
    }

    /**
     * Vérifier le code de vérification
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6|regex:/^[0-9]{6}$/'
        ], [
            'verification_code.required' => 'Le code de vérification est requis.',
            'verification_code.size' => 'Le code doit contenir exactement 6 chiffres.',
            'verification_code.regex' => 'Le code doit contenir uniquement des chiffres.'
        ]);

        $user = Auth::user();

        // Vérifier si le code est valide
        if (!$user->isValidVerificationCode($request->verification_code)) {
            return back()->with('error', 'Code invalide ou expiré. Veuillez demander un nouveau code.');
        }

        // Marquer l'email comme vérifié
        Log::info('Avant markEmailAsVerifiedWithCode', [
            'user_id' => $user->id,
            'email_verified_at_before' => $user->email_verified_at
        ]);

        if ($user->markEmailAsVerifiedWithCode()) {
            Log::info('Après markEmailAsVerifiedWithCode', [
                'user_id' => $user->id,
                'email_verified_at_after' => $user->email_verified_at
            ]);

            // Récupérer l'utilisateur mis à jour depuis la base de données
            $freshUser = User::find($user->id);
            
            Log::info('Utilisateur récupéré de la DB', [
                'user_id' => $freshUser->id,
                'email_verified_at_fresh' => $freshUser->email_verified_at
            ]);
            
            // Forcer la mise à jour de l'utilisateur en session
            Auth::logout();
            Auth::login($freshUser, true);
            
            Log::info('Email vérifié avec succès pour l\'utilisateur: ' . $freshUser->email, [
                'user_id' => $freshUser->id,
                'email_verified_at' => $freshUser->email_verified_at
            ]);
            
            event(new Verified($freshUser));
            
            return redirect()->route('dashboard')->with('success', 'Email vérifié avec succès ! Bienvenue sur VintApp.');
        }

        return back()->with('error', 'Erreur lors de la vérification. Veuillez réessayer.');
    }

    /**
     * Renvoyer un code de vérification
     */
    public function resendCode()
    {
        $user = Auth::user();

        if (!is_null($user->email_verified_at)) {
            return redirect()->route('dashboard')->with('info', 'Votre email est déjà vérifié.');
        }

        $this->sendVerificationCode($user);

        return back()->with('success', 'Un nouveau code de vérification a été envoyé !');
    }

    /**
     * Envoyer le code de vérification par email
     */
    private function sendVerificationCode($user)
    {
        $code = $user->generateVerificationCode();
        
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
        } catch (\Exception $e) {
            Log::error('Erreur envoi email vérification: ' . $e->getMessage());
            // En cas d'erreur d'envoi, on peut quand même continuer
        }
    }

    /**
     * Vérifier l'email de l'utilisateur (ancien système avec lien)
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
     * Renvoyer l'email de vérification (ancien système)
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
