<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = collect(DB::select('SHOW TABLES'))->pluck('Tables_in_vintapp');
$migrations = ['messages', 'notifications', 'reviews', 'categories', 'brands', 'conversations'];

echo "Vérification des tables:\n";
foreach($migrations as $table) {
    echo "$table: " . ($tables->contains($table) ? 'EXISTS' : 'NOT EXISTS') . "\n";
}