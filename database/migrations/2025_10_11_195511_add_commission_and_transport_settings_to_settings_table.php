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
        // Ajouter les paramètres de commission et transport dans la table settings
        DB::table('settings')->insert([
            [
                'key' => 'platform_commission_percentage',
                'value' => '10', // 10% par défaut
                'type' => 'decimal',
                'category' => 'paiement',
                'label' => 'Commission Plateforme (%)',
                'description' => 'Pourcentage de commission de la plateforme sur chaque vente',
                'is_public' => false,
                'is_encrypted' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'transport_fee_percentage',
                'value' => '5', // 5% par défaut
                'type' => 'decimal',
                'category' => 'paiement',
                'label' => 'Frais de Transport (%)',
                'description' => 'Pourcentage des frais de transport sur chaque vente',
                'is_public' => false,
                'is_encrypted' => false,
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
        // Supprimer les paramètres ajoutés
        DB::table('settings')->whereIn('key', [
            'platform_commission_percentage',
            'transport_fee_percentage'
        ])->delete();
    }
};
