<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class CheckAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:admins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les comptes admin disponibles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VÉRIFICATION SYSTÈME RÔLES ===');
        
        // Vérifier si la table roles existe
        if (!\Illuminate\Support\Facades\Schema::hasTable('roles')) {
            $this->error('❌ Table roles introuvable');
            return 1;
        }
        
        $roles = \Illuminate\Support\Facades\DB::table('roles')->get(['id', 'name', 'slug']);
        $this->info('Rôles disponibles:');
        foreach ($roles as $role) {
            $this->info("- {$role->slug} ({$role->name})");
        }
        
        $this->newLine();
        
        // Vérifier s'il y a des utilisateurs avec le rôle admin
        $adminRole = \Illuminate\Support\Facades\DB::table('roles')->where('slug', 'admin')->first();
        
        if (!$adminRole) {
            $this->error('❌ Rôle admin introuvable');
            return 1;
        }
        
        $this->info("✅ Rôle admin trouvé: {$adminRole->name} (ID: {$adminRole->id})");
        
        // Chercher les utilisateurs avec le rôle admin
        if (\Illuminate\Support\Facades\Schema::hasTable('role_user')) {
            $adminUsers = \Illuminate\Support\Facades\DB::table('users')
                ->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->where('role_user.role_id', $adminRole->id)
                ->select('users.id', 'users.name', 'users.email')
                ->get();
            
            if ($adminUsers->isEmpty()) {
                $this->error('❌ Aucun utilisateur avec le rôle admin');
                $this->info('💡 Il faut assigner le rôle admin à un utilisateur');
            } else {
                $this->info("✅ {$adminUsers->count()} utilisateur(s) admin trouvé(s):");
                foreach ($adminUsers as $user) {
                    $this->info("- {$user->name} ({$user->email}) [ID: {$user->id}]");
                }
                
                $this->newLine();
                $this->info('💡 Connectez-vous avec un de ces comptes pour accéder à /admin/wallets/pending');
            }
        }
        
        return 0;
    }
}
