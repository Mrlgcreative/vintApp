<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Accès complet à la gestion des transactions et utilisateurs'],
            ['name' => 'Utilisateur', 'slug' => 'user', 'description' => 'Accès standard aux fonctionnalités'],
            ['name' => 'Vendeur', 'slug' => 'vendeur', 'description' => 'Peut publier des produits et vendre'],
            ['name' => 'Expert', 'slug' => 'expert', 'description' => 'Peut valider l’authenticité des produits'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
