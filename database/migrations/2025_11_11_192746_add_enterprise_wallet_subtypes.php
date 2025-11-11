<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            // Ajouter le champ subtype pour distinguer les types de wallets entreprise
            $table->string('subtype')->nullable()->after('type');
        });

        // Créer les sous-wallets entreprise par défaut
        DB::table('wallets')->insert([
            // Sous-wallets USD
            [
                'user_id' => null,
                'currency' => 'USD',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'commission',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'currency' => 'USD',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'transport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'currency' => 'USD',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'boost',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'currency' => 'USD',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'verification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Sous-wallets CDF
            [
                'user_id' => null,
                'currency' => 'CDF',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'commission',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'currency' => 'CDF',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'transport',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'currency' => 'CDF',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'boost',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'currency' => 'CDF',
                'balance' => 0.00,
                'is_active' => true,
                'type' => 'enterprise',
                'subtype' => 'verification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les sous-wallets entreprise créés
        DB::table('wallets')->where('type', 'enterprise')
            ->whereNotNull('subtype')
            ->delete();

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('subtype');
        });
    }
};
