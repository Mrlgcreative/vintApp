<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Récupérer le premier utilisateur
$user = App\Models\User::first();

if ($user) {
    echo "🔍 Test d'envoi d'email de vérification\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "📧 Utilisateur: {$user->name}\n";
    echo "📧 Email: {$user->email}\n";
    echo "📧 Email vérifié: " . ($user->email_verified_at ? 'Oui' : 'Non') . "\n\n";
    
    try {
        // Envoyer la notification
        $user->sendEmailVerificationNotification();
        echo "✅ Email de vérification envoyé avec succès !\n";
        echo "📬 Vérifiez votre boîte email: {$user->email}\n";
    } catch (\Exception $e) {
        echo "❌ Erreur lors de l'envoi: " . $e->getMessage() . "\n";
        echo "📋 Trace: " . $e->getTraceAsString() . "\n";
    }
} else {
    echo "❌ Aucun utilisateur trouvé dans la base de données\n";
    echo "💡 Créez d'abord un utilisateur avec: php artisan tinker\n";
}
