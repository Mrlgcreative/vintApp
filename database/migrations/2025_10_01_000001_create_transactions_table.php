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
        if (Schema::hasTable('transactions')) {
            return;
        }
        
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('currency', ['USD', 'CDF']);
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])
                  ->default('pending');
            $table->enum('type', ['deposit', 'withdraw', 'transfer', 'purchase'])
                  ->default('deposit');
            $table->enum('payment_method', [
                'wallet',
                'airtel_money',
                'orange_money',
                'mpesa',
                'afrimoney',
                'bank'
            ]);
            $table->string('transaction_ref')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            // Index pour améliorer les performances des requêtes
            $table->index(['user_id', 'status']);
            $table->index(['wallet_id', 'status']);
            $table->index('transaction_ref');
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};