<?php

use Illuminate\Support\Facades\Mail;

// Test d'envoi d'email simple
Mail::raw('Ceci est un email de test depuis VintApp ! 🚀', function($message) {
    $message->to('test@example.com')
            ->subject('Test Email Mailtrap - VintApp');
});

echo "✅ Email de test envoyé ! Vérifiez votre inbox Mailtrap.\n";
