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
        Schema::create('payment_callbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('cascade');
            $table->string('external_transaction_id')->nullable()->index(); // ID de la transaction chez l'opérateur
            $table->string('provider'); // orange_money, mpesa, airtel_money, etc.
            $table->string('status'); // success, failed, pending, cancelled
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('phone_number', 20);
            $table->string('callback_type')->default('ipn'); // ipn, webhook, polling
            $table->text('raw_payload'); // Données brutes reçues de l'opérateur
            $table->json('parsed_data')->nullable(); // Données parsées et structurées
            $table->string('signature')->nullable(); // Signature de sécurité si fournie
            $table->string('ip_address', 45)->nullable(); // IP de l'opérateur
            $table->boolean('is_verified')->default(false); // Signature/IP vérifiée
            $table->boolean('is_processed')->default(false); // Callback traité
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable(); // Erreur lors du traitement
            $table->integer('retry_count')->default(0); // Nombre de tentatives de traitement
            $table->timestamps();
            
            // Index pour optimiser les recherches
            $table->index(['provider', 'status']);
            $table->index('is_processed');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_callbacks');
    }
};
