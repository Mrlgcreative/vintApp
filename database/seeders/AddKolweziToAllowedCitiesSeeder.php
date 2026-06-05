<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AllowedCity;

class AddKolweziToAllowedCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AllowedCity::updateOrCreate(
            ['city_code' => 'KOL'],
            [
                'name' => 'Kolwezi',
                'country' => 'Congo (RDC)',
                'country_code' => 'CD',
                'region' => 'Katanga',
                'latitude' => -10.7167,
                'longitude' => 25.4667,
                'population' => 572942,
                'timezone' => 'Africa/Lubumbashi',
                'is_active' => true,
                'description' => 'Ville majeure de la province du Katanga en RDC, connue pour ses mines de cuivre et de cobalt'
            ]
        );
    }
}