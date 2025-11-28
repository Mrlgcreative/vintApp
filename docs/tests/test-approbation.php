<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\UserWaiting;
use Illuminate\Support\Str;

echo "🚀 Création d'un utilisateur de test pour la pré-inscription...\n\n";

// Supprimer l'ancien si existe
$existing = UserWaiting::where('email', 'testuser@example.com')->first();
if ($existing) {
    echo "🗑️  Suppression de l'ancien utilisateur de test...\n";
    $existing->forceDelete();
}

// Créer un nouvel utilisateur en attente
$userWaiting = UserWaiting::create([
    'name' => 'Test User VintApp',
    'email' => 'testuser@example.com',
    'phone' => '+243812345678',
    'country' => 'RDC',
    'message' => 'Je souhaite rejoindre VintApp pour acheter et vendre des articles vintage !',
    'confirmation_token' => Str::random(32),
    'status' => 'confirmed',
    'email_confirmed_at' => now(),
    'ip_address' => '127.0.0.1',
    'user_agent' => 'Test Script',
]);

echo "✅ Utilisateur créé avec succès !\n";
echo "📧 Email: {$userWaiting->email}\n";
echo "📝 Nom: {$userWaiting->name}\n";
echo "📊 Statut: {$userWaiting->status}\n\n";

echo "🔄 Approbation de l'utilisateur...\n";

try {
    // Approuver l'utilisateur (cela va créer le compte + envoyer l'email)
    $userWaiting->approve("Utilisateur de test approuvé automatiquement");
    
    echo "✅ Utilisateur approuvé avec succès !\n";
    echo "📧 Un email a été envoyé à : {$userWaiting->email}\n";
    echo "🔗 L'email contient un lien pour définir le mot de passe\n\n";
    
    echo "👀 Maintenant, allez voir dans votre inbox Mailtrap :\n";
    echo "   https://mailtrap.io/inboxes\n\n";
    
    echo "📨 Vous devriez voir un email avec :\n";
    echo "   - Sujet : ✅ Votre compte VintApp est prêt ! Définissez votre mot de passe\n";
    echo "   - Un bouton violet pour définir le mot de passe\n";
    echo "   - Une date d'expiration (7 jours)\n\n";
    
    // Afficher le compte User créé
    $user = \App\Models\User::where('email', $userWaiting->email)->first();
    if ($user) {
        echo "✅ Compte utilisateur créé :\n";
        echo "   - ID: {$user->id}\n";
        echo "   - Nom: {$user->name}\n";
        echo "   - Email: {$user->email}\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Erreur lors de l'approbation : " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
