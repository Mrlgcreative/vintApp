<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Structure de la table brands:\n";
$columns = DB::select('DESCRIBE brands');
foreach($columns as $column) {
    echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Default: {$column->Default})\n";
}

echo "\nNombre de marques dans la table:\n";
$count = DB::table('brands')->count();
echo "Nombre total: {$count}\n";

if ($count > 0) {
    echo "\nDernières marques ajoutées:\n";
    $brands = DB::table('brands')->orderBy('id', 'desc')->limit(3)->get();
    foreach($brands as $brand) {
        echo "- ID: {$brand->id}, Nom: {$brand->name}, Slug: {$brand->slug}, Active: {$brand->is_active}\n";
    }
}