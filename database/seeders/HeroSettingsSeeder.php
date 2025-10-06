<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class HeroSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heroSettings = [
            [
                'key' => 'hero_title',
                'value' => 'Découvrez des articles uniques',
                'type' => 'text',
                'category' => 'hero',
                'label' => 'Titre du Hero',
                'description' => 'Titre principal de la bannière d\'accueil',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'hero_subtitle',
                'value' => 'La marketplace moderne pour acheter et vendre en toute sécurité. Rejoignez notre communauté et trouvez des produits exceptionnels.',
                'type' => 'textarea',
                'category' => 'hero',
                'label' => 'Sous-titre du Hero',
                'description' => 'Description sous le titre principal',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'hero_image',
                'value' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'type' => 'text',
                'category' => 'hero',
                'label' => 'Image de fond du Hero',
                'description' => 'URL de l\'image de fond de la bannière',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'hero_button_primary_text',
                'value' => 'Vendre',
                'type' => 'text',
                'category' => 'hero',
                'label' => 'Texte du bouton principal (connecté)',
                'description' => 'Texte affiché sur le bouton principal pour les utilisateurs connectés',
                'is_public' => true,
                'is_encrypted' => false,
            ],
            [
                'key' => 'hero_button_secondary_text',
                'value' => 'Parcourir',
                'type' => 'text',
                'category' => 'hero',
                'label' => 'Texte du bouton secondaire',
                'description' => 'Texte affiché sur le bouton secondaire',
                'is_public' => true,
                'is_encrypted' => false,
            ],
        ];

        foreach ($heroSettings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
