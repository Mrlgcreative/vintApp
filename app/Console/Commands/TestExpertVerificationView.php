<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;
use App\Models\User;

class TestExpertVerificationView extends Command
{
    protected $signature = 'expert:test-verification {check_id}';
    protected $description = 'Teste l\'affichage d\'une vérification spécifique';

    public function handle()
    {
        $checkId = $this->argument('check_id');
        
        // Récupérer la vérification avec toutes les relations
        $check = ProductAuthenticityCheck::with(['item', 'vendor', 'expert', 'verificationImages'])
            ->find($checkId);
            
        if (!$check) {
            $this->error("Vérification ID {$checkId} non trouvée");
            return 1;
        }

        $this->info("=== TEST AFFICHAGE VÉRIFICATION ===");
        $this->line("ID: {$check->id}");
        $this->line("Statut: {$check->status}");
        
        $this->info("\n=== PRODUIT ===");
        $this->line("Nom: " . ($check->item->name ?? 'Non défini'));
        $this->line("Prix: " . ($check->item->price ?? 'Non défini') . " " . ($check->item->currency ?? 'USD'));
        $this->line("Description: " . substr($check->item->description ?? 'Non définie', 0, 100) . "...");
        
        $this->info("\n=== IMAGES PRODUIT ===");
        if (empty($check->item->images)) {
            $this->warn("Aucune image disponible");
        } else {
            $this->line("Nombre d'images: " . count($check->item->images));
            foreach ($check->item->images as $index => $image) {
                $this->line("  Image {$index}: {$image}");
            }
        }
        
        $this->info("\n=== IMAGES DE VÉRIFICATION ===");
        if ($check->verificationImages->isEmpty()) {
            $this->warn("Aucune image de vérification");
        } else {
            $this->line("Nombre d'images de vérification: " . $check->verificationImages->count());
            foreach ($check->verificationImages as $index => $image) {
                $this->line("  Image vérif {$index}: {$image->image_path}");
            }
        }
        
        $this->info("\n=== VENDEUR ===");
        $this->line("Nom: " . ($check->vendor->name ?? 'Non défini'));
        $this->line("Email: " . ($check->vendor->email ?? 'Non défini'));
        
        $this->info("\n=== EXPERT ===");
        if ($check->expert) {
            $this->line("Nom: {$check->expert->name}");
            $this->line("Email: {$check->expert->email}");
            $this->line("Assigné le: {$check->expert_assigned_at}");
        } else {
            $this->warn("Aucun expert assigné");
        }
        
        $this->info("\n=== ANALYSE IA ===");
        if ($check->ai_analysis_result) {
            $this->line("Score de confiance: " . ($check->ai_confidence_score ?? 'Non défini'));
            $analysis = is_string($check->ai_analysis_result) ? 
                json_decode($check->ai_analysis_result, true) : 
                $check->ai_analysis_result;
            
            if ($analysis && is_array($analysis)) {
                $this->line("Probabilité authenticité: " . ($analysis['authenticity_probability'] ?? 'Non défini'));
                if (isset($analysis['detected_features'])) {
                    $this->line("Caractéristiques détectées: " . implode(', ', $analysis['detected_features']));
                }
            }
        } else {
            $this->warn("Aucune analyse IA disponible");
        }

        $this->info("\nURL pour voir cette vérification:");
        $this->line(url("/expert/verifications/{$check->id}"));

        return 0;
    }
}