<?php

use App\Models\User;
use App\Models\Role;

// Récupérer le premier utilisateur
$user = User::first();
$adminRole = Role::where('slug', 'admin')->first();

if ($user && $adminRole) {
    // Assigner le rôle admin si pas déjà assigné
    if (!$user->roles()->where('slug', 'admin')->exists()) {
        $user->roles()->attach($adminRole->id);
        echo "Rôle admin assigné à {$user->name}" . PHP_EOL;
    } else {
        echo "L'utilisateur {$user->name} a déjà le rôle admin" . PHP_EOL;
    }
} else {
    echo "Utilisateur ou rôle admin non trouvé" . PHP_EOL;
}

// Lister tous les utilisateurs avec leurs rôles
echo "\nUtilisateurs et leurs rôles:" . PHP_EOL;
User::with('roles')->get()->each(function($user) {
    $roles = $user->roles->pluck('slug')->join(', ') ?: 'Aucun rôle';
    echo "- {$user->name}: {$roles}" . PHP_EOL;
});