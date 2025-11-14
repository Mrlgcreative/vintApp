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
            // Modifier l'enum pour utiliser les nouveaux niveaux
            $table->enum('certification_level', ['junior', 'senior', 'master'])->default('junior')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expert_profiles', function (Blueprint $table) {
            // Revenir aux anciens niveaux
            $table->enum('certification_level', ['bronze', 'silver', 'gold'])->default('bronze')->change();
        });
    }
};
