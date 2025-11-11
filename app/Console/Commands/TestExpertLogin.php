<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ProductAuthenticityCheck;

class TestExpertLogin extends Command
{
    protected $signature = 'expert:test-login';
    protected $description = 'Teste la connexion expert et affiche les informations du dashboard';

    public function handle()
    {
        // Trouver l'expert Marie Dubois
        $expert = User::find(35);
        
        if (!$expert) {
            $this->error("Expert ID 35 non trouvé");
            return 1;
        }

        $this->info("=== TEST CONNEXION EXPERT ===");
        $this->line("Expert: {$expert->name} ({$expert->email})");
        
        // Vérifier qu'il a le rôle expert
        $hasExpertRole = $expert->roles()->where('slug', 'expert')->exists();
        $this->line("A le rôle expert: " . ($hasExpertRole ? "✓ OUI" : "✗ NON"));
        
        // Récupérer ses vérifications assignées
        $pendingChecks = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->where('status', ProductAuthenticityCheck::STATUS_EXPERT_REVIEW)
            ->with(['item', 'vendor'])
            ->get();
            
        $this->info("\n=== VÉRIFICATIONS ASSIGNÉES ===");
        $this->line("Nombre de vérifications en attente: {$pendingChecks->count()}");
        
        foreach ($pendingChecks as $check) {
            $this->line("─────────────────────────");
            $this->line("ID: {$check->id}");
            $this->line("Statut: {$check->status}");
            $this->line("Produit: " . ($check->item->name ?? 'Nom non défini'));
            $this->line("Prix: " . ($check->item->price ?? 'Prix non défini') . " " . ($check->item->currency ?? 'USD'));
            $this->line("Vendeur: " . ($check->vendor->name ?? 'Vendeur non défini'));
            $this->line("Images disponibles: " . (empty($check->item->images) ? "Non" : "Oui (" . count($check->item->images) . ")"));
            $this->line("Créé le: {$check->created_at}");
            $this->line("Assigné le: {$check->expert_assigned_at}");
        }
        
        // Calculer les statistiques
        $totalChecks = ProductAuthenticityCheck::where('expert_id', $expert->id)->count();
        $completedChecks = ProductAuthenticityCheck::where('expert_id', $expert->id)
            ->whereIn('status', [ProductAuthenticityCheck::STATUS_EXPERT_APPROVED, ProductAuthenticityCheck::STATUS_EXPERT_REJECTED])
            ->count();
            
        $this->info("\n=== STATISTIQUES EXPERT ===");
        $this->line("Total vérifications assignées: {$totalChecks}");
        $this->line("Vérifications complétées: {$completedChecks}");
        $this->line("En cours: {$pendingChecks->count()}");
        
        $this->info("\nTest terminé ! L'expert peut se connecter avec:");
        $this->line("Email: {$expert->email}");
        $this->line("URL Dashboard: " . url('/expert'));

        return 0;
    }
}