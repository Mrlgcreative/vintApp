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
        // Modifier l'enum du status pour ajouter 'pending_verification'
        Schema::table('items', function (Blueprint $table) {
            // Changer le type de la colonne status pour inclure 'pending_verification'
            $table->enum('status', ['active', 'inactive', 'sold', 'pending', 'pending_verification'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Revenir à l'ancienne enum
            $table->enum('status', ['active', 'inactive', 'sold', 'pending'])->default('active')->change();
        });
    }
};
