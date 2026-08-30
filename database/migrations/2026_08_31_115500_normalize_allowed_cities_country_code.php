<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Normalise country_code en ISO alpha-2 (les lignes historiques contenaient
     * du alpha-3 : 'COD' provenant du seed et de la migration GPS initiale).
     */
    public function up(): void
    {
        if (Schema::hasTable('allowed_cities') && Schema::hasColumn('allowed_cities', 'country_code')) {
            DB::table('allowed_cities')
                ->whereIn('country_code', ['COD', 'ZR'])
                ->update(['country_code' => 'CD']);
        }
    }

    public function down(): void
    {
        // Normalisation irréversible (impossible de distinguer 'CD' natif de 'COD' converti).
    }
};