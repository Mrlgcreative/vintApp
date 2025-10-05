<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Ajouter les catégories, marques et l'admin
        $this->call([
            CategorySeeder::class,
            BrandSeeder::class,
            NotificationSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
