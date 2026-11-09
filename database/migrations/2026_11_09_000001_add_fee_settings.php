<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Supplément acheteur : 4,5 % ajouté au prix affiché à l'acheteur.
        // Ne pas écraser une valeur personnalisée ; reprendre seulement si absente ou ancienne valeur 5 %.
        $current = DB::table('settings')->where('key', 'encaissement_fee_percentage')->value('value');
        if ($current === null || (float) $current === 5.0) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'encaissement_fee_percentage'],
                [
                    'value' => '4.5',
                    'type' => 'float',
                    'category' => 'payment',
                    'label' => 'Supplément acheteur (%)',
                    'description' => "Supplément de 4,5 % ajouté au prix affiché à l'acheteur pour couvrir les frais d'encaissement de l'agrégateur. Jamais facturé au vendeur.",
                    'is_public' => false,
                    'is_encrypted' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Frais de retrait de l'agrégateur : la plateforme ne prélève rien ;
        // le vendeur reçoit le montant net de ces frais (jamais les frais d'encaissement).
        if (DB::table('settings')->where('key', 'withdrawal_fee_percentage')->doesntExist()) {
            DB::table('settings')->insert([
                'key' => 'withdrawal_fee_percentage',
                'value' => '0',
                'type' => 'float',
                'category' => 'payment',
                'label' => 'Frais de retrait de l\'agrégateur (%)',
                'description' => "Frais prélevés par l'agrégateur sur chaque retrait. Le vendeur reçoit le montant net de ces frais. Les frais d'encaissement ne sont jamais déduits du retrait.",
                'is_public' => false,
                'is_encrypted' => false,
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
        DB::table('settings')->whereIn('key', [
            'encaissement_fee_percentage',
            'withdrawal_fee_percentage',
        ])->delete();
    }
};
