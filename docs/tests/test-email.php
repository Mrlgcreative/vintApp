<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "🚀 Test d'envoi d'email vers Mailtrap...\n\n";

try {
    Mail::raw('Ceci est un email de test depuis VintApp ! 🎉', function($message) {
        $message->to('test@example.com')
                ->subject('✅ Test Email Mailtrap - VintApp');
    });
    
    echo "✅ Email envoyé avec succès !\n";
    echo "📧 Vérifiez votre inbox Mailtrap : https://mailtrap.io/inboxes\n\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur lors de l'envoi : " . $e->getMessage() . "\n";
    echo "\n💡 Vérifiez que vous avez bien configuré :\n";
    echo "   - MAIL_USERNAME dans .env\n";
    echo "   - MAIL_PASSWORD dans .env\n";
}
