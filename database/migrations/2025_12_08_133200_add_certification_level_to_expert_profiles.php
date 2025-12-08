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
        Schema::table('expert_profiles', function (Blueprint $table) {
            // Ajouter la colonne certification_level si elle n'existe pas
            if (!Schema::hasColumn('expert_profiles', 'certification_level')) {
                $table->enum('certification_level', ['junior', 'senior', 'master'])->default('junior')->after('specialties');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('expert_profiles', 'certification_level')) {
                $table->dropColumn('certification_level');
            }
        });
    }
};
