<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use App\Models\User;

echo "🔍 DIAGNOSTIC ADMIN ACCESS\n";
echo "===========================\n\n";

// Colonnes de la table users
echo "1️⃣  Colonnes de la table users:\n";
$columns = Schema::getColumnListing('users');
echo "   " . implode(', ', $columns) . "\n\n";

// Vérifier si admin_role existe
$hasAdminRole = in_array('admin_role', $columns);
$hasRole = in_array('role', $columns);
$hasIsAdmin = in_array('is_admin', $columns);

echo "2️⃣  Champs admin disponibles:\n";
echo "   admin_role: " . ($hasAdminRole ? '✅ Oui' : '❌ Non') . "\n";
echo "   role: " . ($hasRole ? '✅ Oui' : '❌ Non') . "\n";
echo "   is_admin: " . ($hasIsAdmin ? '✅ Oui' : '❌ Non') . "\n\n";

// Compter les utilisateurs
$totalUsers = User::count();
echo "3️⃣  Total utilisateurs: {$totalUsers}\n\n";

// Essayer de trouver des admins
echo "4️⃣  Recherche d'administrateurs:\n";

if ($hasAdminRole) {
    $admins = User::where('admin_role', 1)->orWhere('admin_role', true)->get();
    echo "   Via admin_role: " . $admins->count() . " trouvé(s)\n";
    foreach ($admins as $admin) {
        echo "      - ID: {$admin->id}, Email: {$admin->email}\n";
    }
}

if ($hasRole) {
    $admins = User::where('role', 'admin')->get();
    echo "   Via role='admin': " . $admins->count() . " trouvé(s)\n";
    foreach ($admins as $admin) {
        echo "      - ID: {$admin->id}, Email: {$admin->email}\n";
    }
}

if ($hasIsAdmin) {
    $admins = User::where('is_admin', 1)->orWhere('is_admin', true)->get();
    echo "   Via is_admin: " . $admins->count() . " trouvé(s)\n";
    foreach ($admins as $admin) {
        echo "      - ID: {$admin->id}, Email: {$admin->email}\n";
    }
}

// Afficher le premier user pour exemple
echo "\n5️⃣  Premier utilisateur (exemple):\n";
$firstUser = User::first();
if ($firstUser) {
    echo "   ID: {$firstUser->id}\n";
    echo "   Email: {$firstUser->email}\n";
    echo "   Name: {$firstUser->name}\n";
    if ($hasAdminRole) echo "   Admin Role: " . ($firstUser->admin_role ?? 'null') . "\n";
    if ($hasRole) echo "   Role: " . ($firstUser->role ?? 'null') . "\n";
    if ($hasIsAdmin) echo "   Is Admin: " . ($firstUser->is_admin ?? 'null') . "\n";
}

echo "\n===========================\n";
echo "✅ Diagnostic terminé !\n\n";

// Instructions
echo "📝 Pour créer un admin:\n";
if ($hasAdminRole) {
    echo "   User::find(1)->update(['admin_role' => 1]);\n";
} elseif ($hasRole) {
    echo "   User::find(1)->update(['role' => 'admin']);\n";
} elseif ($hasIsAdmin) {
    echo "   User::find(1)->update(['is_admin' => 1]);\n";
} else {
    echo "   ⚠️  Aucun champ admin trouvé ! Vérifier la migration.\n";
}
echo "\n";
