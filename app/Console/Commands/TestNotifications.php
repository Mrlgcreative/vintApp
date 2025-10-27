<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use App\Models\User;

class TestNotifications extends Command
{
    protected $signature = 'notifications:test {user_id? : ID de l\'utilisateur}';
    protected $description = 'Créer des notifications de test';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $userId = $this->argument('user_id');
        
        if (!$userId) {
            $users = User::limit(5)->get();
            $this->info('👥 Utilisateurs disponibles :');
            foreach ($users as $user) {
                $this->line("   {$user->id} - {$user->name} ({$user->email})");
            }
            $userId = $this->ask('ID de l\'utilisateur pour les tests');
        }

        $user = User::find($userId);
        if (!$user) {
            $this->error('❌ Utilisateur non trouvé');
            return 1;
        }

        $this->info("🧪 Création de notifications de test pour {$user->name}...");

        // Test 1: Notification de message
        $this->notificationService->createMessageNotification(
            1, // Sender ID (admin)
            $user->id,
            'Bonjour ! Voici un message de test pour vérifier les notifications.'
        );
        $this->info('✅ Notification message créée');

        // Test 2: Notification de commande
        $this->notificationService->createOrderNotification(
            $user->id,
            1, // Seller ID (admin)
            'iPhone 13 Pro Max 256GB'
        );
        $this->info('✅ Notification commande créée');

        // Test 3: Notification de réduction
        $this->notificationService->createDiscountNotification(
            1, // Seller ID
            $user->id,
            'MacBook Air M1',
            15,
            '850€'
        );
        $this->info('✅ Notification réduction créée');

        // Statistiques
        $count = $this->notificationService->getUnreadCount($user->id);
        $this->info("📊 Total notifications non lues: {$count}");

        $this->info('');
        $this->info('🎯 Pour tester :');
        $this->info('1. Connectez-vous avec ce compte');
        $this->info('2. Cliquez sur l\'icône de notifications');
        $this->info('3. Vérifiez que les notifications apparaissent');
        $this->info('4. Testez sur mobile et desktop');

        return 0;
    }
}