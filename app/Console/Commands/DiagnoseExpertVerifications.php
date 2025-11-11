<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;
use App\Models\User;

class DiagnoseExpertVerifications extends Command
{
    protected $signature = 'expert:diagnose';
    protected $description = 'Diagnostique les vérifications d\'authenticité pour les experts';

    public function handle()
    {
        $this->info("=== DIAGNOSTIC VÉRIFICATIONS EXPERT ===");

        // 1. Lister toutes les vérifications
        $this->info("1. Toutes les vérifications:");
        $checks = ProductAuthenticityCheck::all(['id', 'status', 'expert_id', 'user_id', 'created_at']);
        
        if ($checks->isEmpty()) {
            $this->warn("  Aucune vérification trouvée");
        } else {
            foreach ($checks as $check) {
                $this->line("  ID: {$check->id} | Statut: {$check->status} | Expert ID: {$check->expert_id} | User: {$check->user_id} | Créé: {$check->created_at}");
            }
        }

        // 2. Lister les experts
        $this->info("\n2. Utilisateurs experts:");
        $experts = User::whereHas('roles', function($q) { 
            $q->where('slug', 'expert'); 
        })->get(['id', 'name', 'email']);
        
        if ($experts->isEmpty()) {
            $this->warn("  Aucun expert trouvé");
        } else {
            foreach ($experts as $expert) {
                $this->line("  ID: {$expert->id} | Nom: {$expert->name} | Email: {$expert->email}");
            }
        }

        // 3. Vérifications avec statut expert_review
        $this->info("\n3. Vérifications en attente expert:");
        $expertReviews = ProductAuthenticityCheck::where('status', 'expert_review')->get(['id', 'expert_id', 'user_id']);
        
        if ($expertReviews->isEmpty()) {
            $this->warn("  Aucune vérification en attente expert");
        } else {
            foreach ($expertReviews as $review) {
                $this->line("  ID: {$review->id} | Expert ID: {$review->expert_id} | User: {$review->user_id}");
            }
        }

        // 4. Vérifier les constantes de statut
        $this->info("\n4. Statuts disponibles dans le modèle:");
        $reflection = new \ReflectionClass(ProductAuthenticityCheck::class);
        $constants = $reflection->getConstants();
        foreach ($constants as $name => $value) {
            if (str_contains($name, 'STATUS')) {
                $this->line("  {$name}: {$value}");
            }
        }

        // 5. Compter par statut
        $this->info("\n5. Nombre de vérifications par statut:");
        $statusCounts = ProductAuthenticityCheck::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        if ($statusCounts->isEmpty()) {
            $this->warn("  Aucun statut trouvé");
        } else {
            foreach ($statusCounts as $statusCount) {
                $this->line("  {$statusCount->status}: {$statusCount->count}");
            }
        }

        return 0;
    }
}