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
        Schema::create('point_conversion_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3); // USD, CDF
            $table->decimal('points_per_unit', 10, 2); // Combien de points pour 1 unité de monnaie
            $table->decimal('minimum_conversion', 10, 2)->default(100); // Minimum de points à convertir
            $table->decimal('maximum_conversion', 15, 2)->nullable(); // Maximum (null = illimité)
            $table->decimal('conversion_fee_percentage', 5, 2)->default(0); // Frais de conversion en %
            $table->decimal('conversion_fee_fixed', 10, 2)->default(0); // Frais fixes
            $table->boolean('is_active')->default(true);
            $table->json('conditions')->nullable(); // Conditions spéciales (JSON)
            $table->timestamp('effective_from'); // Quand ce taux devient effectif
            $table->timestamp('effective_until')->nullable(); // Quand il expire
            $table->text('notes')->nullable(); // Notes administratives
            $table->timestamps();

            // Index et contraintes
            $table->index(['currency', 'is_active', 'effective_from']);
            $table->index(['effective_from', 'effective_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_conversion_rates');
    }
};