<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class TestAvatars extends Command
{
    protected $signature = 'test:avatars {user_id?}';
    protected $description = 'Teste l\'affichage des avatars pour debug';

    public function handle()
    {
        $userId = $this->argument('user_id') ?: 1;
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ Utilisateur ID {$userId} non trouvé");
            return;
        }
        
        $this->info("🧪 Test Avatar pour : {$user->name}");
        $this->line("📧 Email : {$user->email}");
        
        if (!$user->avatar) {
            $this->warn("⚠️  Aucun avatar défini - Initiales utilisées");
            $this->info("🎨 Initiales : " . strtoupper(substr($user->name, 0, 2)));
            return;
        }
        
        $this->info("🖼️  Avatar trouvé : {$user->avatar}");
        
        // Test si c'est une URL
        if (filter_var($user->avatar, FILTER_VALIDATE_URL)) {
            $this->info("🌐 Type : URL externe (OAuth)");
            
            // Test de connectivité
            $headers = @get_headers($user->avatar);
            if ($headers && strpos($headers[0], '200') !== false) {
                $this->info("✅ Avatar accessible");
            } else {
                $this->error("❌ Avatar non accessible - Fallback nécessaire");
            }
        } else {
            $this->info("💾 Type : Fichier local");
            $localPath = storage_path('app/public/' . $user->avatar);
            
            if (file_exists($localPath)) {
                $this->info("✅ Fichier local trouvé");
                $this->info("📁 Chemin : {$localPath}");
                $this->info("📊 Taille : " . number_format(filesize($localPath)) . " octets");
            } else {
                $this->error("❌ Fichier local non trouvé - Fallback nécessaire");
            }
        }
        
        // URL finale générée
        $finalUrl = filter_var($user->avatar, FILTER_VALIDATE_URL) 
            ? $user->avatar 
            : asset('storage/' . $user->avatar);
            
        $this->info("🔗 URL finale : {$finalUrl}");
        
        $this->info("✨ Test terminé !");
    }
}