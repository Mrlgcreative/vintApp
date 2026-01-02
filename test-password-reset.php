<?php
/**
 * Script de diagnostic pour le reset de mot de passe
 * Exécuter: php test-password-reset.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== Diagnostic Reset Password ===\n\n";

// Récupérer le token de la DB
$record = DB::table('password_reset_tokens')
    ->where('email', 'ochiwasky@gmail.com')
    ->first();

if (!$record) {
    echo "❌ Aucun token trouvé pour cet email\n";
    exit;
}

echo "Email: {$record->email}\n";
echo "Token hashé en DB: {$record->token}\n";
echo "Créé le: {$record->created_at}\n\n";

// Token de l'URL (le dernier utilisé)
$urlToken = 'd297e9d1f251c74484c551db83f3e13ca8342f16466d50fcdd15fa64a7c6177f';

echo "Token de l'URL: {$urlToken}\n";
echo "Longueur: " . strlen($urlToken) . " caractères\n\n";

// Test de vérification
$matches = Hash::check($urlToken, $record->token);
echo "Test Hash::check(): " . ($matches ? "✅ MATCH" : "❌ NO MATCH") . "\n\n";

// Vérifier le broker de password
$broker = app('auth.password.broker');
$user = $broker->getUser(['email' => 'ochiwasky@gmail.com']);

if ($user) {
    echo "User trouvé: {$user->email} (ID: {$user->id})\n";
    
    // Tester le token avec le broker
    $tokenExists = $broker->tokenExists($user, $urlToken);
    echo "Token valide via broker: " . ($tokenExists ? "✅ OUI" : "❌ NON") . "\n";
} else {
    echo "❌ User non trouvé\n";
}

echo "\n=== Fin diagnostic ===\n";
