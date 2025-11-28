<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\PushNotificationService;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:test 
                            {--user= : ID de l\'utilisateur spécifique}
                            {--all : Envoyer à tous les utilisateurs avec token FCM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer une notification push de test';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pushService = app(PushNotificationService::class);
        
        // Si un utilisateur spécifique est spécifié
        if ($userId = $this->option('user')) {
            $user = User::find($userId);
            
            if (!$user) {
                $this->error("❌ Utilisateur #{$userId} introuvable");
                return 1;
            }
            
            if (!$user->fcm_token) {
                $this->error("❌ L'utilisateur {$user->name} n'a pas de token FCM");
                return 1;
            }
            
            $this->info("📤 Envoi de la notification à {$user->name}...");
            
            $success = $pushService->sendToUser($user, [
                'title' => '🧪 Notification Test',
                'body' => "Bonjour {$user->name}! Ceci est un test de notification.",
                'icon' => '/images/icons/icon-192x192.png',
                'tag' => 'test-' . time()
            ], [
                'url' => '/',
                'type' => 'test'
            ]);
            
            if ($success) {
                $this->info("✅ Notification envoyée avec succès!");
                return 0;
            } else {
                $this->error("❌ Échec de l'envoi de la notification");
                return 1;
            }
        }
        
        // Envoyer à tous les utilisateurs
        if ($this->option('all')) {
            $users = User::whereNotNull('fcm_token')->get();
            
            if ($users->isEmpty()) {
                $this->error("❌ Aucun utilisateur avec token FCM trouvé");
                return 1;
            }
            
            $this->info("📢 Envoi de notifications à {$users->count()} utilisateurs...");
            
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            
            $results = ['success' => 0, 'failed' => 0];
            
            foreach ($users as $user) {
                $success = $pushService->sendToUser($user, [
                    'title' => '📢 Notification Test',
                    'body' => "Bonjour {$user->name}! Ceci est un test de notification VintApp.",
                    'icon' => '/images/icons/icon-192x192.png',
                    'tag' => 'broadcast-test-' . time(),
                    'requireInteraction' => true
                ], [
                    'url' => '/',
                    'type' => 'broadcast_test'
                ]);
                
                if ($success) {
                    $results['success']++;
                } else {
                    $results['failed']++;
                }
                
                $bar->advance();
                usleep(100000); // 100ms de pause entre chaque envoi
            }
            
            $bar->finish();
            $this->newLine(2);
            
            $this->info("✅ Notifications envoyées: {$results['success']}/{$users->count()}");
            if ($results['failed'] > 0) {
                $this->warn("⚠️  Échecs: {$results['failed']}");
            }
            
            return 0;
        }
        
        // Si aucune option, afficher l'aide
        $this->info("🔔 Test de notifications push");
        $this->newLine();
        $this->info("Options disponibles:");
        $this->line("  --user=ID   : Envoyer à un utilisateur spécifique");
        $this->line("  --all       : Envoyer à tous les utilisateurs avec token FCM");
        $this->newLine();
        $this->info("Exemples:");
        $this->line("  php artisan push:test --user=1");
        $this->line("  php artisan push:test --all");
        $this->newLine();
        
        // Afficher les utilisateurs disponibles
        $users = User::whereNotNull('fcm_token')
            ->select('id', 'name', 'email', 'device_type', 'browser')
            ->get();
            
        if ($users->isNotEmpty()) {
            $this->info("👥 Utilisateurs avec token FCM ({$users->count()}):");
            $this->table(
                ['ID', 'Nom', 'Email', 'Device', 'Browser'],
                $users->map(fn($u) => [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->device_type ?? 'N/A',
                    $u->browser ?? 'N/A'
                ])->toArray()
            );
        } else {
            $this->warn("⚠️  Aucun utilisateur avec token FCM enregistré");
        }
        
        return 0;
    }
}
