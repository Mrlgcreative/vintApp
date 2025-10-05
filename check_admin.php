<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User;

$admin = User::where('email', 'admin@vintapp.com')->first();

if ($admin) {
    echo "Admin user: " . $admin->name . PHP_EOL;
    echo "Has admin role: " . ($admin->isAdmin() ? "Yes" : "No") . PHP_EOL;
    echo "Roles: " . $admin->roles->pluck('name')->join(', ') . PHP_EOL;
} else {
    echo "Admin user not found!" . PHP_EOL;
}