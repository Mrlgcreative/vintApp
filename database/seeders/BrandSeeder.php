<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple', 
                'slug' => 'apple', 
                'description' => 'Technologie Apple - iPhone, iPad, Mac',
                'is_active' => true
            ],
            [
                'name' => 'Samsung', 
                'slug' => 'samsung', 
                'description' => 'Électronique Samsung - Smartphones, TV',
                'is_active' => true
            ],
            [
                'name' => 'Nike', 
                'slug' => 'nike', 
                'description' => 'Sport et vêtements Nike',
                'is_active' => true
            ],
            [
                'name' => 'Adidas', 
                'slug' => 'adidas', 
                'description' => 'Sport et vêtements Adidas',
                'is_active' => true
            ],
            [
                'name' => 'Sony', 
                'slug' => 'sony', 
                'description' => 'Électronique Sony - TV, Audio, Caméras',
                'is_active' => true
            ],
            [
                'name' => 'LG', 
                'slug' => 'lg', 
                'description' => 'Électronique LG - TV, Électroménager',
                'is_active' => true
            ],
            [
                'name' => 'Dell', 
                'slug' => 'dell', 
                'description' => 'Informatique Dell - Ordinateurs, Serveurs',
                'is_active' => true
            ],
            [
                'name' => 'HP', 
                'slug' => 'hp', 
                'description' => 'Informatique HP - Ordinateurs, Imprimantes',
                'is_active' => true
            ],
            [
                'name' => 'Canon', 
                'slug' => 'canon', 
                'description' => 'Photographie Canon - Appareils photo, Imprimantes',
                'is_active' => true
            ],
            [
                'name' => 'Nikon', 
                'slug' => 'nikon', 
                'description' => 'Photographie Nikon - Appareils photo, Optiques',
                'is_active' => true
            ],
            [
                'name' => 'Puma', 
                'slug' => 'puma', 
                'description' => 'Sport et vêtements Puma',
                'is_active' => true
            ],
            [
                'name' => 'Under Armour', 
                'slug' => 'under-armour', 
                'description' => 'Sport et vêtements Under Armour',
                'is_active' => true
            ],
            [
                'name' => 'Asus', 
                'slug' => 'asus', 
                'description' => 'Informatique Asus - Ordinateurs, Cartes graphiques',
                'is_active' => true
            ],
            [
                'name' => 'Lenovo', 
                'slug' => 'lenovo', 
                'description' => 'Informatique Lenovo - Ordinateurs, Tablettes',
                'is_active' => true
            ],
            [
                'name' => 'Xiaomi', 
                'slug' => 'xiaomi', 
                'description' => 'Électronique Xiaomi - Smartphones, IoT',
                'is_active' => true
            ],
            [
                'name' => 'Huawei', 
                'slug' => 'huawei', 
                'description' => 'Électronique Huawei - Smartphones, Équipements réseau',
                'is_active' => true
            ],
            [
                'name' => 'OnePlus', 
                'slug' => 'oneplus', 
                'description' => 'Smartphones OnePlus',
                'is_active' => true
            ],
            [
                'name' => 'Google', 
                'slug' => 'google', 
                'description' => 'Technologie Google - Pixel, Nest',
                'is_active' => true
            ],
            [
                'name' => 'Microsoft', 
                'slug' => 'microsoft', 
                'description' => 'Technologie Microsoft - Surface, Xbox',
                'is_active' => true
            ],
            [
                'name' => 'Acer', 
                'slug' => 'acer', 
                'description' => 'Informatique Acer - Ordinateurs, Moniteurs',
                'is_active' => true
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
