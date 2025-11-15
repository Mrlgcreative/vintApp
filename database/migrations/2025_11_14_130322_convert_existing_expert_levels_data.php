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
        // Vérifier si la colonne existe déjà
        if (!Schema::hasColumn('expert_profiles', 'certification_level')) {
            return; // Skip si la colonne n'existe pas
        }

        // D'abord ajouter une nouvelle colonne temporaire
        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->enum('new_certification_level', ['junior', 'senior', 'master'])->nullable()->after('certification_level');
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

        // Supprimer l'ancienne colonne
        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->dropColumn('certification_level');
        });

        // Renommer et rendre non-nullable
        Schema::table('expert_profiles', function (Blueprint $table) {
            $table->renameColumn('new_certification_level', 'certification_level');
        });
        
        // Mettre à jour pour enlever les null et définir la valeur par défaut
        DB::statement("ALTER TABLE expert_profiles MODIFY certification_level ENUM('junior', 'senior', 'master') NOT NULL DEFAULT 'junior'");
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
