<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$roles = \Illuminate\Support\Facades\DB::table('roles')->get();
foreach ($roles as $r) {
    echo "id={$r->id} | slug={$r->slug} | name={$r->name}" . PHP_EOL;
}
