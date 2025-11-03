<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PointConversionRate;
use Carbon\Carbon;

class AffiliateSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les taux de conversion de base
        $this->createConversionRates();
        
        $this->command->info('Système d\'affiliation initialisé avec succès !');
    }

    /**
     * Crée les taux de conversion par défaut
     */
    private function createConversionRates(): void
    {
        // Taux USD : 1000 points = 1 USD
        PointConversionRate::create([
            'currency' => 'USD',
            'points_per_unit' => 1000.00,
            'minimum_conversion' => 500.00,
            'maximum_conversion' => 50000.00,
            'conversion_fee_percentage' => 5.00, // 5% de frais
            'conversion_fee_fixed' => 0.50, // 50 cents fixes
            'is_active' => true,
            'effective_from' => Carbon::now(),
            'effective_until' => null,
            'conditions' => [
                'min_level' => 1,
                'daily_limit' => 10000, // 10,000 points max par jour
                'min_referrals' => 0
            ],
            'notes' => 'Taux de conversion standard USD - 1000 points = 1 USD'
        ]);

        // Taux CDF : 100 points = 1 CDF
        PointConversionRate::create([
            'currency' => 'CDF',
            'points_per_unit' => 100.00,
            'minimum_conversion' => 200.00,
            'maximum_conversion' => 200000.00,
            'conversion_fee_percentage' => 3.00, // 3% de frais
            'conversion_fee_fixed' => 50.00, // 50 CDF fixes
            'is_active' => true,
            'effective_from' => Carbon::now(),
            'effective_until' => null,
            'conditions' => [
                'min_level' => 1,
                'daily_limit' => 50000, // 50,000 points max par jour
                'min_referrals' => 0
            ],
            'notes' => 'Taux de conversion standard CDF - 100 points = 1 CDF'
        ]);

        // Taux premium USD pour les utilisateurs de niveau élevé
        PointConversionRate::create([
            'currency' => 'USD',
            'points_per_unit' => 900.00, // Meilleur taux : 900 points = 1 USD
            'minimum_conversion' => 1000.00,
            'maximum_conversion' => 100000.00,
            'conversion_fee_percentage' => 2.00, // Frais réduits : 2%
            'conversion_fee_fixed' => 0.25,
            'is_active' => true,
            'effective_from' => Carbon::now(),
            'effective_until' => null,
            'conditions' => [
                'min_level' => 5, // Niveau 5 requis
                'daily_limit' => 20000,
                'min_referrals' => 5 // Minimum 5 parrainages
            ],
            'notes' => 'Taux premium USD pour utilisateurs niveau 5+ avec 5+ parrainages'
        ]);

        $this->command->info('Taux de conversion créés avec succès.');
    }
}