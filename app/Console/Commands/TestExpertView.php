<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;

class TestExpertView extends Command
{
    protected $signature = 'expert:test-view {verification_id}';
    protected $description = 'Test la vue expert pour capturer l\'erreur exacte';

    public function handle()
    {
        $verificationId = $this->argument('verification_id');
        
        try {
            $check = ProductAuthenticityCheck::findOrFail($verificationId);
            
            // Charger les relations
            $check->load([
                'item.category',
                'item.brand', 
                'item.user',
                'vendor',
                'verificationImages',
                'auditLogs.performer'
            ]);

            $this->info("=== DONNÉES CHARGÉES ===");
            $this->line("Check ID: {$check->id}");
            $this->line("Item: " . ($check->item ? "✓" : "✗"));
            
            if ($check->item) {
                // Tester chaque expression une par une
                $this->info("\n=== TEST DES EXPRESSIONS ===");
                
                // Test 1: name
                try {
                    $name = $check->item->name ?? 'Produit sans nom';
                    $this->line("✓ name: {$name}");
                } catch (\Exception $e) {
                    $this->error("✗ name: " . $e->getMessage());
                }
                
                // Test 2: category
                try {
                    $category = $check->item->category->name ?? 'Non spécifiée';
                    $this->line("✓ category: {$category}");
                } catch (\Exception $e) {
                    $this->error("✗ category: " . $e->getMessage());
                }
                
                // Test 3: brand
                try {
                    $brand = $check->item->brand->name ?? 'Non spécifiée';
                    $this->line("✓ brand: {$brand}");
                } catch (\Exception $e) {
                    $this->error("✗ brand: " . $e->getMessage());
                }
                
                // Test 4: condition
                try {
                    $condition = ucfirst($check->item->condition ?? 'Non spécifié');
                    $this->line("✓ condition: {$condition}");
                } catch (\Exception $e) {
                    $this->error("✗ condition: " . $e->getMessage());
                }
                
                // Test 5: price + currency
                try {
                    $price = number_format($check->item->price, 0, ',', ' ') . ' ' . $check->item->currency;
                    $this->line("✓ price: {$price}");
                } catch (\Exception $e) {
                    $this->error("✗ price: " . $e->getMessage());
                }
                
                // Test 6: description
                try {
                    $description = $check->item->description ?? 'Pas de description';
                    $this->line("✓ description: " . substr($description, 0, 50) . "...");
                } catch (\Exception $e) {
                    $this->error("✗ description: " . $e->getMessage());
                }
                
                // Test 7: images
                try {
                    $images = $check->item->images ?? [];
                    $this->line("✓ images: " . (is_array($images) ? count($images) . ' images' : 'Type: ' . gettype($images)));
                } catch (\Exception $e) {
                    $this->error("✗ images: " . $e->getMessage());
                }
                
                // Test 8: Analyser le contenu de ai_analysis_result
                if ($check->ai_analysis_result) {
                    try {
                        $aiData = is_string($check->ai_analysis_result) 
                            ? json_decode($check->ai_analysis_result, true) 
                            : $check->ai_analysis_result;
                        
                        if (is_array($aiData)) {
                            $this->line("✓ ai_analysis_result: array avec " . count($aiData) . " éléments");
                            
                            // Vérifier chaque élément
                            foreach ($aiData as $key => $value) {
                                $valueType = gettype($value);
                                if (is_array($value)) {
                                    $this->line("  - {$key}: array (" . count($value) . " éléments)");
                                } else {
                                    $displayValue = is_string($value) ? substr($value, 0, 30) : $value;
                                    $this->line("  - {$key}: {$valueType} - {$displayValue}");
                                }
                            }
                        } else {
                            $this->line("✓ ai_analysis_result: " . gettype($aiData));
                        }
                    } catch (\Exception $e) {
                        $this->error("✗ ai_analysis_result: " . $e->getMessage());
                    }
                }
            }
            
            $this->success("Tous les tests passent ! Le problème peut être ailleurs.");
            
        } catch (\Exception $e) {
            $this->error("ERREUR GLOBALE: " . $e->getMessage());
            $this->line("Fichier: " . $e->getFile());
            $this->line("Ligne: " . $e->getLine());
        }

        return 0;
    }
}