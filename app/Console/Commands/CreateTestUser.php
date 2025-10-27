<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateTestUser extends Command
{
    protected $signature = 'create:test-user';
    protected $description = 'Crée un utilisateur de test sans privilèges admin';

    public function handle()
    {
        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('email', 'test@example.com')->first();
        
        if ($existingUser) {
            $this->info("👤 Utilisateur test existe déjà :");
            $this->line("   - Nom : {$existingUser->name}");
            $this->line("   - Email : {$existingUser->email}");
            $this->line("   - ID : {$existingUser->id}");
            return;
        }

        $this->info("🔨 Création de l'utilisateur test...");

        // Créer l'utilisateur
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(), // Pré-vérifier l'email
        ]);

        // Attribuer le rôle utilisateur (ID 2)
        $user->roles()->attach(2);

        $this->info("✅ Utilisateur test créé avec succès !");
        $this->line("   📧 Email : test@example.com");
        $this->line("   🔑 Mot de passe : password");
        $this->line("   🆔 ID : {$user->id}");
        $this->line("   🏷️  Rôle : Utilisateur (non-admin)");
        
        $this->info("");
        $this->info("🧪 Test de connexion :");
        $this->line("1. Déconnectez-vous si connecté");
        $this->line("2. Connectez-vous avec test@example.com / password");
        $this->line("3. Vérifiez que vous êtes redirigé vers /dashboard (pas /admin)");
    }
}