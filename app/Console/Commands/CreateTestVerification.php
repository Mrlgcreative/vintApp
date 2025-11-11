<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductAuthenticityCheck;
use App\Models\Item;
use App\Models\User;

class CreateTestVerification extends Command
{
    protected $signature = 'expert:create-test';
    protected $description = 'Crée une vérification de test avec un produit complet';

    public function handle()
    {
        // Créer un utilisateur vendeur de test s'il n'existe pas
        $vendor = User::firstOrCreate(
            ['email' => 'vendor.test@vintapp.com'],
            [
                'name' => 'Vendeur Test',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Créer un produit de test
        $item = Item::firstOrCreate(
            ['name' => 'Sac Louis Vuitton Vintage'],
            [
                'name' => 'Sac Louis Vuitton Vintage',
                'description' => 'Magnifique sac Louis Vuitton en cuir véritable, modèle vintage en excellent état.',
                'price' => 1200.00,
                'category_id' => 1,
                'user_id' => $vendor->id,
                'condition' => 'good',
                'status' => 'active',
                'authenticity_requested' => true,
                'authenticity_verified' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Créer une vérification d'authenticité
        $check = ProductAuthenticityCheck::create([
            'item_id' => $item->id,
            'user_id' => $vendor->id,
            'status' => ProductAuthenticityCheck::STATUS_EXPERT_REVIEW,
            'expert_id' => 35, // Marie Dubois
            'ai_confidence_score' => 0.75,
            'ai_analysis_result' => [
                'authenticity_probability' => 0.75,
                'detected_features' => ['logo_quality', 'stitching_pattern', 'material_texture'],
                'concerns' => ['date_code_unclear']
            ],
            'verification_fee' => 50.00,
            'payment_completed' => true,
            'submitted_at' => now()->subHours(2),
            'ai_completed_at' => now()->subHours(1),
            'expert_assigned_at' => now()->subMinutes(30),
        ]);

        $this->info("Vérification de test créée avec succès !");
        $this->line("Item ID: {$item->id} - {$item->name}");
        $this->line("Vérification ID: {$check->id}");
        $this->line("Assigné à l'expert ID: {$check->expert_id}");
        $this->line("Statut: {$check->status}");

        return 0;
    }
}