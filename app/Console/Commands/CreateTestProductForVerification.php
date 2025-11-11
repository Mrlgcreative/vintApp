<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;

class CreateTestProductForVerification extends Command
{
    protected $signature = 'item:create-test-verification';
    protected $description = 'Crée un produit test pour tester la demande de vérification';

    public function handle()
    {
        // Trouver un utilisateur
        $user = User::first();
        if (!$user) {
            $this->error("Aucun utilisateur trouvé");
            return 1;
        }

        // Trouver la catégorie Electronique
        $category = Category::where('slug', 'electronique')->first();
        if (!$category) {
            $this->error("Catégorie electronique non trouvée");
            return 1;
        }

        // Créer un produit de test
        $item = Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'name' => 'MacBook Pro Test - Vérification',
            'description' => 'MacBook Pro M1 de test pour vérifier la fonctionnalité de demande d\'authenticité',
            'price' => 2500.00,
            'currency' => 'USD',
            'quantity' => 1,
            'condition' => 'like_new',
            'status' => 'active',
            'authenticity_requested' => false,
            'authenticity_verified' => false,
        ]);

        $this->info("✅ Produit de test créé avec succès !");
        $this->line("ID: {$item->id}");
        $this->line("Nom: {$item->name}");
        $this->line("Catégorie: {$category->name} (slug: {$category->slug})");
        $this->line("Utilisateur: {$user->name}");
        $this->line("Prix: {$item->price} {$item->currency}");
        
        // Tester immédiatement
        $this->info("\n=== TEST IMMEDIATE ===");
        $canRequest = $item->canRequestVerification();
        $this->line("Peut demander vérification: " . ($canRequest ? "OUI ✓" : "NON ✗"));
        
        $this->info("\nURL de test: " . url("/items/{$item->id}"));

        return 0;
    }
}