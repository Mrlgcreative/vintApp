<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "Diagnosing Order #12 for Refund Eligibility...\n\n";
    
    $order = App\Models\Order::find(12);
    
    if (!$order) {
        echo "❌ Order #12 not found!\n";
        exit;
    }
    
    echo "Order Details:\n";
    echo "- Order Number: {$order->order_number}\n";
    echo "- Buyer ID: {$order->buyer_id}\n";
    echo "- Status: {$order->status}\n";
    echo "- Created: {$order->created_at}\n";
    echo "- Confirmed by buyer: " . ($order->confirmed_by_buyer_at ? $order->confirmed_by_buyer_at : 'NO') . "\n";
    
    if ($order->confirmed_by_buyer_at) {
        $daysSince = $order->confirmed_by_buyer_at->diffInDays(now());
        echo "- Days since confirmation: $daysSince\n";
    }
    
    $refundsCount = $order->refunds()->count();
    echo "- Existing refunds: $refundsCount\n";
    
    if ($refundsCount > 0) {
        echo "\nExisting refunds:\n";
        foreach ($order->refunds as $refund) {
            echo "  - Refund #{$refund->id}: {$refund->status} (${$refund->refund_amount})\n";
        }
    }
    
    echo "\nEligibility Check:\n";
    echo "- Has buyer confirmation: " . ($order->confirmed_by_buyer_at ? "✅" : "❌") . "\n";
    echo "- No existing refunds: " . ($refundsCount == 0 ? "✅" : "❌") . "\n";
    
    if ($order->confirmed_by_buyer_at) {
        $daysSince = $order->confirmed_by_buyer_at->diffInDays(now());
        echo "- Within 30 days: " . ($daysSince <= 30 ? "✅" : "❌") . " ({$daysSince} days)\n";
    }
    
    echo "\nRecommendation:\n";
    if (!$order->confirmed_by_buyer_at) {
        echo "- Need to confirm delivery first\n";
    } elseif ($refundsCount > 0) {
        echo "- Refund already requested\n";
    } elseif ($order->confirmed_by_buyer_at->diffInDays(now()) > 30) {
        echo "- Too late for refund (over 30 days)\n";
    } else {
        echo "- Should be eligible for refund\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}