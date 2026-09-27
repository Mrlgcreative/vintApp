<?php

namespace Database\Seeders;

use App\Models\AllowedCity;
use Illuminate\Database\Seeder;

class AddKolweziAndLubumbashiToAllowedCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'city_code' => 'CD_KOLWEZI',
                'name' => 'Kolwezi',
                'region' => 'Lualaba',
                'latitude' => -10.7167,
                'longitude' => 25.4667,
                'population' => 572942,
                'description' => 'Chef-lieu de la province du Lualaba en RDC, ville minière connue pour ses gisements de cuivre et de cobalt.',
            ],
            [
                'city_code' => 'CD_LUBUMBASHI',
                'name' => 'Lubumbashi',
                'region' => 'Haut-Katanga',
                'latitude' => -11.6870,
                'longitude' => 27.4794,
                'population' => 2800000,
                'description' => 'Chef-lieu du Haut-Katanga en RDC, grand pôle minier et commercial du sud du pays.',
            ],
        ];

        foreach ($cities as $city) {
            AllowedCity::updateOrCreate(
                ['city_code' => $city['city_code']],
                [
                    'name' => $city['name'],
                    'country' => 'Congo (RDC)',
                    'country_code' => 'CD',
                    'region' => $city['region'],
                    'latitude' => $city['latitude'],
                    'longitude' => $city['longitude'],
                    'population' => $city['population'],
                    'timezone' => 'Africa/Lubumbashi',
                    'is_active' => true,
                    'description' => $city['description'],
                ]
            );
        }
    }
}
