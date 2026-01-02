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
use Illuminate\Support\Facades\Password;

echo "=== Diagnostic Reset Password ===\n\n";

$email = 'ochiwasky@gmail.com';

// Récupérer le token de la DB
$record = DB::table('password_reset_tokens')
    ->where('email', $email)
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

// Supprimer l'ancien token et en créer un nouveau
echo "=== Création d'un NOUVEAU token valide ===\n\n";

$user = \App\Models\User::where('email', $email)->first();
if ($user) {
    // Supprimer l'ancien token
    DB::table('password_reset_tokens')->where('email', $email)->delete();
    
    // Créer un nouveau token via le broker
    $token = Password::broker()->createToken($user);
    
    echo "✅ Nouveau token créé: {$token}\n";
    echo "Longueur: " . strlen($token) . " caractères\n\n";
    
    // Construire l'URL de reset
    $resetUrl = url("/reset-password/{$token}?email=" . urlencode($email));
    echo "🔗 URL de reset:\n{$resetUrl}\n\n";
    
    // Vérifier que le nouveau token fonctionne
    $newRecord = DB::table('password_reset_tokens')->where('email', $email)->first();
    $newMatches = Hash::check($token, $newRecord->token);
    echo "Vérification nouveau token: " . ($newMatches ? "✅ MATCH" : "❌ NO MATCH") . "\n";
} else {
    echo "❌ User non trouvé\n";
}

echo "\n=== Fin diagnostic ===\n";
