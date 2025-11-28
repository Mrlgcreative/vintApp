<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Structure de la table orders:\n";
$columns = DB::select('DESCRIBE orders');
foreach($columns as $column) {
    echo "- {$column->Field}: {$column->Type}\n";
}

echo "\nRecherche des fichiers contenant 'user_id' en relation avec 'orders':\n";