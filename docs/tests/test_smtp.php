<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

echo "🧪 Test de Connexion SMTP Gmail\n";
echo "================================\n\n";

// Afficher la configuration actuelle (masquée)
echo "📋 Configuration actuelle :\n";
echo "   MAIL_MAILER : " . Config::get('mail.default') . "\n";
echo "   MAIL_HOST   : " . Config::get('mail.mailers.smtp.host') . "\n";
echo "   MAIL_PORT   : " . Config::get('mail.mailers.smtp.port') . "\n";
echo "   MAIL_USERNAME : " . substr(Config::get('mail.mailers.smtp.username'), 0, 5) . "***\n";
echo "   MAIL_ENCRYPTION : " . Config::get('mail.mailers.smtp.encryption') . "\n";
echo "   MAIL_FROM : " . Config::get('mail.from.address') . "\n\n";

echo "📤 Envoi d'un email de test...\n";

$emailTest = 'gloirelumingu1@gmail.com';

try {
    $startTime = microtime(true);
    
    Mail::raw('Ceci est un email de test envoyé depuis VintApp pour vérifier la configuration SMTP.', function($message) use ($emailTest) {
        $message->to($emailTest)
                ->subject('🧪 Test SMTP - VintApp')
                ->from(config('mail.from.address'), config('mail.from.name'));
    });
    
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime), 2);
    
    echo "✅ Email envoyé avec succès en {$duration}s !\n\n";
    echo "📬 Destinataire : $emailTest\n";
    echo "📥 Vérifiez votre boîte email (et les SPAMS)\n\n";
    
    echo "💡 Conseils :\n";
    echo "   1. Attendez 1-2 minutes (délais SMTP)\n";
    echo "   2. Vérifiez le dossier SPAM/Indésirables\n";
    echo "   3. Vérifiez l'onglet Promotions (Gmail)\n";
    echo "   4. Ajoutez " . config('mail.from.address') . " à vos contacts\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR lors de l'envoi !\n\n";
    echo "Message : " . $e->getMessage() . "\n\n";
    
    echo "🔧 Solutions possibles :\n";
    
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "   ❌ Le serveur SMTP refuse la connexion\n";
        echo "      → Vérifiez MAIL_HOST et MAIL_PORT dans .env\n";
        echo "      → Gmail : smtp.gmail.com port 587\n";
    }
    
    if (strpos($e->getMessage(), 'Authentication') !== false || strpos($e->getMessage(), 'Username') !== false) {
        echo "   ❌ Problème d'authentification\n";
        echo "      → Vérifiez MAIL_USERNAME et MAIL_PASSWORD\n";
        echo "      → Utilisez un Mot de Passe d'Application Gmail\n";
        echo "      → https://myaccount.google.com/apppasswords\n";
    }
    
    if (strpos($e->getMessage(), 'timeout') !== false) {
        echo "   ❌ Timeout de connexion\n";
        echo "      → Ajoutez MAIL_TIMEOUT=30 dans .env\n";
        echo "      → Vérifiez votre connexion internet\n";
        echo "      → Vérifiez que le port 587 n'est pas bloqué par un firewall\n";
    }
    
    echo "\n📄 Vérifiez aussi les logs : storage/logs/laravel.log\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
