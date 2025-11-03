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
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_id', 50)->unique(); // ID unique de transaction
            $table->enum('type', [
                'earn_referral',      // Gagner par parrainage
                'earn_signup_bonus',  // Bonus d'inscription
                'earn_purchase',      // Gagner par achat
                'earn_sale',          // Gagner par vente
                'earn_review',        // Gagner par avis
                'earn_daily_login',   // Connexion quotidienne
                'earn_social_share',  // Partage sur réseaux sociaux
                'earn_profile_complete', // Profil complété
                'earn_bonus',         // Bonus administrateur
                'redeem_cash',        // Conversion en argent
                'redeem_discount',    // Utilisation pour réduction
                'expire',             // Points expirés
                'refund',             // Remboursement
                'adjustment'          // Ajustement administrateur
            ]);
            $table->decimal('amount', 15, 2); // Montant (+ ou -)
            $table->decimal('balance_before', 15, 2); // Solde avant
            $table->decimal('balance_after', 15, 2); // Solde après
            $table->string('description', 500); // Description de la transaction
            $table->string('reference_type')->nullable(); // Type de référence (Order, Item, etc.)
            $table->bigInteger('reference_id')->nullable(); // ID de référence
            $table->json('metadata')->nullable(); // Données additionnelles (JSON)
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->timestamp('processed_at')->nullable(); // Quand traité
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index(['status', 'processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};