<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AllowedCity;

/**
 * Ajoute tous les pays africains (et leurs principales villes) comme zones autorisées.
 *
 * L'enregistrement de chaque pays (même sans ville) est indispensable pour que
 * la géolocalisation IP (IpGeoLocationService) considère le code pays comme
 * « desservi » : AllowedCity::countryCodeIsAllowed() se base sur les distincts
 * `country_code` présents dans allowed_cities.
 *
 * Idempotent : chaque ligne est créée/écrasée via updateOrCreate sur city_code.
 */
class AfricaCountriesCitiesSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->countries() as $entry) {
            $countryCode = strtoupper($entry['code']);
            $countryName = $entry['country'];

            foreach ($entry['cities'] as $city) {
                AllowedCity::updateOrCreate(
                    ['city_code' => $entry['code'] . '_' . strtoupper(str_replace(' ', '_', $city['name']))],
                    [
                        'name' => $city['name'],
                        'country' => $countryName,
                        'country_code' => $countryCode,
                        'region' => $city['region'] ?? null,
                        'latitude' => $city['lat'],
                        'longitude' => $city['lng'],
                        'population' => $city['population'] ?? null,
                        'timezone' => $this->timezoneFor($countryCode),
                        'is_active' => true,
                        'description' => $city['description']
                            ?? "Ville en {$countryName} (VintApp).",
                    ]
                );
            }
        }
    }

    /**
     * Route : 54 pays africains + capitales (et villes majeures).
     * Coordonnées approx. WGS84 (lat, lng).
     */
    protected function countries(): array
    {
        return [
            ['code' => 'DZ', 'country' => 'Algérie', 'cities' => [
                ['name' => 'Alger', 'lat' => 36.7538, 'lng' => 3.0588, 'region' => 'Alger'],
                ['name' => 'Oran', 'lat' => 35.6987, 'lng' => -0.6333, 'region' => 'Oran'],
                ['name' => 'Constantine', 'lat' => 36.3650, 'lng' => 6.6147, 'region' => 'Constantine'],
            ]],
            ['code' => 'AO', 'country' => 'Angola', 'cities' => [
                ['name' => 'Luanda', 'lat' => -8.8390, 'lng' => 13.2894, 'region' => 'Luanda'],
                ['name' => 'Lubango', 'lat' => -14.9080, 'lng' => 13.4940, 'region' => 'Huíla'],
                ['name' => 'Benguela', 'lat' => -12.5783, 'lng' => 13.4070, 'region' => 'Benguela'],
            ]],
            ['code' => 'BJ', 'country' => 'Bénin', 'cities' => [
                ['name' => 'Porto-Novo', 'lat' => 6.4969, 'lng' => 2.6286, 'region' => 'Ouémé'],
                ['name' => 'Cotonou', 'lat' => 6.3654, 'lng' => 2.4183, 'region' => 'Littoral'],
            ]],
            ['code' => 'BW', 'country' => 'Botswana', 'cities' => [
                ['name' => 'Gaborone', 'lat' => -24.6282, 'lng' => 25.9231, 'region' => 'Gaborone'],
                ['name' => 'Francistown', 'lat' => -21.1700, 'lng' => 27.5079, 'region' => 'North-East'],
            ]],
            ['code' => 'BF', 'country' => 'Burkina Faso', 'cities' => [
                ['name' => 'Ouagadougou', 'lat' => 12.3714, 'lng' => -1.5197, 'region' => 'Centre'],
                ['name' => 'Bobo-Dioulasso', 'lat' => 11.1833, 'lng' => -4.2833, 'region' => 'Hauts-Bassins'],
            ]],
            ['code' => 'BI', 'country' => 'Burundi', 'cities' => [
                ['name' => 'Gitega', 'lat' => -3.4264, 'lng' => 29.9306, 'region' => 'Gitega'],
                ['name' => 'Bujumbura', 'lat' => -3.3614, 'lng' => 29.3600, 'region' => 'Bujumbura Mairie'],
            ]],
            ['code' => 'CM', 'country' => 'Cameroun', 'cities' => [
                ['name' => 'Yaoundé', 'lat' => 3.8480, 'lng' => 11.5021, 'region' => 'Centre'],
                ['name' => 'Douala', 'lat' => 4.0511, 'lng' => 9.7679, 'region' => 'Littoral'],
                ['name' => 'Bafoussam', 'lat' => 5.4781, 'lng' => 10.4177, 'region' => 'Ouest'],
            ]],
            ['code' => 'CV', 'country' => 'Cap-Vert', 'cities' => [
                ['name' => 'Praia', 'lat' => 14.9330, 'lng' => -23.5133, 'region' => 'Santiago'],
                ['name' => 'Mindelo', 'lat' => 16.8901, 'lng' => -24.9804, 'region' => 'São Vicente'],
            ]],
            ['code' => 'CF', 'country' => 'République centrafricaine', 'cities' => [
                ['name' => 'Bangui', 'lat' => 4.3947, 'lng' => 18.5582, 'region' => 'Bangui'],
            ]],
            ['code' => 'TD', 'country' => 'Tchad', 'cities' => [
                ['name' => 'N’Djamena', 'lat' => 12.1348, 'lng' => 15.0557, 'region' => 'N’Djamena'],
                ['name' => 'Moundou', 'lat' => 8.5667, 'lng' => 16.0833, 'region' => 'Logone Occidental'],
            ]],
            ['code' => 'KM', 'country' => 'Comores', 'cities' => [
                ['name' => 'Moroni', 'lat' => -11.7022, 'lng' => 43.2551, 'region' => 'Grande Comore'],
            ]],
            ['code' => 'CG', 'country' => 'Congo (Brazzaville)', 'cities' => [
                ['name' => 'Brazzaville', 'lat' => -4.2634, 'lng' => 15.2429, 'region' => 'Brazzaville'],
                ['name' => 'Pointe-Noire', 'lat' => -4.7692, 'lng' => 11.8660, 'region' => 'Pointe-Noire'],
            ]],
            ['code' => 'CD', 'country' => 'Congo (RDC)', 'cities' => [
                ['name' => 'Kinshasa', 'lat' => -4.4419, 'lng' => 15.2663, 'region' => 'Kinshasa'],
                ['name' => 'Lubumbashi', 'lat' => -11.6870, 'lng' => 27.4794, 'region' => 'Haut-Katanga'],
                ['name' => 'Goma', 'lat' => -1.6741, 'lng' => 29.2238, 'region' => 'Nord-Kivu'],
                ['name' => 'Bukavu', 'lat' => -2.5000, 'lng' => 28.8500, 'region' => 'Sud-Kivu'],
                ['name' => 'Kisangani', 'lat' => 0.5153, 'lng' => 25.1910, 'region' => 'Tshopo'],
                ['name' => 'Mbuji-Mayi', 'lat' => -6.1509, 'lng' => 23.5898, 'region' => 'Kasaï-Oriental'],
                ['name' => 'Kananga', 'lat' => -5.8963, 'lng' => 22.4166, 'region' => 'Kasaï-Central'],
                ['name' => 'Kolwezi', 'lat' => -10.7167, 'lng' => 25.4667, 'region' => 'Lualaba'],
                ['name' => 'Matadi', 'lat' => -5.7995, 'lng' => 13.4408, 'region' => 'Kongo-Central'],
                ['name' => 'Mbandaka', 'lat' => 0.0483, 'lng' => 18.2603, 'region' => 'Équateur'],
            ]],
            ['code' => 'DJ', 'country' => 'Djibouti', 'cities' => [
                ['name' => 'Djibouti', 'lat' => 11.5721, 'lng' => 43.1457, 'region' => 'Djibouti Ville'],
            ]],
            ['code' => 'EG', 'country' => 'Égypte', 'cities' => [
                ['name' => 'Le Caire', 'lat' => 30.0444, 'lng' => 31.2357, 'region' => 'Le Caire'],
                ['name' => 'Alexandrie', 'lat' => 31.2001, 'lng' => 29.9187, 'region' => 'Alexandrie'],
                ['name' => 'Gizeh', 'lat' => 30.0131, 'lng' => 31.2089, 'region' => 'Gizeh'],
            ]],
            ['code' => 'GQ', 'country' => 'Guinée équatoriale', 'cities' => [
                ['name' => 'Malabo', 'lat' => 3.7500, 'lng' => 8.7833, 'region' => 'Bioko Norte'],
                ['name' => 'Bata', 'lat' => 1.8631, 'lng' => 9.7658, 'region' => 'Litoral'],
            ]],
            ['code' => 'ER', 'country' => 'Érythrée', 'cities' => [
                ['name' => 'Asmara', 'lat' => 15.3229, 'lng' => 38.9251, 'region' => 'Maekel'],
            ]],
            ['code' => 'SZ', 'country' => 'Eswatini', 'cities' => [
                ['name' => 'Mbabane', 'lat' => -26.3054, 'lng' => 31.1367, 'region' => 'Hhohho'],
            ]],
            ['code' => 'ET', 'country' => 'Éthiopie', 'cities' => [
                ['name' => 'Addis-Abeba', 'lat' => 9.0243, 'lng' => 38.7469, 'region' => 'Addis-Abeba'],
                ['name' => 'Dire Dawa', 'lat' => 9.5931, 'lng' => 41.8661, 'region' => 'Dire Dawa'],
                ['name' => 'Gondar', 'lat' => 12.6030, 'lng' => 37.4521, 'region' => 'Amhara'],
            ]],
            ['code' => 'GA', 'country' => 'Gabon', 'cities' => [
                ['name' => 'Libreville', 'lat' => 0.4162, 'lng' => 9.4673, 'region' => 'Estuaire'],
                ['name' => 'Port-Gentil', 'lat' => -0.7200, 'lng' => 8.7817, 'region' => 'Ogooué-Maritime'],
            ]],
            ['code' => 'GM', 'country' => 'Gambie', 'cities' => [
                ['name' => 'Banjul', 'lat' => 13.4527, 'lng' => -16.5780, 'region' => 'Banjul'],
                ['name' => 'Serekunda', 'lat' => 13.4383, 'lng' => -16.6781, 'region' => 'Banjul'],
            ]],
            ['code' => 'GH', 'country' => 'Ghana', 'cities' => [
                ['name' => 'Accra', 'lat' => 5.6037, 'lng' => -0.1870, 'region' => 'Greater Accra'],
                ['name' => 'Kumasi', 'lat' => 6.6885, 'lng' => -1.6244, 'region' => 'Ashanti'],
                ['name' => 'Tamale', 'lat' => 9.4032, 'lng' => -0.8390, 'region' => 'Northern'],
            ]],
            ['code' => 'GN', 'country' => 'Guinée', 'cities' => [
                ['name' => 'Conakry', 'lat' => 9.6412, 'lng' => -13.5784, 'region' => 'Conakry'],
                ['name' => 'Kankan', 'lat' => 10.3858, 'lng' => -9.3057, 'region' => 'Kankan'],
            ]],
            ['code' => 'GW', 'country' => 'Guinée-Bissau', 'cities' => [
                ['name' => 'Bissau', 'lat' => 11.8636, 'lng' => -15.5977, 'region' => 'Bissau'],
            ]],
            ['code' => 'CI', 'country' => 'Côte d’Ivoire', 'cities' => [
                ['name' => 'Yamoussoukro', 'lat' => 6.8276, 'lng' => -5.2893, 'region' => 'Yamoussoukro'],
                ['name' => 'Abidjan', 'lat' => 5.3599, 'lng' => -4.0083, 'region' => 'Abidjan'],
                ['name' => 'Bouaké', 'lat' => 7.6853, 'lng' => -5.0302, 'region' => 'Vallée du Bandama'],
            ]],
            ['code' => 'KE', 'country' => 'Kenya', 'cities' => [
                ['name' => 'Nairobi', 'lat' => -1.2921, 'lng' => 36.8219, 'region' => 'Nairobi'],
                ['name' => 'Mombasa', 'lat' => -4.0435, 'lng' => 39.6682, 'region' => 'Mombasa'],
                ['name' => 'Kisumu', 'lat' => -0.0917, 'lng' => 34.7680, 'region' => 'Kisumu'],
            ]],
            ['code' => 'LS', 'country' => 'Lesotho', 'cities' => [
                ['name' => 'Maseru', 'lat' => -29.3150, 'lng' => 27.4869, 'region' => 'Maseru'],
            ]],
            ['code' => 'LR', 'country' => 'Liberia', 'cities' => [
                ['name' => 'Monrovia', 'lat' => 6.3008, 'lng' => -10.7970, 'region' => 'Montserrado'],
            ]],
            ['code' => 'LY', 'country' => 'Libye', 'cities' => [
                ['name' => 'Tripoli', 'lat' => 32.8872, 'lng' => 13.1913, 'region' => 'Tripoli'],
                ['name' => 'Benghazi', 'lat' => 32.1190, 'lng' => 20.0867, 'region' => 'Benghazi'],
            ]],
            ['code' => 'MG', 'country' => 'Madagascar', 'cities' => [
                ['name' => 'Antananarivo', 'lat' => -18.8792, 'lng' => 47.5079, 'region' => 'Analamanga'],
                ['name' => 'Antsirabe', 'lat' => -19.8590, 'lng' => 47.0330, 'region' => 'Vakinankaratra'],
                ['name' => 'Toamasina', 'lat' => -18.1443, 'lng' => 49.3958, 'region' => 'Atsinanana'],
            ]],
            ['code' => 'MW', 'country' => 'Malawi', 'cities' => [
                ['name' => 'Lilongwe', 'lat' => -13.9626, 'lng' => 33.7741, 'region' => 'Lilongwe'],
                ['name' => 'Blantyre', 'lat' => -15.7861, 'lng' => 35.0058, 'region' => 'Blantyre'],
            ]],
            ['code' => 'ML', 'country' => 'Mali', 'cities' => [
                ['name' => 'Bamako', 'lat' => 12.6392, 'lng' => -8.0029, 'region' => 'Bamako'],
                ['name' => 'Ségou', 'lat' => 13.4317, 'lng' => -6.2157, 'region' => 'Ségou'],
                ['name' => 'Sikasso', 'lat' => 11.3154, 'lng' => -5.6666, 'region' => 'Sikasso'],
            ]],
            ['code' => 'MR', 'country' => 'Mauritanie', 'cities' => [
                ['name' => 'Nouakchott', 'lat' => 18.0735, 'lng' => -15.9582, 'region' => 'Nouakchott'],
                ['name' => 'Nouadhibou', 'lat' => 20.9310, 'lng' => -17.0349, 'region' => 'Dakhlet Nouadhibou'],
            ]],
            ['code' => 'MU', 'country' => 'Maurice', 'cities' => [
                ['name' => 'Port-Louis', 'lat' => -20.1609, 'lng' => 57.5012, 'region' => 'Port Louis'],
                ['name' => 'Curepipe', 'lat' => -20.3163, 'lng' => 57.5200, 'region' => 'Plaines Wilhems'],
            ]],
            ['code' => 'MA', 'country' => 'Maroc', 'cities' => [
                ['name' => 'Rabat', 'lat' => 34.0209, 'lng' => -6.8416, 'region' => 'Rabat-Salé-Kénitra'],
                ['name' => 'Casablanca', 'lat' => 33.5731, 'lng' => -7.5898, 'region' => 'Casablanca-Settat'],
                ['name' => 'Marrakech', 'lat' => 31.6295, 'lng' => -7.9811, 'region' => 'Marrakech-Safi'],
            ]],
            ['code' => 'MZ', 'country' => 'Mozambique', 'cities' => [
                ['name' => 'Maputo', 'lat' => -25.9692, 'lng' => 32.5732, 'region' => 'Maputo'],
                ['name' => 'Beira', 'lat' => -19.8436, 'lng' => 34.8389, 'region' => 'Sofala'],
                ['name' => 'Nampula', 'lat' => -15.1165, 'lng' => 39.2666, 'region' => 'Nampula'],
            ]],
            ['code' => 'NA', 'country' => 'Namibie', 'cities' => [
                ['name' => 'Windhoek', 'lat' => -22.5609, 'lng' => 17.0658, 'region' => 'Khomas'],
                ['name' => 'Walvis Bay', 'lat' => -22.9575, 'lng' => 14.5053, 'region' => 'Erongo'],
            ]],
            ['code' => 'NE', 'country' => 'Niger', 'cities' => [
                ['name' => 'Niamey', 'lat' => 13.5116, 'lng' => 2.1254, 'region' => 'Niamey'],
                ['name' => 'Zinder', 'lat' => 13.8068, 'lng' => 8.9882, 'region' => 'Zinder'],
            ]],
            ['code' => 'NG', 'country' => 'Nigéria', 'cities' => [
                ['name' => 'Abuja', 'lat' => 9.0579, 'lng' => 7.4951, 'region' => 'Federal Capital'],
                ['name' => 'Lagos', 'lat' => 6.5244, 'lng' => 3.3792, 'region' => 'Lagos'],
                ['name' => 'Kano', 'lat' => 12.0022, 'lng' => 8.5920, 'region' => 'Kano'],
                ['name' => 'Ibadan', 'lat' => 7.3775, 'lng' => 3.9470, 'region' => 'Oyo'],
            ]],
            ['code' => 'RW', 'country' => 'Rwanda', 'cities' => [
                ['name' => 'Kigali', 'lat' => -1.9441, 'lng' => 30.0619, 'region' => 'Kigali'],
                ['name' => 'Butare', 'lat' => -2.5960, 'lng' => 29.7394, 'region' => 'Southern'],
            ]],
            ['code' => 'ST', 'country' => 'Sao Tomé-et-Principe', 'cities' => [
                ['name' => 'São Tomé', 'lat' => 0.3365, 'lng' => 6.7273, 'region' => 'São Tomé'],
            ]],
            ['code' => 'SN', 'country' => 'Sénégal', 'cities' => [
                ['name' => 'Dakar', 'lat' => 14.6937, 'lng' => -17.4441, 'region' => 'Dakar'],
                ['name' => 'Thiès', 'lat' => 14.7910, 'lng' => -16.9260, 'region' => 'Thiès'],
                ['name' => 'Saint-Louis', 'lat' => 16.0185, 'lng' => -16.4896, 'region' => 'Saint-Louis'],
            ]],
            ['code' => 'SC', 'country' => 'Seychelles', 'cities' => [
                ['name' => 'Victoria', 'lat' => -4.6191, 'lng' => 55.4513, 'region' => 'Mahé'],
            ]],
            ['code' => 'SL', 'country' => 'Sierra Leone', 'cities' => [
                ['name' => 'Freetown', 'lat' => 8.4846, 'lng' => -13.2346, 'region' => 'Western Area'],
            ]],
            ['code' => 'SO', 'country' => 'Somalie', 'cities' => [
                ['name' => 'Mogadiscio', 'lat' => 2.0469, 'lng' => 45.3182, 'region' => 'Banaadir'],
                ['name' => 'Hargeisa', 'lat' => 9.5624, 'lng' => 44.0770, 'region' => 'Woqooyi Galbeed'],
            ]],
            ['code' => 'ZA', 'country' => 'Afrique du Sud', 'cities' => [
                ['name' => 'Pretoria', 'lat' => -25.7479, 'lng' => 28.2293, 'region' => 'Gauteng'],
                ['name' => 'Johannesburg', 'lat' => -26.2041, 'lng' => 28.0473, 'region' => 'Gauteng'],
                ['name' => 'Le Cap', 'lat' => -33.9249, 'lng' => 18.4241, 'region' => 'Western Cape'],
                ['name' => 'Durban', 'lat' => -29.8587, 'lng' => 31.0218, 'region' => 'KwaZulu-Natal'],
            ]],
            ['code' => 'SS', 'country' => 'Soudan du Sud', 'cities' => [
                ['name' => 'Djouba', 'lat' => 4.8594, 'lng' => 31.5713, 'region' => 'Central Equatoria'],
            ]],
            ['code' => 'SD', 'country' => 'Soudan', 'cities' => [
                ['name' => 'Khartoum', 'lat' => 15.5007, 'lng' => 32.5599, 'region' => 'Khartoum'],
                ['name' => 'Omdourman', 'lat' => 15.6167, 'lng' => 32.4800, 'region' => 'Khartoum'],
            ]],
            ['code' => 'TZ', 'country' => 'Tanzanie', 'cities' => [
                ['name' => 'Dodoma', 'lat' => -6.1630, 'lng' => 35.7516, 'region' => 'Dodoma'],
                ['name' => 'Dar es Salam', 'lat' => -6.7924, 'lng' => 39.2083, 'region' => 'Dar es Salaam'],
                ['name' => 'Arusha', 'lat' => -3.3869, 'lng' => 36.6830, 'region' => 'Arusha'],
            ]],
            ['code' => 'TG', 'country' => 'Togo', 'cities' => [
                ['name' => 'Lomé', 'lat' => 6.1256, 'lng' => 1.2254, 'region' => 'Maritime'],
                ['name' => 'Sokodé', 'lat' => 8.9833, 'lng' => 1.1333, 'region' => 'Centrale'],
            ]],
            ['code' => 'TN', 'country' => 'Tunisie', 'cities' => [
                ['name' => 'Tunis', 'lat' => 36.8065, 'lng' => 10.1815, 'region' => 'Tunis'],
                ['name' => 'Sfax', 'lat' => 34.7406, 'lng' => 10.7603, 'region' => 'Sfax'],
                ['name' => 'Sousse', 'lat' => 35.8256, 'lng' => 10.6084, 'region' => 'Sousse'],
            ]],
            ['code' => 'UG', 'country' => 'Ouganda', 'cities' => [
                ['name' => 'Kampala', 'lat' => 0.3476, 'lng' => 32.5825, 'region' => 'Central'],
                ['name' => 'Gulu', 'lat' => 2.7731, 'lng' => 32.2890, 'region' => 'Northern'],
                ['name' => 'Mbarara', 'lat' => -0.6072, 'lng' => 30.6545, 'region' => 'Western'],
            ]],
            ['code' => 'ZM', 'country' => 'Zambie', 'cities' => [
                ['name' => 'Lusaka', 'lat' => -15.3875, 'lng' => 28.3228, 'region' => 'Lusaka'],
                ['name' => 'Kitwe', 'lat' => -12.8024, 'lng' => 28.2132, 'region' => 'Copperbelt'],
                ['name' => 'Ndola', 'lat' => -12.9586, 'lng' => 28.6366, 'region' => 'Copperbelt'],
            ]],
            ['code' => 'ZW', 'country' => 'Zimbabwe', 'cities' => [
                ['name' => 'Harare', 'lat' => -17.8252, 'lng' => 31.0335, 'region' => 'Harare'],
                ['name' => 'Bulawayo', 'lat' => -20.1481, 'lng' => 28.5868, 'region' => 'Bulawayo'],
            ]],
            ['code' => 'EH', 'country' => 'Sahara occidental', 'cities' => [
                ['name' => 'Laâyoune', 'lat' => 27.1500, 'lng' => -13.1990, 'region' => 'Laâyoune-Sakia El Hamra'],
            ]],
        ];
    }

    protected function timezoneFor(string $cc): ?string
    {
        return [
            'DZ' => 'Africa/Algiers', 'AO' => 'Africa/Luanda', 'BJ' => 'Africa/Porto-Novo',
            'BW' => 'Africa/Gaborone', 'BF' => 'Africa/Ouagadougou', 'BI' => 'Africa/Bujumbura',
            'CM' => 'Africa/Douala', 'CV' => 'Atlantic/Cape_Verde', 'CF' => 'Africa/Bangui',
            'TD' => 'Africa/Ndjamena', 'KM' => 'Indian/Comoro', 'CG' => 'Africa/Brazzaville',
            'CD' => 'Africa/Lubumbashi', 'DJ' => 'Africa/Djibouti', 'EG' => 'Africa/Cairo',
            'GQ' => 'Africa/Malabo', 'ER' => 'Africa/Asmara', 'SZ' => 'Africa/Mbabane',
            'ET' => 'Africa/Addis_Ababa', 'GA' => 'Africa/Libreville', 'GM' => 'Africa/Banjul',
            'GH' => 'Africa/Accra', 'GN' => 'Africa/Conakry', 'GW' => 'Africa/Bissau',
            'CI' => 'Africa/Abidjan', 'KE' => 'Africa/Nairobi', 'LS' => 'Africa/Maseru',
            'LR' => 'Africa/Monrovia', 'LY' => 'Africa/Tripoli', 'MG' => 'Indian/Antananarivo',
            'MW' => 'Africa/Blantyre', 'ML' => 'Africa/Bamako', 'MR' => 'Africa/Nouakchott',
            'MU' => 'Indian/Mauritius', 'MA' => 'Africa/Casablanca', 'MZ' => 'Africa/Maputo',
            'NA' => 'Africa/Windhoek', 'NE' => 'Africa/Niamey', 'NG' => 'Africa/Lagos',
            'RW' => 'Africa/Kigali', 'ST' => 'Africa/Sao_Tome', 'SN' => 'Africa/Dakar',
            'SC' => 'Indian/Mahe', 'SL' => 'Africa/Freetown', 'SO' => 'Africa/Mogadishu',
            'ZA' => 'Africa/Johannesburg', 'SS' => 'Africa/Juba', 'SD' => 'Africa/Khartoum',
            'TZ' => 'Africa/Dar_es_Salaam', 'TG' => 'Africa/Lome', 'TN' => 'Africa/Tunis',
            'UG' => 'Africa/Kampala', 'ZM' => 'Africa/Lusaka', 'ZW' => 'Africa/Harare',
            'EH' => 'Africa/El_Aaiun',
        ][$cc] ?? null;
    }
}