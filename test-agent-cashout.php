<?php

/**
 * Script de test pour le décaissement via agents mobile money
 * 
 * Usage: php test-agent-cashout.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\MobileMoneyService;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Log;

// Test de la détection d'opérateur
function testProviderDetection(MobileMoneyService $service)
{
    echo "=== Test de Détection d'Opérateur ===\n";
    
    $testNumbers = [
        '+243841234567' => 'orange_money',
        '+243811234567' => 'mpesa',
        '+243971234567' => 'airtel_money',
        '+243901234567' => 'africell',
        '0841234567' => 'orange_money',
        '811234567' => 'mpesa',
    ];
    
    foreach ($testNumbers as $phone => $expectedProvider) {
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('detectProviderFromPhone');
        $method->setAccessible(true);
        
        $normalizedPhone = $service->normalizePhoneNumber($phone);
        $detectedProvider = $method->invoke($service, $normalizedPhone);
        
        $status = $detectedProvider === $expectedProvider ? '✅' : '❌';
        echo "{$status} {$phone} -> {$detectedProvider} (attendu: {$expectedProvider})\n";
    }
    echo "\n";
}

// Test de la configuration des agents
function testAgentConfiguration(MobileMoneyService $service)
{
    echo "=== Test de Configuration Agent ===\n";
    
    $providers = ['orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash'];
    
    foreach ($providers as $provider) {
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('getAgentConfiguration');
        $method->setAccessible(true);
        
        $config = $method->invoke($service, $provider);
        
        if (!empty($config)) {
            echo "✅ {$provider}: Configuration trouvée\n";
            echo "   - API URL: " . ($config['api_url'] ?? 'Non défini') . "\n";
        } else {
            echo "⚠️  {$provider}: Aucune configuration\n";
        }
    }
    echo "\n";
}

// Test de validation des données
function testValidationLogic()
{
    echo "=== Test de Logique de Validation ===\n";
    
    // Simuler les données de validation d'un retrait agent
    $testCases = [
        [
            'payment_method' => 'agent',
            'agent_phone' => '+243841234567',
            'agent_id' => 123,
            'amount' => 50.00,
            'description' => 'Test décaissement agent',
        ],
        [
            'payment_method' => 'agent',
            'agent_phone' => '+243811234567',
            'agent_id' => null, // agent_id optionnel
            'amount' => 100.00,
            'description' => 'Test sans agent_id',
        ],
        [
            'payment_method' => 'orange_money', // Non-agent
            'phone_number' => '+243841234567',
            'amount' => 25.00,
            'description' => 'Test décaissement direct',
        ],
    ];
    
    foreach ($testCases as $i => $case) {
        echo "Test Case " . ($i + 1) . ":\n";
        echo "  - Method: {$case['payment_method']}\n";
        
        if ($case['payment_method'] === 'agent') {
            echo "  - Agent Phone: " . ($case['agent_phone'] ?? 'Non défini') . "\n";
            echo "  - Agent ID: " . ($case['agent_id'] ?? 'Non défini') . "\n";
            
            // Vérifier si agent_phone est requis quand payment_method = agent
            if (empty($case['agent_phone'])) {
                echo "  ❌ Erreur: agent_phone requis pour payment_method=agent\n";
            } else {
                echo "  ✅ Validation OK\n";
            }
        } else {
            echo "  - Phone: " . ($case['phone_number'] ?? 'Non défini') . "\n";
            echo "  ✅ Décaissement direct OK\n";
        }
        
        echo "  - Amount: {$case['amount']}\n\n";
    }
}

// Test des métadonnées de transaction
function testTransactionMetadata()
{
    echo "=== Test des Métadonnées de Transaction ===\n";
    
    $agentData = [
        'phone_number' => '+243841234567',
        'payment_method' => 'agent',
        'agent_id' => 456,
        'agent_phone' => '+243841234567',
    ];
    
    // Simuler la construction des métadonnées comme dans WalletController
    $metadata = [
        'phone_number' => $agentData['phone_number'],
        'payment_method' => $agentData['payment_method'],
        'withdrawal_date' => date('Y-m-d H:i:s'),
    ];
    
    if (!empty($agentData['agent_id'])) {
        $metadata['agent_id'] = $agentData['agent_id'];
    }
    if (!empty($agentData['agent_phone'])) {
        $metadata['agent_phone'] = $agentData['agent_phone'];
    }
    
    echo "Métadonnées générées:\n";
    echo json_encode($metadata, JSON_PRETTY_PRINT) . "\n\n";
    
    // Vérifier que les données agent sont présentes
    if (isset($metadata['agent_id']) && isset($metadata['agent_phone'])) {
        echo "✅ Métadonnées agent correctement générées\n";
    } else {
        echo "❌ Métadonnées agent manquantes\n";
    }
    echo "\n";
}

// Fonction principale
function main()
{
    echo "🧪 Test du Système de Décaissement Agent Mobile Money\n";
    echo "=" . str_repeat('=', 55) . "\n\n";
    
    try {
        // Créer une instance du service (sans Laravel)
        $service = new MobileMoneyService();
        
        // Exécuter les tests
        testProviderDetection($service);
        testAgentConfiguration($service);
        testValidationLogic();
        testTransactionMetadata();
        
        echo "🎉 Tous les tests terminés !\n\n";
        
        echo "📋 Prochaines étapes recommandées:\n";
        echo "1. Configurer les variables d'environnement dans .env\n";
        echo "2. Obtenir les credentials API des opérateurs\n";
        echo "3. Tester avec de vraies APIs en mode sandbox\n";
        echo "4. Configurer les webhooks pour les callbacks\n";
        echo "5. Implémenter l'interface utilisateur pour les agents\n\n";
        
    } catch (Exception $e) {
        echo "❌ Erreur lors des tests: " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
}

// Fonction helper pour normaliser les numéros (copie de la méthode du service)
function normalizePhoneNumber(string $phone): string
{
    $phone = preg_replace('/[^\d+]/', '', $phone);
    
    if (str_starts_with($phone, '0')) {
        return '+243' . substr($phone, 1);
    }
    
    if (str_starts_with($phone, '243')) {
        return '+' . $phone;
    }
    
    if (!str_starts_with($phone, '+')) {
        return '+243' . $phone;
    }
    
    return $phone;
}

// Exécuter les tests seulement si le script est appelé directement
if (basename($_SERVER['SCRIPT_NAME']) === 'test-agent-cashout.php') {
    main();
}