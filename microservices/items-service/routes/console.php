<?php

use App\Models\Category;
use App\Services\ItemService;
use Illuminate\Support\Facades\Artisan;

Artisan::command('demo:item {--seller=1 : user_id du vendeur}', function () {
    $category = Category::firstOrCreate(
        ['slug' => 'demo'],
        ['name' => 'Démo', 'is_active' => true],
    );

    $item = app(ItemService::class)->create([
        'name' => 'Article démo',
        'description' => 'Créé par demo:item',
        'price' => 100,
        'currency' => config('items.currency.default', 'USD'),
        'quantity' => 1,
        'condition' => 'good',
        'status' => 'active',
        'category_id' => $category->id,
    ], (int) $this->option('seller'));

    $this->line("item public_id : {$item->public_id}");
    $this->line('relayez : php artisan events:relay --once');

    return self::SUCCESS;
})->purpose('Crée un article de démonstration et son événement outbox');
