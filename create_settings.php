<?php

require_once 'vendor/autoload.php';

// Charger l'application Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Création des paramètres de base...\n";
    
    $settings = [
        [
            'key' => 'app_name',
            'value' => 'VintApp',
            'type' => 'string',
            'category' => 'general',
            'label' => 'Nom de l\'application',
            'description' => 'Le nom affiché de l\'application',
            'is_public' => true,
            'is_encrypted' => false
        ],
        [
            'key' => 'app_description',
            'value' => 'Plateforme de vente d\'articles vintage',
            'type' => 'string',
            'category' => 'general',
            'label' => 'Description',
            'description' => 'Description de l\'application',
            'is_public' => true,
            'is_encrypted' => false
        ],
        [
            'key' => 'app_maintenance',
            'value' => false,
            'type' => 'boolean',
            'category' => 'general',
            'label' => 'Mode maintenance',
            'description' => 'Activer le mode maintenance',
            'is_public' => false,
            'is_encrypted' => false
        ],
        [
            'key' => 'max_file_size',
            'value' => 10,
            'type' => 'integer',
            'category' => 'upload',
            'label' => 'Taille max fichier (MB)',
            'description' => 'Taille maximale des fichiers uploadés',
            'is_public' => false,
            'is_encrypted' => false
        ],
        [
            'key' => 'admin_email',
            'value' => 'admin@vintapp.com',
            'type' => 'string',
            'category' => 'contact',
            'label' => 'Email administrateur',
            'description' => 'Email de contact administrateur',
            'is_public' => true,
            'is_encrypted' => false
        ]
    ];
    
    foreach($settings as $setting) {
        $created = \App\Models\Setting::updateOrCreate(
            ['key' => $setting['key']], 
            $setting
        );
        echo "Paramètre créé/mis à jour: " . $created->key . " = " . $created->value . "\n";
    }
    
    echo "Tous les paramètres ont été créés avec succès!\n";
    
} catch (\Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}