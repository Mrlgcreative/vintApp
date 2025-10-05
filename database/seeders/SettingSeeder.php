<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $settings = [
            // Paramètres généraux de l'application
            [
                'key' => 'app_name',
                'value' => 'VintApp',
                'type' => 'string',
                'category' => 'general',
                'label' => 'Nom de l\'application',
                'description' => 'Le nom affiché de l\'application',
                'is_public' => true,
            ],
            [
                'key' => 'commission_rate',
                'value' => 5.0,
                'type' => 'float',
                'category' => 'payment',
                'label' => 'Taux de commission (%)',
                'description' => 'Pourcentage de commission sur les ventes',
                'is_public' => false,
            ],
            [
                'key' => 'min_withdrawal_amount',
                'value' => 10,
                'type' => 'integer',
                'category' => 'payment',
                'label' => 'Montant minimum de retrait',
                'description' => 'Montant minimum pour effectuer un retrait',
                'is_public' => false,
            ],
            [
                'key' => 'max_images_per_item',
                'value' => 10,
                'type' => 'integer',
                'category' => 'content',
                'label' => 'Images max par article',
                'description' => 'Nombre maximum d\'images par article',
                'is_public' => true,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
