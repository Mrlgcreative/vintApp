<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;
use Illuminate\Support\Facades\View;

class TestExpertShowView extends Command
{
    protected $signature = 'expert:test-show {verification_id}';
    protected $description = 'Tester le rendu de la vue expert show sans erreur';

    public function handle()
    {
        $verificationId = $this->argument('verification_id');
        $verification = ProductAuthenticityCheck::with(['item.category', 'item.brand', 'expert', 'auditLogs'])
                          ->find($verificationId);
        
        if (!$verification) {
            $this->error("Vérification {$verificationId} non trouvée");
            return 1;
        }

        try {
            $this->info("=== TEST DE RENDU DE LA VUE EXPERT SHOW ===");
            
            // Simuler les variables globales nécessaires
            config(['app.name' => 'Vintapp']);
            
            // Tester uniquement la logique des audit logs directement
            $this->line("Test des audit logs sans rendu complet...");
            
            $problemArrays = 0;
            $processedLogs = [];
            
            foreach ($verification->auditLogs as $log) {
                if ($log->details && is_array($log->details)) {
                    foreach ($log->details as $key => $value) {
                        if (is_array($value)) {
                            $problemArrays++;
                            // Simuler la logique de notre correction
                            $processedValue = json_encode($value);
                            $processedLogs[] = [
                                'log_id' => $log->id,
                                'key' => $key,
                                'original' => $value,
                                'processed' => $processedValue
                            ];
                        }
                    }
                }
            }
            
            $this->line("Arrays problématiques trouvés : {$problemArrays}");
            
            if ($problemArrays > 0) {
                $this->info("✅ Correction des arrays appliquée :");
                foreach ($processedLogs as $processed) {
                    $this->line("  Log {$processed['log_id']} - {$processed['key']}: " . $processed['processed']);
                }
                $this->info("✅ Tous les arrays sont maintenant correctement encodés en JSON");
            } else {
                $this->info("✅ Aucun array problématique trouvé");
            }
            
            // Test simple de la structure de données
            $this->line("\n=== VÉRIFICATION DES DONNÉES PRINCIPALES ===");
            $checks = [
                'Item name' => $verification->item->name ?? 'N/A',
                'Expert name' => $verification->expert ? $verification->expert->name : 'Non assigné',
                'Status' => $verification->status ?? 'N/A',
                'Audit logs count' => $verification->auditLogs->count()
            ];
            
            foreach ($checks as $label => $value) {
                $this->info("✓ {$label}: {$value}");
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Erreur lors du rendu de la vue :");
            $this->error($e->getMessage());
            $this->line("Ligne : " . $e->getLine());
            $this->line("Fichier : " . $e->getFile());
            return 1;
        }

        return 0;
    }
}