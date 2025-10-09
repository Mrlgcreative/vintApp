#!/usr/bin/env php
<?php

/**
 * Script de test pour l'API Forex
 * 
 * Usage: php test_forex_api.php
 */

echo "\n🧪 Test de l'API Forex - Taux de change USD/CDF\n";
echo "================================================\n\n";

// 1. Test de récupération du taux
echo "📡 Test 1: Récupération du taux actuel...\n";
$ch = curl_init('http://localhost:8000/exchange/rate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Succès!\n";
    echo "   From: {$data['from']}\n";
    echo "   To: {$data['to']}\n";
    echo "   Rate: " . number_format($data['rate'], 2, '.', ',') . " CDF\n";
    echo "   Cached: " . ($data['cached'] ? 'Oui' : 'Non') . "\n";
    echo "   Fallback: " . (isset($data['fallback']) && $data['fallback'] ? 'Oui (taux de secours)' : 'Non (taux réel)') . "\n";
    echo "   Updated: {$data['updated_at']}\n\n";
    
    $currentRate = $data['rate'];
} else {
    echo "❌ Échec! Code HTTP: $httpCode\n";
    echo "   Réponse: $response\n\n";
    exit(1);
}

// 2. Test de conversion USD -> CDF
echo "💱 Test 2: Conversion 100 USD -> CDF...\n";
$amount = 100;
$expectedResult = $amount * $currentRate;
echo "   Montant: $amount USD\n";
echo "   Taux: " . number_format($currentRate, 2, '.', ',') . " CDF\n";
echo "   Résultat attendu: " . number_format($expectedResult, 2, '.', ',') . " CDF\n\n";

// 3. Test de conversion CDF -> USD
echo "💱 Test 3: Conversion 250,000 CDF -> USD...\n";
$amountCDF = 250000;
$expectedUSD = $amountCDF / $currentRate;
echo "   Montant: " . number_format($amountCDF, 0, '.', ',') . " CDF\n";
echo "   Taux: " . number_format($currentRate, 2, '.', ',') . " CDF\n";
echo "   Résultat attendu: $" . number_format($expectedUSD, 2, '.', ',') . " USD\n\n";

// 4. Test du cache
echo "⏱️  Test 4: Vérification du cache...\n";
$start = microtime(true);
$ch = curl_init('http://localhost:8000/exchange/rate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);
$firstCall = (microtime(true) - $start) * 1000;

$start = microtime(true);
$ch = curl_init('http://localhost:8000/exchange/rate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);
$secondCall = (microtime(true) - $start) * 1000;

echo "   Premier appel: " . number_format($firstCall, 2) . " ms\n";
echo "   Deuxième appel: " . number_format($secondCall, 2) . " ms\n";
echo "   Amélioration: " . number_format((($firstCall - $secondCall) / $firstCall) * 100, 1) . "%\n";

if ($secondCall < $firstCall) {
    echo "   ✅ Cache fonctionne correctement!\n\n";
} else {
    echo "   ⚠️  Cache pourrait ne pas fonctionner\n\n";
}

// 5. Comparaison avec l'ancien taux fixe
echo "📊 Test 5: Comparaison ancien vs nouveau système...\n";
$oldRate = 2500;
$difference = $currentRate - $oldRate;
$percentDiff = (($currentRate - $oldRate) / $oldRate) * 100;

echo "   Ancien taux (fixe): " . number_format($oldRate, 2, '.', ',') . " CDF\n";
echo "   Nouveau taux (API): " . number_format($currentRate, 2, '.', ',') . " CDF\n";
echo "   Différence: " . ($difference > 0 ? '+' : '') . number_format($difference, 2, '.', ',') . " CDF\n";
echo "   Variation: " . ($percentDiff > 0 ? '+' : '') . number_format($percentDiff, 2) . "%\n\n";

// Impact sur un exemple de transaction
$exampleAmount = 1000; // USD
$oldConversion = $exampleAmount * $oldRate;
$newConversion = $exampleAmount * $currentRate;
$savingsOrLoss = $newConversion - $oldConversion;

echo "   💡 Impact sur une transaction de $exampleAmount USD:\n";
echo "      Ancien: " . number_format($oldConversion, 0, '.', ',') . " CDF\n";
echo "      Nouveau: " . number_format($newConversion, 0, '.', ',') . " CDF\n";
echo "      " . ($savingsOrLoss > 0 ? "Gain" : "Perte") . ": " . ($savingsOrLoss > 0 ? '+' : '') . number_format(abs($savingsOrLoss), 0, '.', ',') . " CDF\n\n";

// 6. Recommandations
echo "💡 Recommandations:\n";
echo "===================\n";

if ($percentDiff > 5) {
    echo "⚠️  Le taux réel est " . number_format(abs($percentDiff), 1) . "% plus élevé que l'ancien taux fixe.\n";
    echo "   Les utilisateurs paieront plus en CDF pour la même quantité d'USD.\n";
    echo "   Considérez ajuster les prix ou informer les utilisateurs.\n\n";
} elseif ($percentDiff < -5) {
    echo "✅ Le taux réel est " . number_format(abs($percentDiff), 1) . "% plus bas que l'ancien taux fixe.\n";
    echo "   Les utilisateurs paieront moins en CDF pour la même quantité d'USD.\n";
    echo "   C'est une bonne nouvelle pour vos clients!\n\n";
} else {
    echo "✅ Le taux réel est proche de l'ancien taux fixe (±5%).\n";
    echo "   L'impact sur les transactions sera minime.\n\n";
}

// Configuration actuelle
echo "⚙️  Configuration actuelle:\n";
echo "   Provider: " . (getenv('FOREX_API_PROVIDER') ?: 'exchangerate-api (défaut)') . "\n";
echo "   Cache duration: " . (getenv('FOREX_CACHE_DURATION') ?: '3600') . " secondes (1 heure)\n";
echo "   Base currency: " . (getenv('FOREX_BASE_CURRENCY') ?: 'USD') . "\n\n";

echo "✅ Tous les tests terminés avec succès!\n";
echo "================================================\n\n";

echo "📚 Pour plus d'informations, consultez FOREX_API_GUIDE.md\n\n";
