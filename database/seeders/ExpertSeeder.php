<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ExpertProfile;

class ExpertSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Vérifier si le rôle expert existe
        $expertRole = DB::table('roles')->where('slug', 'expert')->first();
        
        if (!$expertRole) {
            // Créer le rôle expert s'il n'existe pas
            $expertRoleId = DB::table('roles')->insertGetId([
                'name' => 'Expert',
                'slug' => 'expert',
                'description' => 'Expert en vérification d\'authenticité',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $expertRoleId = $expertRole->id;
        }

        // Créer des experts de test
        $experts = [
            [
                'name' => 'Marie Dubois',
                'email' => 'marie.expert@vintapp.com',
                'specialties' => ['mode_luxe', 'bijoux'],
                'certification_level' => 'gold',
                'bio' => 'Expert en mode de luxe avec 15 ans d\'expérience chez Louis Vuitton et Hermès.',
                'credentials' => 'Certification gemologue, Ex-authenticatrice chez Vestiaire Collective'
            ],
            [
                'name' => 'Jean-Pierre Martin',
                'email' => 'jp.expert@vintapp.com',
                'specialties' => ['montres', 'electronique'],
                'certification_level' => 'silver',
                'bio' => 'Horloger certifié et expert en produits électroniques haut de gamme.',
                'credentials' => 'Formation horlogerie suisse, 10 ans chez Rolex'
            ],
            [
                'name' => 'Sophie Chen',
                'email' => 'sophie.expert@vintapp.com',
                'specialties' => ['sacs_maroquinerie', 'chaussures'],
                'certification_level' => 'bronze',
                'bio' => 'Spécialiste en maroquinerie et accessoires de mode.',
                'credentials' => 'Formation cuir et maroquinerie, Ex-acheteuse chez Galeries Lafayette'
            ]
        ];

        foreach ($experts as $expertData) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $expertData['name'],
                'email' => $expertData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // Assigner le rôle expert
            DB::table('role_user')->insert([
                'user_id' => $user->id,
                'role_id' => $expertRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Créer le profil expert
            ExpertProfile::create([
                'user_id' => $user->id,
                'specialties' => $expertData['specialties'],
                'verification_count' => rand(10, 100),
                'approval_rate' => rand(85, 98) + (rand(0, 99) / 100),
                'certification_level' => $expertData['certification_level'],
                'is_active' => true,
                'commission_rate' => 5.00,
                'bio' => $expertData['bio'],
                'credentials' => $expertData['credentials'],
            ]);
        }

        $this->command->info('✅ Experts créés avec succès!');
        $this->command->line('📧 Emails: marie.expert@vintapp.com, jp.expert@vintapp.com, sophie.expert@vintapp.com');
        $this->command->line('🔑 Mot de passe: password123');
    }
}
