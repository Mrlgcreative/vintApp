<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ExpertProfile;

class ListExpertCandidates extends Command
{
    protected $signature = 'admin:list-expert-candidates {--limit=10}';
    protected $description = 'Lister les utilisateurs qui peuvent devenir experts';

    public function handle()
    {
        $limit = $this->option('limit');

        $this->info("=== CANDIDATS POUR DEVENIR EXPERTS ===\n");

        // Utilisateurs qui ne sont pas encore experts
        $candidates = User::whereDoesntHave('expertProfile')
            ->whereNotNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        if ($candidates->isEmpty()) {
            $this->warn("Aucun candidat trouvé.");
            return 0;
        }

        $this->line("Utilisateurs éligibles (limité à {$limit}) :");
        $this->newLine();

        foreach ($candidates as $user) {
            $this->line("🔹 ID: {$user->id}");
            $this->line("   Nom: {$user->name}");
            $this->line("   Email: {$user->email}");
            $this->line("   Inscrit le: " . $user->created_at->format('d/m/Y H:i'));
            $this->line("   Dernière activité: " . ($user->last_seen ? $user->last_seen->diffForHumans() : 'Jamais'));
            $this->newLine();
        }

        // Statistiques actuelles
        $totalExperts = ExpertProfile::count();
        $activeExperts = ExpertProfile::where('is_active', true)->count();

        $this->info("📊 STATISTIQUES ACTUELLES :");
        $this->line("   Total experts: {$totalExperts}");
        $this->line("   Experts actifs: {$activeExperts}");
        $this->line("   Candidats potentiels: " . User::whereDoesntHave('expertProfile')->whereNotNull('email_verified_at')->count());

        $this->newLine();
        $this->comment("💡 Pour désigner un expert, utilisez :");
        $this->comment("   php artisan admin:designate-expert {user_id} --specialties=mode_luxe,electronique --level=senior");

        return 0;
    }
}