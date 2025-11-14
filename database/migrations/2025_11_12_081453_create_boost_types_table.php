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
        Schema::create('boost_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // featured, top, urgent, premium, spotlight
            $table->string('display_name'); // Nom affiché à l'utilisateur
            $table->text('description'); // Description du boost
            $table->string('icon')->nullable(); // Icône FontAwesome
            $table->string('color')->default('#3B82F6'); // Couleur du badge
            
            // Tarification
            $table->decimal('price_usd', 8, 2); // Prix en USD
            $table->decimal('price_cdf', 10, 0); // Prix en CDF
            
            // Durées disponibles (en heures)
            $table->json('available_durations'); // [24, 48, 72, 168] par exemple
            
            // Configuration visuelle
            $table->json('visual_config'); // Configuration d'affichage
            
            // Statut et ordre
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('max_concurrent')->default(1); // Nombre max de ce boost par produit
            
            // Méta
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boost_types');
    }
};
