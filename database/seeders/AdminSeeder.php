<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles s'ils n'existent pas
        $adminRole = Role::firstOrCreate([
            'slug' => 'admin'
        ], [
            'name' => 'Administrateur',
            'description' => 'Administrateur du système'
        ]);

        $userRole = Role::firstOrCreate([
            'slug' => 'user'
        ], [
            'name' => 'Utilisateur',
            'description' => 'Utilisateur standard'
        ]);

        // Créer l'utilisateur admin s'il n'existe pas
        $admin = User::firstOrCreate([
            'email' => 'admin@vintapp.com'
        ], [
            'name' => 'Administrateur',
            'password' => Hash::make('Password123'),
            'phone' => '+243123456789',
            'address' => 'Kinshasa, RDC',
            'bio' => 'Compte administrateur système',
            'location' => 'Kinshasa',
            'newsletter_subscribed' => false,
            'last_seen' => now(),
        ]);

        // Assigner le rôle admin
        if (!$admin->hasRole('admin')) {
            $admin->roles()->attach($adminRole);
        }

        $this->command->info('Utilisateur admin créé avec succès !');
        $this->command->info('Email: admin@vintapp.com');
        $this->command->info('Mot de passe: Password123');
    }
}
