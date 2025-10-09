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
        Schema::create('allowed_cities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom de la ville (ex: Kinshasa, Lubumbashi)
            $table->string('country')->default('Congo (RDC)'); // Pays
            $table->string('region')->nullable(); // Province/Région
            $table->string('city_code')->nullable()->unique(); // Code unique de la ville
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Index pour performance
            $table->index(['name', 'country']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allowed_cities');
    }
};
