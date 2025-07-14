<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Message;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Utilisateurs de test
        $user1 = User::firstOrCreate(
            ['email' => 'alice@test.com'],
            [
                'name' => 'Alice Martin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $user2 = User::firstOrCreate(
            ['email' => 'bob@test.com'],
            [
                'name' => 'Bob Dupont',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $user3 = User::firstOrCreate(
            ['email' => 'claire@test.com'],
            [
                'name' => 'Claire Dubois',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Messages de test (créés seulement s'ils n'existent pas déjà)
        $messages = [
            // Alice <-> Bob
            ['sender_id' => $user1->id, 'receiver_id' => $user2->id, 'content' => 'Bonjour Bob ! Comment allez-vous ?'],
            ['sender_id' => $user2->id, 'receiver_id' => $user1->id, 'content' => 'Très bien, merci Alice ! Et vous ?'],
            ['sender_id' => $user1->id, 'receiver_id' => $user2->id, 'content' => 'Parfait ! Avez-vous vu le nouvel article sur VintApp ?'],
            ['sender_id' => $user2->id, 'receiver_id' => $user1->id, 'content' => 'Oui, il est très intéressant !'],
            // Alice <-> Claire
            ['sender_id' => $user1->id, 'receiver_id' => $user3->id, 'content' => 'Salut Claire !'],
            ['sender_id' => $user3->id, 'receiver_id' => $user1->id, 'content' => 'Coucou Alice ! Comment ça va ?'],
            ['sender_id' => $user1->id, 'receiver_id' => $user3->id, 'content' => 'Super ! Tu veux qu\'on se retrouve ?'],
            // Bob <-> Claire
            ['sender_id' => $user2->id, 'receiver_id' => $user3->id, 'content' => 'Bonjour Claire !'],
            ['sender_id' => $user3->id, 'receiver_id' => $user2->id, 'content' => 'Bonjour Bob !'],
        ];

        foreach ($messages as $data) {
            Message::firstOrCreate(
                [
                    'sender_id' => $data['sender_id'],
                    'receiver_id' => $data['receiver_id'],
                    'content' => $data['content'],
                ],
                [
                    'type' => 'general',
                    'is_read' => false,
                ]
            );
        }

        $this->command->info('Utilisateurs et messages de test prêts !');
        $this->command->info('Connectez-vous avec :');
        $this->command->info('- alice@test.com / password');
        $this->command->info('- bob@test.com / password');
        $this->command->info('- claire@test.com / password');
    }
}
