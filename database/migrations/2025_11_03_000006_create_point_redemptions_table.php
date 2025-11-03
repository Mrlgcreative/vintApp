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
        Schema::create('point_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('redemption_id', 50)->unique(); // ID unique de rachat
            $table->enum('type', ['cash_conversion', 'discount_code', 'gift_card', 'special_offer']);
            $table->decimal('points_used', 15, 2); // Points utilisés
            $table->string('currency', 3)->nullable(); // Devise (pour cash_conversion)
            $table->decimal('cash_amount', 15, 2)->nullable(); // Montant en argent
            $table->decimal('conversion_rate', 10, 2)->nullable(); // Taux utilisé
            $table->decimal('fees_charged', 10, 2)->default(0); // Frais appliqués
            $table->string('redemption_code', 100)->nullable(); // Code de réduction généré
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('description'); // Description du rachat
            $table->json('details')->nullable(); // Détails additionnels (JSON)
            $table->foreignId('processed_by')->nullable()->constrained('users'); // Admin qui a traité
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Pour codes de réduction
            $table->text('failure_reason')->nullable(); // Raison d'échec
            $table->timestamps();

            // Index pour les requêtes
            $table->index(['user_id', 'status']);
            $table->index(['redemption_id']);
            $table->index(['type', 'status']);
            $table->index(['processed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_redemptions');
    }
};