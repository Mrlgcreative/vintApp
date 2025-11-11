<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;

class CheckProductCurrencies extends Command
{
    protected $signature = 'expert:check-currencies';
    protected $description = 'Vérifie les devises des produits dans les vérifications';

    public function handle()
    {
        $this->info("=== VÉRIFICATION DES DEVISES ===");

        $checks = ProductAuthenticityCheck::with(['item', 'vendor'])->get();
        
        foreach ($checks as $check) {
            $this->line("─────────────────────────");
            $this->line("Vérification ID: {$check->id}");
            $this->line("Produit: " . ($check->item->name ?? 'Nom non défini'));
            $this->line("Prix: " . number_format($check->item->price, 2) . " " . ($check->item->currency ?? 'DEVISE NON DÉFINIE'));
            $this->line("Vendeur: " . ($check->vendor->name ?? 'Vendeur non défini'));
        }

        $this->info("\nVérification terminée !");
        return 0;
    }
}