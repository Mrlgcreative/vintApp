<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le rôle admin s'il n'existe pas
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrateur']
        );

        // Créer l'utilisateur admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@vintapp.com',
            'password' => Hash::make('Password123'),
        ]);

        // Attacher le rôle admin
        $admin->roles()->attach($adminRole->id);
    }
}