<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestGoogleAuth extends Command
{
    protected $signature = 'firebase:test-google-auth';
    protected $description = 'Tester la configuration Google OAuth avec Firebase';

    public function handle()
    {
        $this->info('🔍 Test de la configuration Google OAuth...');
        
        // Vérifier la configuration Firebase
        $firebaseConfig = config('firebase.web_config');
        
        $this->info('');
        $this->info('📋 Configuration Firebase Web:');
        $this->info("   API Key: " . ($firebaseConfig['apiKey'] ?? 'MANQUANT'));
        $this->info("   Auth Domain: " . ($firebaseConfig['authDomain'] ?? 'MANQUANT'));
        $this->info("   Project ID: " . ($firebaseConfig['projectId'] ?? 'MANQUANT'));
        $this->info("   App ID: " . ($firebaseConfig['appId'] ?? 'MANQUANT'));
        
        // Vérifier si les valeurs par défaut sont encore présentes
        $issues = [];
        
        if (!isset($firebaseConfig['apiKey']) || str_contains($firebaseConfig['apiKey'], 'YOUR_API_KEY')) {
            $issues[] = 'API Key manquante ou par défaut';
        }
        
        if (!isset($firebaseConfig['appId']) || str_contains($firebaseConfig['appId'], 'YOUR_APP_ID')) {
            $issues[] = 'App ID manquante ou par défaut';
        }
        
        if (!isset($firebaseConfig['authDomain']) || empty($firebaseConfig['authDomain'])) {
            $issues[] = 'Auth Domain manquant';
        }
        
        if (empty($issues)) {
            $this->info('');
            $this->info('✅ Configuration Firebase OK');
        } else {
            $this->error('');
            $this->error('❌ Problèmes détectés:');
            foreach ($issues as $issue) {
                $this->error("   - {$issue}");
            }
        }
        
        $this->info('');
        $this->info('🎯 Actions recommandées:');
        $this->info('1. Vérifiez Firebase Console > Authentication > Sign-in method');
        $this->info('2. Assurez-vous que Google est activé comme provider');
        $this->info('3. Vérifiez les domaines autorisés (localhost, 127.0.0.1)');
        $this->info('4. Testez avec la console navigateur (F12) pour voir les logs détaillés');
        
        $this->info('');
        $this->info('🔗 Liens utiles:');
        $this->info('   Firebase Console: https://console.firebase.google.com/project/' . ($firebaseConfig['projectId'] ?? 'VOTRE_PROJECT_ID'));
        $this->info('   Google Cloud Console: https://console.cloud.google.com/');
        
        return 0;
    }
}