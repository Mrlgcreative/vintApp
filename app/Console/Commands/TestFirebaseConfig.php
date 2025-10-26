<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FirebaseService;

class TestFirebaseConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'firebase:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Firebase configuration and connectivity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔥 Testing Firebase Configuration...');
        $this->newLine();

        try {
            $service = new FirebaseService();
            $config = $service->getConfigInfo();

            // Afficher les informations de configuration
            $this->line('📋 Configuration Status:');
            $this->table(
                ['Setting', 'Value', 'Status'],
                [
                    ['Project ID', $config['project_id'] ?? 'Not set', $config['project_id'] ? '✅' : '❌'],
                    ['Credentials Path', $config['credentials_path'] ?? 'Not set', $config['credentials_path'] ? '✅' : '❌'],
                    ['Credentials File Exists', $config['credentials_exists'] ? 'Yes' : 'No', $config['credentials_exists'] ? '✅' : '❌'],
                    ['Firebase Configured', $config['is_configured'] ? 'Yes' : 'No', $config['is_configured'] ? '✅' : '❌'],
                ]
            );

            $this->newLine();

            if ($config['is_configured']) {
                $this->info('✅ Firebase is properly configured!');
                
                // Test des services Firebase
                $this->line('🧪 Testing Firebase Services:');
                
                try {
                    $factory = app('firebase.factory');
                    $this->line('   ✅ Firebase Factory: OK');
                } catch (\Exception $e) {
                    $this->error('   ❌ Firebase Factory: ' . $e->getMessage());
                }

                try {
                    $auth = app('firebase.auth');
                    $this->line('   ✅ Firebase Auth: OK');
                } catch (\Exception $e) {
                    $this->error('   ❌ Firebase Auth: ' . $e->getMessage());
                }

                try {
                    $messaging = app('firebase.messaging');
                    $this->line('   ✅ Firebase Messaging: OK');
                } catch (\Exception $e) {
                    $this->error('   ❌ Firebase Messaging: ' . $e->getMessage());
                }

            } else {
                $this->error('❌ Firebase is not properly configured!');
                $this->newLine();
                $this->warn('📝 To fix this:');
                
                if (!$config['project_id']) {
                    $this->line('1. Set FIREBASE_PROJECT_ID in your .env file');
                }
                
                if (!$config['credentials_path']) {
                    $this->line('2. Set FIREBASE_CREDENTIALS path in your .env file');
                }
                
                if (!$config['credentials_exists']) {
                    $this->line('3. Download serviceAccountKey.json from Firebase Console');
                    $this->line('   Place it in: storage/firebase/serviceAccountKey.json');
                }
            }

            $this->newLine();
            $this->line('🌐 Web Configuration:');
            
            $webConfig = $config['web_config'];
            foreach ($webConfig as $key => $value) {
                $status = !empty($value) ? '✅' : '❌';
                $displayValue = !empty($value) ? (strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value) : 'Not set';
                $this->line("   {$key}: {$displayValue} {$status}");
            }

        } catch (\Exception $e) {
            $this->error('💥 Error testing Firebase: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
