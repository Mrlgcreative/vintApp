<?php
require_once 'vendor/autoload.php';

use Kreait\Firebase\Factory;

// Configuration Firebase simple
$firebase = (new Factory)->create();
$auth = $firebase->createAuth();

try {
    // Essayer de créer un utilisateur test
    echo "📝 Tentative de création d'un utilisateur test...\n";
    
    $userProperties = [
        'email' => 'test@example.com',
        'password' => 'password123',
        'displayName' => 'Test User',
        'emailVerified' => true
    ];
    
    $user = $auth->createUser($userProperties);
    
    echo "✅ Utilisateur créé avec succès !\n";
    echo "UID: " . $user->uid . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Nom: " . $user->displayName . "\n";
    
} catch (Exception $e) {
    echo "⚠️ Erreur: " . $e->getMessage() . "\n";
    
    // Vérifier si l'utilisateur existe déjà
    try {
        echo "🔍 Vérification si l'utilisateur existe déjà...\n";
        $existingUser = $auth->getUserByEmail('test@example.com');
        echo "✅ Utilisateur trouvé:\n";
        echo "UID: " . $existingUser->uid . "\n";
        echo "Email: " . $existingUser->email . "\n";
        echo "Nom: " . ($existingUser->displayName ?? 'Non défini') . "\n";
        echo "Email vérifié: " . ($existingUser->emailVerified ? 'Oui' : 'Non') . "\n";
        
    } catch (Exception $e2) {
        echo "❌ Utilisateur introuvable: " . $e2->getMessage() . "\n";
        echo "💡 Vous devez probablement créer l'utilisateur dans la console Firebase.\n";
    }
}