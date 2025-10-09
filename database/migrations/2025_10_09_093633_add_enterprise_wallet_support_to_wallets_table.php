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
        // Rendre user_id nullable pour les wallets système (entreprise)
        DB::statement('ALTER TABLE wallets MODIFY user_id BIGINT UNSIGNED NULL');
        
        // Modifier l'ENUM type pour ajouter 'enterprise'
        DB::statement("ALTER TABLE wallets MODIFY COLUMN type ENUM('main', 'pending', 'enterprise') DEFAULT 'main'");
        
        Schema::table('wallets', function (Blueprint $table) {
            // Ajouter le taux de commission (pour le wallet entreprise) si n'existe pas
            if (!Schema::hasColumn('wallets', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(5.00)->after('balance')
                    ->comment('Taux de commission en pourcentage (ex: 5.00 = 5%)');
            }
        });

        // Vérifier et supprimer la contrainte unique si elle existe
        try {
            DB::statement('ALTER TABLE wallets DROP INDEX wallets_user_id_currency_unique');
        } catch (\Exception $e) {
            // La contrainte n'existe peut-être pas, on ignore l'erreur
        }

        // Créer le wallet entreprise global en USD s'il n'existe pas
        if (!DB::table('wallets')->where('type', 'enterprise')->where('currency', 'USD')->exists()) {
            DB::table('wallets')->insert([
                'user_id' => null,
                'type' => 'enterprise',
                'currency' => 'USD',
                'balance' => 0.00,
                'commission_rate' => 5.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Créer aussi un wallet entreprise en CDF s'il n'existe pas
        if (!DB::table('wallets')->where('type', 'enterprise')->where('currency', 'CDF')->exists()) {
            DB::table('wallets')->insert([
                'user_id' => null,
                'type' => 'enterprise',
                'currency' => 'CDF',
                'balance' => 0.00,
                'commission_rate' => 5.00,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Supprimer les wallets entreprise
            DB::table('wallets')->where('type', 'enterprise')->delete();
            
            // Retirer les colonnes ajoutées
            $table->dropColumn(['type', 'commission_rate']);
            
            // Remettre user_id non nullable
            $table->foreignId('user_id')->nullable(false)->change();
            
            // Remettre la contrainte unique
            $table->unique(['user_id', 'currency']);
        });
    }
};
