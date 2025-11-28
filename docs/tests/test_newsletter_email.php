<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\NewsletterSubscriber;
use App\Mail\WelcomeNewsletter;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "🔍 Test d'envoi d'email newsletter...\n\n";

// Vérifier la configuration mail
echo "📧 Configuration Mail:\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM: " . config('mail.from.address') . "\n";
echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n\n";

// Récupérer le dernier abonné
$subscriber = NewsletterSubscriber::latest()->first();

if (!$subscriber) {
    echo "❌ Aucun abonné trouvé dans la base de données.\n";
    echo "   Créez un abonné de test...\n\n";
    
    // Créer un abonné de test
    $subscriber = NewsletterSubscriber::create([
        'email' => 'test@example.com',
        'name' => 'Test User',
    ]);
    
    echo "✅ Abonné de test créé: {$subscriber->email}\n\n";
}

echo "📨 Abonné trouvé:\n";
echo "   Email: {$subscriber->email}\n";
echo "   Nom: {$subscriber->name}\n";
echo "   Actif: " . ($subscriber->is_active ? 'Oui' : 'Non') . "\n";
echo "   Vérifié: " . ($subscriber->email_verified ? 'Oui' : 'Non') . "\n";
echo "   Receive Welcome: " . ($subscriber->receive_welcome ? 'Oui' : 'Non') . "\n\n";

try {
    echo "📤 Tentative d'envoi de l'email de bienvenue...\n";
    
    // Activer le mode debug pour les emails
    config(['mail.log_channel' => 'stack']);
    
    Mail::to($subscriber->email)->send(new WelcomeNewsletter($subscriber));
    
    echo "✅ Email envoyé avec succès à {$subscriber->email}!\n";
    echo "   Vérifiez votre boîte de réception et vos spams.\n\n";
    
    // Incrémenter le compteur
    $subscriber->incrementEmailsSent();
    
    echo "📊 Statistiques mises à jour:\n";
    echo "   Emails envoyés: {$subscriber->emails_sent}\n";
    
} catch (\Exception $e) {
    echo "❌ ERREUR lors de l'envoi:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    
    echo "🔍 Détails de l'erreur:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n✅ Test terminé.\n";
