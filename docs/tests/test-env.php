<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Variables d'Environnement ===\n\n";
echo "BROADCAST_CONNECTION (env): " . env('BROADCAST_CONNECTION', 'NON DEFINI') . "\n";
echo "BROADCAST_CONNECTION (config): " . config('broadcasting.default', 'NON DEFINI') . "\n";
echo "\nPUSHER_APP_ID: " . env('PUSHER_APP_ID', 'NON DEFINI') . "\n";
echo "PUSHER_APP_KEY: " . env('PUSHER_APP_KEY', 'NON DEFINI') . "\n";
echo "PUSHER_APP_SECRET: " . (env('PUSHER_APP_SECRET') ? '***DEFINI***' : 'NON DEFINI') . "\n";
echo "PUSHER_APP_CLUSTER: " . env('PUSHER_APP_CLUSTER', 'NON DEFINI') . "\n";
echo "\nVITE_PUSHER_APP_KEY: " . env('VITE_PUSHER_APP_KEY', 'NON DEFINI') . "\n";
echo "VITE_PUSHER_APP_CLUSTER: " . env('VITE_PUSHER_APP_CLUSTER', 'NON DEFINI') . "\n";

echo "\n=== Config Broadcasting ===\n\n";
print_r(config('broadcasting'));
