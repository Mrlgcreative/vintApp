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
        Schema::table('payments', function (Blueprint $table) {
            // Ajouter user_id pour supporter les rechargements wallet (sans buyer/seller)
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');
            
            // Rendre buyer_id et seller_id nullable pour les rechargements wallet
            $table->foreignId('buyer_id')->nullable()->change();
            $table->foreignId('seller_id')->nullable()->change();
            $table->foreignId('order_id')->nullable()->change();
            
            // Colonnes CinetPay spécifiques
            $table->string('designation')->nullable()->after('amount');
            $table->string('cpm_trans_id')->nullable()->after('transaction_id')->index();
            $table->string('cpm_result')->nullable()->after('cpm_trans_id');
            $table->string('cpm_trans_status')->nullable()->after('cpm_result');
            $table->string('payment_token')->nullable()->after('cpm_trans_status');
            $table->decimal('cpm_amount', 15, 2)->nullable()->after('payment_token');
            
            // Métadonnées et informations supplémentaires
            $table->text('metadata')->nullable()->after('payment_details');
            $table->text('error_message')->nullable()->after('metadata');
            $table->ipAddress('ip_address')->nullable()->after('error_message');
            
            // Modifier l'enum method pour inclure 'cinetpay'
            $table->enum('method', ['stripe', 'mobile_money', 'bank_transfer', 'cash', 'cinetpay', 'card', 'wallet'])->default('cinetpay')->change();
            
            // Modifier l'enum status pour inclure 'cancelled'
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending')->change();
            
            // Index pour optimisation
            $table->index(['user_id', 'status']);
            $table->index(['order_id', 'status']);
        });

        // Ajouter les colonnes payment à la table orders si elles n'existent pas
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'payment_transaction_id')) {
                $table->string('payment_transaction_id')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_transaction_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'designation',
                'cpm_trans_id',
                'cpm_result',
                'cpm_trans_status',
                'payment_token',
                'cpm_amount',
                'metadata',
                'error_message',
                'ip_address',
            ]);
            
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['order_id', 'status']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_transaction_id', 'payment_status']);
        });
    }
};
