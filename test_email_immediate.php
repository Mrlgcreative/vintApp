<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeNewsletter;
use Illuminate\Support\Facades\DB;

echo "🧪 Test d'envoi d'email Newsletter\n";
echo "====================================\n\n";

// Créer un abonné de test
$testEmail = 'gloirelumingu1@gmail.com';

echo "📧 Email de test : $testEmail\n\n";

// Supprimer l'ancien abonné de test s'il existe
NewsletterSubscriber::where('email', $testEmail)->delete();

// Créer un nouvel abonné
$subscriber = NewsletterSubscriber::create([
    'email' => $testEmail,
    'name' => 'Test User',
]);

echo "✅ Abonné créé : {$subscriber->email}\n";
echo "🔑 Token de désabonnement : {$subscriber->unsubscribe_token}\n\n";

// Vérifier le nombre de jobs AVANT l'envoi
$jobsAvant = DB::table('jobs')->count();
echo "📊 Jobs en queue AVANT : $jobsAvant\n\n";

// Envoyer l'email
echo "📤 Envoi de l'email de bienvenue...\n";
try {
    Mail::to($subscriber->email)->send(new WelcomeNewsletter($subscriber));
    echo "✅ Commande d'envoi exécutée avec succès !\n\n";
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n\n";
}

// Vérifier le nombre de jobs APRÈS l'envoi
$jobsApres = DB::table('jobs')->count();
echo "📊 Jobs en queue APRÈS : $jobsApres\n";

if ($jobsApres > $jobsAvant) {
    echo "⚠️  L'email a été MIS EN QUEUE (différé)\n";
    echo "💡 Pour l'envoyer immédiatement, exécutez : php artisan queue:work --once\n";
} else {
    echo "✅ L'email a été ENVOYÉ IMMÉDIATEMENT (sans queue)\n";
}

echo "\n📬 Vérifiez votre boîte email : $testEmail\n";
echo "   (N'oubliez pas de vérifier les SPAMS)\n\n";

echo "🔍 Pour voir les logs : tail -f storage/logs/laravel.log\n";
