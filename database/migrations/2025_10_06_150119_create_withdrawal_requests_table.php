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
        Schema::create('withdrawal_requests', function (Blueprint $table) {
            $table->id();
            
            // Référence à la transaction wallet
            $table->foreignId('wallet_transaction_id')
                  ->constrained('wallet_transactions')
                  ->onDelete('cascade');
            
            // Informations de retrait
            $table->string('phone_number', 20);
            $table->enum('payment_method', [
                'orange_money',
                'airtel_money',
                'mpesa',
                'africell',
                'illicocash'
            ]);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('CDF');
            
            // Statut du retrait
            $table->enum('status', [
                'pending',      // En attente d'initiation
                'processing',   // Envoyé au provider
                'completed',    // Complété avec succès
                'failed',       // Échec
                'cancelled'     // Annulé manuellement
            ])->default('pending');
            
            // Réponses du provider
            $table->string('provider_reference')->nullable();
            $table->json('provider_response')->nullable();
            
            // Métadonnées
            $table->text('failure_reason')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index('status');
            $table->index('payment_method');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawal_requests');
    }
};
