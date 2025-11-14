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
        Schema::table('boost_types', function (Blueprint $table) {
            // Ajouter les colonnes manquantes pour la compatibilité
            $table->decimal('base_price', 10, 2)->default(0)->after('color');
            $table->decimal('price_per_day', 8, 2)->default(0)->after('base_price');
            $table->integer('min_duration')->default(1)->after('price_per_day');
            $table->integer('max_duration')->default(30)->after('min_duration');
            $table->json('benefits')->nullable()->after('max_duration');
            $table->boolean('is_premium')->default(false)->after('benefits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boost_types', function (Blueprint $table) {
            $table->dropColumn([
                'base_price',
                'price_per_day',
                'min_duration',
                'max_duration',
                'benefits',
                'is_premium'
            ]);
        });
    }
};
