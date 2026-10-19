<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Les articles analysés par IA avant ce changement gardent le score heuristique
 * local dans `verification_score`, alors que le verdict du modèle vision est
 * stocké dans verification_details['ai']['score']. On aligne le score affiché
 * (fiche article, notifications, VintPass, push) sur le verdict IA réel.
 *
 * Seules les lignes ayant déjà un verdict IA sont touchées.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('items')
            ->whereRaw("JSON_EXTRACT(verification_details, '$.ai.score') IS NOT NULL")
            ->update([
                'verification_score' => DB::raw("JSON_EXTRACT(verification_details, '$.ai.score')"),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Les anciens scores heuristiques ne sont pas conservés : rien à restaurer.
    }
};
