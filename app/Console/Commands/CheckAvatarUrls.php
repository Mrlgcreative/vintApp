<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Wallet;
use App\Models\User;

class CheckAvatarUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:avatar-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vérifier les avatar_url des utilisateurs pour détecter des URLs problématiques';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== VÉRIFICATION AVATAR_URL ===');
        
        // Vérifier les utilisateurs avec des wallets pending
        $walletsWithUsers = Wallet::with('user')
            ->where('type', 'pending')
            ->whereHas('user')
            ->take(20)
            ->get();
            
        if ($walletsWithUsers->isEmpty()) {
            $this->info('Aucun wallet pending avec utilisateur trouvé.');
        } else {
            $this->info("Vérification de {$walletsWithUsers->count()} wallet(s) pending:");
            
            foreach ($walletsWithUsers as $wallet) {
                if ($wallet->user) {
                    $avatar = $wallet->user->avatar_url ?? 'NULL';
                    $this->line("User ID: {$wallet->user->id} - Avatar: {$avatar}");
                    
                    // Détecter les URLs problématiques
                    if ($avatar && (strpos($avatar, 'admin/wallets') !== false || strpos($avatar, 'pending') !== false)) {
                        $this->error("⚠️ PROBLÈME DÉTECTÉ: Avatar pointe vers une route admin!");
                        $this->line("   User: {$wallet->user->name} ({$wallet->user->email})");
                    }
                }
            }
        }
        
        $this->newLine();
        
        // Vérifier tous les utilisateurs avec des avatar_url suspects
        $this->info('=== RECHERCHE GLOBALE D\'AVATAR_URL SUSPECTS ===');
        
        $suspectUsers = User::where('avatar_url', 'like', '%admin%')
            ->orWhere('avatar_url', 'like', '%pending%')
            ->orWhere('avatar_url', 'like', '%wallets%')
            ->get(['id', 'name', 'email', 'avatar_url']);
            
        if ($suspectUsers->isEmpty()) {
            $this->info('✅ Aucun avatar_url suspect trouvé.');
        } else {
            $this->error("❌ {$suspectUsers->count()} avatar_url suspect(s) trouvé(s):");
            foreach ($suspectUsers as $user) {
                $this->line("User ID: {$user->id} - {$user->name} ({$user->email})");
                $this->line("   Avatar: {$user->avatar_url}");
                $this->newLine();
            }
        }
        
        return 0;
    }
}
