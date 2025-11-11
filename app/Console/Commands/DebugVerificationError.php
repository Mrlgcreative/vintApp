<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;

class DebugVerificationError extends Command
{
    protected $signature = 'expert:debug-error {verification_id}';
    protected $description = 'Debug l\'erreur htmlspecialchars pour une vérification';

    public function handle()
    {
        $verificationId = $this->argument('verification_id');
        $check = ProductAuthenticityCheck::with('item')->find($verificationId);
        
        if (!$check) {
            $this->error("Vérification ID {$verificationId} non trouvée");
            return 1;
        }

        $this->info("=== DEBUG ERREUR VERIFICATION ===");
        $this->line("Vérification ID: {$check->id}");
        $this->line("Item ID: {$check->item_id}");
        
        if ($check->item) {
            $this->info("\n=== DONNÉES DU PRODUIT ===");
            $this->line("Nom (name): " . ($check->item->name ?? 'NULL'));
            
            // Vérifier si title existe
            $item = $check->item;
            $attributes = $item->getAttributes();
            
            $this->line("Attribut 'title' existe: " . (array_key_exists('title', $attributes) ? 'OUI' : 'NON'));
            
            if (array_key_exists('title', $attributes)) {
                $titleValue = $attributes['title'];
                $this->line("Type de title: " . gettype($titleValue));
                
                if (is_array($titleValue)) {
                    $this->error("❌ PROBLÈME: title est un array!");
                    $this->line("Contenu: " . json_encode($titleValue));
                } else {
                    $this->line("Valeur title: " . ($titleValue ?? 'NULL'));
                }
            }
            
            // Lister tous les attributs
            $this->info("\n=== TOUS LES ATTRIBUTS ===");
            foreach ($attributes as $key => $value) {
                $type = gettype($value);
                if (is_array($value)) {
                    $this->line("{$key}: array (" . count($value) . " éléments)");
                } else {
                    $displayValue = is_string($value) ? substr($value, 0, 50) : $value;
                    $this->line("{$key}: {$type} - {$displayValue}");
                }
            }
        } else {
            $this->error("Aucun item lié à cette vérification");
        }

        return 0;
    }
}