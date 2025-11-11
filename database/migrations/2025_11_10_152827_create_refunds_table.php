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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_id')->nullable(); // Transaction originale
            $table->string('refund_transaction_id')->nullable(); // Transaction de remboursement
            $table->decimal('refund_amount', 10, 2);
            $table->decimal('original_amount', 10, 2);
            $table->decimal('counter_offer_amount', 10, 2)->nullable();
            $table->string('currency', 3);
            $table->text('reason'); // Raison du remboursement
            $table->enum('refund_type', ['partial', 'full'])->default('full');
            $table->enum('status', ['pending', 'approved', 'rejected', 'negotiation', 'completed'])->default('pending');
            $table->json('evidence_photos')->nullable(); // Photos de preuve
            $table->text('admin_notes')->nullable(); // Notes de l'admin/vendeur
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Index pour les performances
            $table->index(['status', 'created_at']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
