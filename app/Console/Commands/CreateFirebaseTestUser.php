<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Kreait\Firebase\Factory;
use Exception;

class CreateFirebaseTestUser extends Command
{
    protected $signature = 'firebase:create-test-user {email=test@example.com} {password=password123}';
    protected $description = 'Créer un utilisateur test dans Firebase Auth';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        try {
            $firebase = app('firebase');
            $auth = $firebase->createAuth();

            // Vérifier si l'utilisateur existe déjà
            try {
                $existingUser = $auth->getUserByEmail($email);
                $this->info("✅ Utilisateur existe déjà:");
                $this->info("   UID: " . $existingUser->uid);
                $this->info("   Email: " . $existingUser->email);
                $this->info("   Nom: " . ($existingUser->displayName ?? 'Non défini'));
                $this->info("   Email vérifié: " . ($existingUser->emailVerified ? 'Oui' : 'Non'));
                return;
            } catch (Exception $e) {
                // L'utilisateur n'existe pas, on va le créer
            }

            // Créer l'utilisateur
            $this->info("📝 Création de l'utilisateur test...");
            
            $userProperties = [
                'email' => $email,
                'password' => $password,
                'displayName' => 'Test User',
                'emailVerified' => true
            ];
            
            $user = $auth->createUser($userProperties);
            
            $this->info("✅ Utilisateur créé avec succès !");
            $this->info("   UID: " . $user->uid);
            $this->info("   Email: " . $user->email);
            $this->info("   Nom: " . $user->displayName);
            
            $this->info("");
            $this->info("🧪 Vous pouvez maintenant tester la connexion avec:");
            $this->info("   Email: {$email}");
            $this->info("   Mot de passe: {$password}");
            
        } catch (Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'email-already-in-use')) {
                $this->info("ℹ️ L'utilisateur existe peut-être déjà. Essayez de vous connecter.");
            }
        }
    }
}