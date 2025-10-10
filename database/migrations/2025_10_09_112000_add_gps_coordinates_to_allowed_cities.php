<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('allowed_cities', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('region');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('country_code', 3)->nullable()->after('country')->index();
            $table->integer('population')->nullable()->after('longitude');
            $table->string('timezone', 50)->nullable()->after('population');
        });

        // Mettre à jour les villes existantes avec les coordonnées GPS
        DB::table('allowed_cities')->whereNull('latitude')->update([
            'country_code' => 'COD', // République Démocratique du Congo
        ]);

        // Kinshasa
        DB::table('allowed_cities')->where('name', 'Kinshasa')->update([
            'latitude' => -4.3276,
            'longitude' => 15.3136,
            'country_code' => 'COD',
            'population' => 17000000,
            'timezone' => 'Africa/Kinshasa',
        ]);

        // Lubumbashi
        DB::table('allowed_cities')->where('name', 'Lubumbashi')->update([
            'latitude' => -11.6795,
            'longitude' => 27.4794,
            'country_code' => 'COD',
            'population' => 2800000,
            'timezone' => 'Africa/Lubumbashi',
        ]);

        // Mbuji-Mayi
        DB::table('allowed_cities')->where('name', 'Mbuji-Mayi')->update([
            'latitude' => -6.1200,
            'longitude' => 23.5900,
            'country_code' => 'COD',
            'population' => 3500000,
            'timezone' => 'Africa/Lubumbashi',
        ]);

        // Kananga
        DB::table('allowed_cities')->where('name', 'Kananga')->update([
            'latitude' => -5.8967,
            'longitude' => 22.4169,
            'country_code' => 'COD',
            'population' => 1500000,
            'timezone' => 'Africa/Lubumbashi',
        ]);

        // Kisangani
        DB::table('allowed_cities')->where('name', 'Kisangani')->update([
            'latitude' => 0.5150,
            'longitude' => 25.1908,
            'country_code' => 'COD',
            'population' => 1800000,
            'timezone' => 'Africa/Lubumbashi',
        ]);

        // Goma
        DB::table('allowed_cities')->where('name', 'Goma')->update([
            'latitude' => -1.6792,
            'longitude' => 29.2228,
            'country_code' => 'COD',
            'population' => 1000000,
            'timezone' => 'Africa/Lubumbashi',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('allowed_cities', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'country_code', 'population', 'timezone']);
        });
    }
};
