<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ExpertProfile;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class DesignateUserAsExpert extends Command
{
    protected $signature = 'admin:designate-expert {user_id} {--specialties=mode_luxe,electronique} {--level=senior}';
    protected $description = 'Désigner un utilisateur comme expert avec des spécialisations';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $specialties = explode(',', $this->option('specialties'));
        $level = $this->option('level');

        try {
            DB::beginTransaction();

            // Vérifier que l'utilisateur existe
            $user = User::find($userId);
            if (!$user) {
                $this->error("Utilisateur ID {$userId} non trouvé");
                return 1;
            }

            // Vérifier qu'il n'est pas déjà expert
            if ($user->expertProfile) {
                $this->error("L'utilisateur {$user->name} est déjà expert");
                return 1;
            }

            // Créer le profil expert
            $expertProfile = ExpertProfile::create([
                'user_id' => $user->id,
                'specialties' => $specialties,
                'certification_level' => $level,
                'bio' => "Expert désigné via commande artisan",
                'is_active' => true,
                'verification_count' => 0,
                'approval_rate' => 0,
            ]);

            // Assigner le rôle expert
            $expertRole = Role::where('slug', 'expert')->first();
            if ($expertRole && !$user->roles->contains($expertRole)) {
                $user->roles()->attach($expertRole);
            }

            DB::commit();

            $this->info("✅ {$user->name} a été désigné comme expert avec succès !");
            $this->line("📋 Spécialisations : " . implode(', ', $specialties));
            $this->line("🎯 Niveau : {$level}");
            $this->line("🆔 Expert Profile ID : {$expertProfile->id}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Erreur : " . $e->getMessage());
            return 1;
        }
    }
}