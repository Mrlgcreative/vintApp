<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoostTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boostTypes = [
            [
                'name' => 'featured',
                'display_name' => '⭐ Produit Vedette',
                'description' => 'Votre produit sera marqué comme vedette avec une étoile dorée et affiché en priorité.',
                'icon' => 'fas fa-star',
                'color' => '#F59E0B',
                'price_usd' => 2.00,
                'price_cdf' => 4000,
                'available_durations' => json_encode([24, 48, 72, 168]), // 1j, 2j, 3j, 1sem
                'visual_config' => json_encode([
                    'badge_text' => 'VEDETTE',
                    'badge_color' => 'bg-yellow-500',
                    'border_color' => 'border-yellow-400',
                    'glow_effect' => true
                ]),
                'sort_order' => 1
            ],
            [
                'name' => 'top',
                'display_name' => '🔝 Top Position',
                'description' => 'Votre produit apparaîtra toujours en haut des listes de recherche et catégories.',
                'icon' => 'fas fa-arrow-up',
                'color' => '#10B981',
                'price_usd' => 1.50,
                'price_cdf' => 3000,
                'available_durations' => json_encode([12, 24, 48, 72]),
                'visual_config' => json_encode([
                    'badge_text' => 'TOP',
                    'badge_color' => 'bg-green-500',
                    'priority_boost' => 100
                ]),
                'sort_order' => 2
            ],
            [
                'name' => 'urgent',
                'display_name' => '🚨 Urgent',
                'description' => 'Badge urgent rouge pour créer un sentiment d\'urgence et attirer l\'attention.',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => '#EF4444',
                'price_usd' => 1.00,
                'price_cdf' => 2000,
                'available_durations' => json_encode([6, 12, 24, 48]),
                'visual_config' => json_encode([
                    'badge_text' => 'URGENT',
                    'badge_color' => 'bg-red-500',
                    'pulse_animation' => true
                ]),
                'sort_order' => 3
            ],
            [
                'name' => 'premium',
                'display_name' => '👑 Premium',
                'description' => 'Bordure dorée premium et badge couronne pour un look haut de gamme.',
                'icon' => 'fas fa-crown',
                'color' => '#D97706',
                'price_usd' => 3.00,
                'price_cdf' => 6000,
                'available_durations' => json_encode([24, 48, 72, 168, 336]), // jusqu'à 2 semaines
                'visual_config' => json_encode([
                    'badge_text' => 'PREMIUM',
                    'badge_color' => 'bg-gradient-to-r from-yellow-400 to-orange-500',
                    'border_color' => 'border-yellow-400',
                    'border_width' => '2px',
                    'shadow_effect' => 'shadow-xl'
                ]),
                'sort_order' => 4
            ],
            [
                'name' => 'spotlight',
                'display_name' => '💡 Spotlight',
                'description' => 'Votre produit sera mis en avant dans un carrousel spécial sur la page d\'accueil.',
                'icon' => 'fas fa-lightbulb',
                'color' => '#8B5CF6',
                'price_usd' => 5.00,
                'price_cdf' => 10000,
                'available_durations' => json_encode([24, 48, 72]),
                'visual_config' => json_encode([
                    'badge_text' => 'SPOTLIGHT',
                    'badge_color' => 'bg-purple-500',
                    'homepage_carousel' => true,
                    'special_animation' => 'spotlight'
                ]),
                'sort_order' => 5
            ]
        ];

        foreach ($boostTypes as $boostType) {
            DB::table('boost_types')->insert(array_merge($boostType, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
