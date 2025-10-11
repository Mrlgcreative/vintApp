<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        // Envoyer l'email de vérification si l'utilisateur n'a pas encore vérifié son email
        if ($event->user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
            Log::info('📧 Envoi de l\'email de vérification à : ' . $event->user->email);
            $event->user->sendEmailVerificationNotification();
            Log::info('✅ Email de vérification envoyé avec succès à : ' . $event->user->email);
        }
    }
}
