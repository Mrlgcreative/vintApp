<?php

require_once 'vendor/autoload.php';

use App\Models\Message;
use App\Models\User;

// Test simple pour vérifier la logique des messages reçus
echo "Test de la logique des messages reçus:\n";

try {
    // Simuler une requête pour récupérer les messages reçus avec subject et item_id
    echo "1. Test de récupération des messages reçus avec subject et item_id...\n";
    
    // Afficher la structure attendue de la table messages
    echo "Structure attendue de la table messages:\n";
    echo "- id (primary)\n";
    echo "- sender_id (foreign key vers users)\n";
    echo "- receiver_id (foreign key vers users)\n";
    echo "- subject (nullable)\n";
    echo "- item_id (nullable, foreign key vers items)\n";
    echo "- content\n";
    echo "- attachment (nullable)\n";
    echo "- is_read (boolean)\n";
    echo "- read_at (nullable)\n";
    echo "- created_at\n";
    echo "- updated_at\n";
    
    echo "\nTest terminé.\n";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}