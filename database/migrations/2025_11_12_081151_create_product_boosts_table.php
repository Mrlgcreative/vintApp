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
        Schema::create('product_boosts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade'); // Référence vers la table items
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Le vendeur qui a acheté le boost
            
            // Type de boost
            $table->enum('boost_type', [
                'featured',     // Produit mis en avant (badge étoile)
                'top',          // Affiché en haut de liste
                'urgent',       // Badge urgent rouge
                'premium',      // Bordure dorée + badge premium
                'spotlight'     // Mis en avant dans un carrousel
            ]);
            
            // Durée et timing
            $table->integer('duration_hours'); // Durée en heures
            $table->datetime('starts_at');     // Date de début du boost
            $table->datetime('expires_at');    // Date d'expiration
            
            // Prix et paiement
            $table->decimal('price', 10, 2);   // Prix payé pour ce boost
            $table->string('currency', 3);     // USD ou CDF
            $table->string('payment_method')->nullable(); // airtel_money, mpesa, etc.
            $table->string('transaction_id')->nullable(); // ID de transaction de paiement
            
            // Statuts
            $table->enum('status', [
                'pending',      // En attente de paiement
                'active',       // Boost actif
                'expired',      // Expiré
                'cancelled'     // Annulé
            ])->default('pending');
            
            // Statistiques
            $table->integer('views_gained')->default(0);    // Vues supplémentaires générées
            $table->integer('clicks_gained')->default(0);   // Clics supplémentaires
            $table->decimal('sales_generated', 10, 2)->default(0); // Ventes générées
            
            // Métadonnées
            $table->json('metadata')->nullable(); // Données supplémentaires (couleurs, positions, etc.)
            $table->text('admin_notes')->nullable(); // Notes administratives
            
            $table->timestamps();
            
            // Index pour optimisation
            $table->index(['item_id', 'status']);
            $table->index(['boost_type', 'status']);
            $table->index(['expires_at', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_boosts');
    }
};
