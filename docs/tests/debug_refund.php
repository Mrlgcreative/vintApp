<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Testing Refund Request Process...\n\n";
    
    // Trouver une commande qui pourrait être éligible
    $orders = App\Models\Order::with(['buyer', 'item'])->get();
    
    echo "Available orders:\n";
    foreach ($orders->take(5) as $order) {
        echo "- Order #{$order->order_number}\n";
        echo "  Buyer ID: {$order->buyer_id}\n";
        echo "  Seller ID: " . ($order->seller_id ?? 'NULL') . "\n";
        echo "  Item User ID: " . ($order->item->user_id ?? 'NULL') . "\n";
        echo "  Status: {$order->status}\n";
        echo "  Confirmed by buyer: " . ($order->confirmed_by_buyer_at ? 'YES' : 'NO') . "\n";
        echo "  Refunds count: " . $order->refunds()->count() . "\n";
        echo "  Total amount: \${$order->total_amount}\n\n";
    }
    
    // Test de validation d'un formulaire simulé
    echo "Testing validation rules...\n";
    
    $testData = [
        'reason' => 'Test reason for refund',
        'refund_type' => 'full',
        'refund_amount' => null,
        'evidence_photos' => []
    ];
    
    echo "Test data validation:\n";
    echo "- Reason (min 10 chars): " . (strlen($testData['reason']) >= 10 ? "✅" : "❌") . "\n";
    echo "- Refund type (partial/full): " . (in_array($testData['refund_type'], ['partial', 'full']) ? "✅" : "❌") . "\n";
    echo "- Amount (nullable): " . ($testData['refund_amount'] === null || is_numeric($testData['refund_amount']) ? "✅" : "❌") . "\n";
    
    // Test de la méthode isRefundEligible (si possible de simuler)
    $testOrder = $orders->first();
    if ($testOrder) {
        echo "\nTesting eligibility for order #{$testOrder->order_number}:\n";
        echo "- Has confirmed_by_buyer_at: " . ($testOrder->confirmed_by_buyer_at ? "✅" : "❌") . "\n";
        echo "- Order status: {$testOrder->status}\n";
        echo "- Has existing refunds: " . ($testOrder->refunds()->exists() ? "❌" : "✅") . "\n";
    }
    
    echo "\nPossible issues to check:\n";
    echo "1. CSRF Token mismatch\n";
    echo "2. Validation errors not properly handled\n";
    echo "3. JavaScript/AJAX request format issues\n";
    echo "4. Server-side method errors\n";
    echo "5. Database constraints or relation issues\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}