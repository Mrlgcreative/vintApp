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
        Schema::table('transactions', function (Blueprint $table) {
            // Ajouter user_id (alias de buyer_id)
            if (!Schema::hasColumn('transactions', 'user_id')) {
                // Copier les valeurs de buyer_id vers user_id
                $table->unsignedBigInteger('user_id')->after('id')->nullable();
                
                // Après l'exécution, on va copier les données de buyer_id
                // vers user_id dans la méthode up
            }
            
            // Ajouter wallet_id
            if (!Schema::hasColumn('transactions', 'wallet_id')) {
                $table->foreignId('wallet_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            }
            
            // Ajouter currency
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->enum('currency', ['USD', 'CDF'])->default('USD')->after('amount');
            }
            
            // Modifier status pour enum
            if (Schema::hasColumn('transactions', 'status')) {
                // Laravel ne peut pas modifier directement un enum, on va d'abord le supprimer
                DB::statement('ALTER TABLE transactions MODIFY status VARCHAR(20)');
            }
            
            // Ajouter type
            if (!Schema::hasColumn('transactions', 'type')) {
                $table->enum('type', ['deposit', 'withdraw', 'transfer', 'purchase'])->default('deposit')->after('status');
            }
            
            // Ajouter payment_method
            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->enum('payment_method', [
                    'wallet',
                    'airtel_money',
                    'orange_money',
                    'mpesa',
                    'afrimoney',
                    'bank'
                ])->default('orange_money')->after('type');
            }
            
            // Ajouter phone
            if (!Schema::hasColumn('transactions', 'phone')) {
                $table->string('phone')->nullable()->after('provider');
            }
        });
        
        // Copier les données de buyer_id vers user_id
        if (Schema::hasColumn('transactions', 'buyer_id') && Schema::hasColumn('transactions', 'user_id')) {
            DB::statement('UPDATE transactions SET user_id = buyer_id WHERE user_id IS NULL');
            
            // Ajouter la contrainte de foreign key après avoir copié les données
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            if (Schema::hasColumn('transactions', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('transactions', 'wallet_id')) {
                $table->dropForeign(['wallet_id']);
                $table->dropColumn('wallet_id');
            }
            
            if (Schema::hasColumn('transactions', 'currency')) {
                $table->dropColumn('currency');
            }
            
            if (Schema::hasColumn('transactions', 'type')) {
                $table->dropColumn('type');
            }
            
            if (Schema::hasColumn('transactions', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            
            if (Schema::hasColumn('transactions', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
