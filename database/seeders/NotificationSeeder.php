<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 2) {
            $this->command->info('Pas assez d\'utilisateurs pour créer des notifications de test.');
            return;
        }

        // Créer des notifications de test pour chaque utilisateur
        foreach ($users as $user) {
            // Notification de nouveau message
            Notification::create([
                'user_id' => $user->id,
                'type' => 'new_message',
                'title' => 'Nouveau message',
                'message' => 'John Doe vous a envoyé un message',
                'data' => [
                    'sender_id' => $users->where('id', '!=', $user->id)->first()->id,
                    'sender_name' => 'John Doe',
                    'message_preview' => 'Bonjour ! J\'ai une question concernant votre article...',
                    'conversation_id' => $users->where('id', '!=', $user->id)->first()->id,
                ],
                'read_at' => null,
            ]);

            // Notification de nouvelle commande
            Notification::create([
                'user_id' => $user->id,
                'type' => 'new_order',
                'title' => 'Nouvelle commande',
                'message' => 'Jane Smith a commandé votre article "iPhone 12"',
                'data' => [
                    'buyer_id' => $users->where('id', '!=', $user->id)->first()->id,
                    'buyer_name' => 'Jane Smith',
                    'item_name' => 'iPhone 12',
                ],
                'read_at' => null,
            ]);

            // Notification d'article favori
            Notification::create([
                'user_id' => $user->id,
                'type' => 'item_favorited',
                'title' => 'Article ajouté aux favoris',
                'message' => 'Vous avez ajouté "Nike Air Max" à vos favoris',
                'data' => [
                    'item_name' => 'Nike Air Max',
                ],
                'read_at' => null,
            ]);
        }

        $this->command->info('Notifications de test créées avec succès !');
    }
} 