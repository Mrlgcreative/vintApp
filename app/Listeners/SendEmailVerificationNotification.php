<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

class SendEmailVerificationNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        
        // Générer et envoyer le code de vérification si l'utilisateur n'a pas encore vérifié son email
        if ($user instanceof User && ! $user->hasVerifiedEmail()) {
            try {
                Log::info('📧 Génération du code de vérification pour : ' . $user->email);
                
                // Générer et stocker le code de vérification (expire après 15 minutes)
                $code = $user->generateVerificationCode();
                
                Log::info('📧 Envoi du code de vérification par email à : ' . $user->email);
                
                // Envoyer le code par email
                Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
                
                Log::info('✅ Code de vérification envoyé avec succès à : ' . $user->email, [
                    'user_id' => $user->id,
                    'verification_code' => $code
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Erreur lors de l\'envoi du code de vérification : ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
        }
    }
}
