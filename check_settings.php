<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Paramètres dans la table settings ===\n\n";

$settings = DB::table('settings')->get();

foreach ($settings as $setting) {
    echo "Key: {$setting->key}\n";
    echo "Value: {$setting->value}\n";
    echo "Type: {$setting->type}\n";
    echo "Description: {$setting->description}\n";
    echo "---\n\n";
}

echo "Total: " . $settings->count() . " paramètres\n";
