<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TestAdminRedirect extends Command
{
    protected $signature = 'test:admin-redirect {user_id?}';
    protected $description = 'Teste la redirection admin';

    public function handle()
    {
        $userId = $this->argument('user_id') ?: 1;
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("❌ Utilisateur ID {$userId} non trouvé");
            return;
        }
        
        $this->info("🧪 Test Redirection Admin pour : {$user->name}");
        $this->line("📧 Email : {$user->email}");
        
        // Test 1: Vérification avec la méthode hasRole (si elle fonctionne)
        try {
            $hasRoleMethod = $user->hasRole('admin');
            $this->info("📋 Méthode hasRole('admin') : " . ($hasRoleMethod ? 'OUI' : 'NON'));
        } catch (\Exception $e) {
            $this->warn("⚠️  Méthode hasRole() échoue : " . $e->getMessage());
        }
        
        // Test 2: Vérification directe en base
        $isAdminDB = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->where('roles.slug', 'admin')
            ->exists();
            
        $this->info("📊 Vérification directe DB : " . ($isAdminDB ? 'OUI' : 'NON'));
        
        // Test 3: Voir tous les rôles de l'utilisateur
        $userRoles = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('role_user.user_id', $user->id)
            ->select('roles.name', 'roles.slug')
            ->get();
            
        $this->info("🏷️  Rôles attribués :");
        foreach ($userRoles as $role) {
            $this->line("   - {$role->name} ({$role->slug})");
        }
        
        if ($userRoles->isEmpty()) {
            $this->warn("⚠️  Aucun rôle attribué à cet utilisateur !");
        }
        
        // Conclusion
        if ($isAdminDB) {
            $this->info("✅ L'utilisateur SERA redirigé vers admin.dashboard");
        } else {
            $this->info("ℹ️  L'utilisateur sera redirigé vers dashboard (utilisateur normal)");
        }
        
        $this->info("🔗 Route admin.dashboard : " . route('admin.dashboard'));
        $this->info("🔗 Route dashboard : " . route('dashboard'));
    }
}