<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;

class DebugExpertShow extends Command
{
    protected $signature = 'expert:debug-show {verification_id}';
    protected $description = 'Debug l\'affichage expert avec les mêmes données que la vue';

    public function handle()
    {
        $verificationId = $this->argument('verification_id');
        
        try {
            // Reproduire exactement ce que fait ExpertController::show
            $check = ProductAuthenticityCheck::findOrFail($verificationId);
            
            $this->info("=== CHARGEMENT INITIAL ===");
            $this->line("Vérification trouvée: {$check->id}");
            
            // Charger les relations comme dans le contrôleur
            $this->info("Chargement des relations...");
            $check->load([
                'item.category',
                'item.brand', 
                'item.user',
                'vendor',
                'verificationImages',
                'auditLogs.performer'
            ]);
            
            $this->info("\n=== VÉRIFICATION DES RELATIONS ===");
            $this->line("Item chargé: " . ($check->item ? "OUI" : "NON"));
            
            if ($check->item) {
                $this->line("Item name: " . ($check->item->name ?? 'NULL'));
                $this->line("Item category: " . ($check->item->category ? $check->item->category->name : 'NULL'));
                $this->line("Item brand: " . ($check->item->brand ? $check->item->brand->name : 'NULL'));
                $this->line("Item user: " . ($check->item->user ? $check->item->user->name : 'NULL'));
                
                // Vérifier les attributs de brand si elle existe
                if ($check->item->brand) {
                    $brand = $check->item->brand;
                    $this->info("\n=== DONNÉES BRAND ===");
                    $brandAttributes = $brand->getAttributes();
                    foreach ($brandAttributes as $key => $value) {
                        $type = gettype($value);
                        if (is_array($value)) {
                            $this->line("{$key}: array (" . count($value) . " éléments) - " . json_encode($value));
                        } else {
                            $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
                            $this->line("{$key}: {$type} - {$displayValue}");
                        }
                    }
                }
                
                // Vérifier les attributs de category si elle existe  
                if ($check->item->category) {
                    $category = $check->item->category;
                    $this->info("\n=== DONNÉES CATEGORY ===");
                    $categoryAttributes = $category->getAttributes();
                    foreach ($categoryAttributes as $key => $value) {
                        $type = gettype($value);
                        if (is_array($value)) {
                            $this->line("{$key}: array (" . count($value) . " éléments) - " . json_encode($value));
                        } else {
                            $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
                            $this->line("{$key}: {$type} - {$displayValue}");
                        }
                    }
                }
            }
            
            $this->line("Vendor chargé: " . ($check->vendor ? "OUI" : "NON"));
            $this->line("Images de vérification: " . $check->verificationImages->count());
            
            $this->info("\n=== TEST D'AFFICHAGE ===");
            // Tester les expressions qui sont utilisées dans la vue
            $this->line("Test \$check->item->name: " . ($check->item->name ?? 'NULL'));
            
            try {
                $testName = $check->item->name ?? 'Produit sans nom';
                $this->line("✓ Expression name OK: {$testName}");
            } catch (\Exception $e) {
                $this->error("✗ Erreur avec name: " . $e->getMessage());
            }
            
            try {
                $testPrice = number_format($check->item->price, 0, ',', ' ') . ' ' . $check->item->currency;
                $this->line("✓ Expression prix OK: {$testPrice}");
            } catch (\Exception $e) {
                $this->error("✗ Erreur avec prix: " . $e->getMessage());
            }
            
        } catch (\Exception $e) {
            $this->error("ERREUR: " . $e->getMessage());
            $this->line("Trace: " . $e->getTraceAsString());
        }

        return 0;
    }
}