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
        // D'abord ajouter une nouvelle colonne temporaire
        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->enum('new_certification_level', ['junior', 'senior', 'master'])->default('junior')->after('certification_level');
        });

        // Convertir les données existantes
        DB::table('expert_profiles')->update([
            'new_certification_level' => DB::raw("CASE 
                WHEN certification_level = 'bronze' THEN 'junior'
                WHEN certification_level = 'silver' THEN 'senior'
                WHEN certification_level = 'gold' THEN 'master'
                ELSE 'junior'
            END")
        ]);

        // Supprimer l'ancienne colonne et renommer la nouvelle
        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->dropColumn('certification_level');
        });

        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->renameColumn('new_certification_level', 'certification_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Faire l'inverse pour la rollback
        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->enum('old_certification_level', ['bronze', 'silver', 'gold'])->default('bronze')->after('certification_level');
        });

        DB::table('expert_profiles')->update([
            'old_certification_level' => DB::raw("CASE 
                WHEN certification_level = 'junior' THEN 'bronze'
                WHEN certification_level = 'senior' THEN 'silver'
                WHEN certification_level = 'master' THEN 'gold'
                ELSE 'bronze'
            END")
        ]);

        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->dropColumn('certification_level');
        });

        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->renameColumn('old_certification_level', 'certification_level');
        });
    }
};
