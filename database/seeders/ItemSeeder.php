<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer le premier utilisateur ou créer un utilisateur de test
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $categories = Category::all();
        $brands = Brand::all();

        $items = [
            [
                'name' => 'iPhone 13 Pro',
                'description' => 'iPhone 13 Pro en excellent état, 128GB, couleur bleu',
                'price' => 899.99,
                'quantity' => 1,
                'condition' => 'like_new',
                'category_id' => $categories->where('slug', 'electronique')->first()->id,
                'brand_id' => $brands->where('slug', 'apple')->first()->id,
                'views' => 45,
            ],
            [
                'name' => 'Nike Air Max 270',
                'description' => 'Chaussures Nike Air Max 270, taille 42, couleur noire',
                'price' => 129.99,
                'quantity' => 1,
                'condition' => 'good',
                'category_id' => $categories->where('slug', 'sport')->first()->id,
                'brand_id' => $brands->where('slug', 'nike')->first()->id,
                'views' => 32,
            ],
            [
                'name' => 'MacBook Pro 2021',
                'description' => 'MacBook Pro 14 pouces, M1 Pro, 16GB RAM, 512GB SSD',
                'price' => 1999.99,
                'quantity' => 1,
                'condition' => 'new',
                'category_id' => $categories->where('slug', 'informatique')->first()->id,
                'brand_id' => $brands->where('slug', 'apple')->first()->id,
                'views' => 78,
            ],
            [
                'name' => 'Samsung Galaxy S21',
                'description' => 'Samsung Galaxy S21, 128GB, couleur violet',
                'price' => 699.99,
                'quantity' => 1,
                'condition' => 'good',
                'category_id' => $categories->where('slug', 'electronique')->first()->id,
                'brand_id' => $brands->where('slug', 'samsung')->first()->id,
                'views' => 56,
            ],
            [
                'name' => 'Livre Harry Potter',
                'description' => 'Collection complète Harry Potter, 7 tomes, édition collector',
                'price' => 89.99,
                'quantity' => 1,
                'condition' => 'good',
                'category_id' => $categories->where('slug', 'livres')->first()->id,
                'brand_id' => null,
                'views' => 23,
            ],
        ];

        foreach ($items as $item) {
            $item['user_id'] = $user->id;
            Item::create($item);
        }
    }
}
