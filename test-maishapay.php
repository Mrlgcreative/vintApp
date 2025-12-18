<?php
/**
 * Script de test MaishaPay
 * Exécuter via: php test-maishapay.php
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\MaishaPay;

echo "=== TEST MAISHAPAY ===\n\n";

// 1. Vérifier la configuration
echo "1. Vérification configuration:\n";
$maishaPay = new MaishaPay();

if ($maishaPay->isConfigured()) {
    echo "   ✅ MaishaPay est configuré\n";
} else {
    echo "   ❌ MaishaPay n'est PAS configuré\n";
    echo "   Vérifiez les variables dans .env:\n";
    echo "   - MAISHAPAY_API_KEY\n";
    echo "   - MAISHAPAY_SECRET_KEY\n";
    exit(1);
}

echo "   Environment: " . config('services.maishapay.environment') . "\n";
echo "\n";

// 2. Test détection opérateur
echo "2. Test détection opérateur:\n";
$testNumbers = [
    '812345678' => 'VODACOM',
    '823456789' => 'VODACOM',
    '841234567' => 'ORANGE',
    '851234567' => 'ORANGE',
    '971234567' => 'AIRTEL',
    '901234567' => 'AFRICELL',
];

foreach ($testNumbers as $phone => $expected) {
    $detected = $maishaPay->detectOperator($phone);
    $status = $detected === $expected ? '✅' : '❌';
    echo "   $status $phone -> $detected (attendu: $expected)\n";
}
echo "\n";

// 3. Test formatage numéro
echo "3. Test formatage numéro:\n";
$formatted = $maishaPay->formatPhone('812345678');
echo "   812345678 -> $formatted\n";
$formatted2 = $maishaPay->formatPhone('243812345678');
echo "   243812345678 -> $formatted2\n";
echo "\n";

// 4. Test simulation de paiement (sandbox uniquement)
if (config('services.maishapay.environment') === 'sandbox') {
    echo "4. Test simulation paiement (sandbox):\n";
    
    $result = $maishaPay->simulatePayment([
        'amount' => 5000,
        'phone' => '812345678',
        'currency' => 'CDF',
        'operator' => 'VODACOM',
    ]);
    
    if ($result['success']) {
        echo "   ✅ Simulation réussie!\n";
        echo "   Transaction ID: " . $result['transaction_id'] . "\n";
        echo "   Status: " . $result['status'] . "\n";
        echo "   Message: " . $result['message'] . "\n";
    } else {
        echo "   ❌ Simulation échouée: " . $result['message'] . "\n";
    }
} else {
    echo "4. Mode production - simulation désactivée\n";
}
echo "\n";

// 5. Afficher les routes disponibles
echo "5. Routes MaishaPay:\n";
echo "   POST /payments/maishapay/initiate -> payments.maishapay.initiate\n";
echo "   GET  /payments/maishapay/status/{id} -> payments.maishapay.status\n";
echo "   POST /payments/maishapay/callback -> payments.maishapay.callback (webhook)\n";
echo "\n";

echo "=== TEST TERMINÉ ===\n";
