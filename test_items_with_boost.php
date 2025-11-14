<?php

use App\Models\Item;

// Tester que les items sont chargés avec leurs boosts
$items = Item::with(['category', 'brand', 'user', 'activeBoosts.boostType'])
    ->whereHas('activeBoosts')
    ->where('status', 'active')
    ->get();

echo "Articles avec boosts: " . $items->count() . "\n";

foreach ($items as $item) {
    echo "- {$item->name} (ID: {$item->id})\n";
    foreach ($item->activeBoosts as $boost) {
        echo "  * Boost: {$boost->boostType->name} (expires: {$boost->expires_at})\n";
    }
}

$regularItems = Item::with(['category', 'brand', 'user'])
    ->whereDoesntHave('activeBoosts')
    ->where('status', 'active')
    ->count();

echo "\nArticles sans boost: " . $regularItems . "\n";