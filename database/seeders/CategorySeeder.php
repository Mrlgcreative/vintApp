<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Électronique', 
                'slug' => 'electronique', 
                'description' => 'Appareils électroniques et gadgets',
                'icon' => 'mobile-alt',
                'is_active' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Vêtements', 
                'slug' => 'vetements', 
                'description' => 'Vêtements et accessoires',
                'icon' => 'tshirt',
                'is_active' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Livres', 
                'slug' => 'livres', 
                'description' => 'Livres et publications',
                'icon' => 'book',
                'is_active' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Sport', 
                'slug' => 'sport', 
                'description' => 'Équipements sportifs',
                'icon' => 'futbol',
                'is_active' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Maison', 
                'slug' => 'maison', 
                'description' => 'Articles pour la maison',
                'icon' => 'home',
                'is_active' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Automobile', 
                'slug' => 'automobile', 
                'description' => 'Pièces et accessoires auto',
                'icon' => 'car',
                'is_active' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Jouets', 
                'slug' => 'jouets', 
                'description' => 'Jouets et jeux',
                'icon' => 'gamepad',
                'is_active' => true,
                'sort_order' => 7
            ],
            [
                'name' => 'Informatique', 
                'slug' => 'informatique', 
                'description' => 'Ordinateurs et accessoires',
                'icon' => 'laptop',
                'is_active' => true,
                'sort_order' => 8
            ],
            [
                'name' => 'Beauté', 
                'slug' => 'beaute', 
                'description' => 'Cosmétiques et produits de beauté',
                'icon' => 'spa',
                'is_active' => true,
                'sort_order' => 9
            ],
            [
                'name' => 'Musique', 
                'slug' => 'musique', 
                'description' => 'Instruments et équipements musicaux',
                'icon' => 'music',
                'is_active' => true,
                'sort_order' => 10
            ],
            [
                'name' => 'Jardinage', 
                'slug' => 'jardinage', 
                'description' => 'Outils et plantes de jardin',
                'icon' => 'seedling',
                'is_active' => true,
                'sort_order' => 11
            ],
            [
                'name' => 'Collection', 
                'slug' => 'collection', 
                'description' => 'Objets de collection',
                'icon' => 'gem',
                'is_active' => true,
                'sort_order' => 12
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
