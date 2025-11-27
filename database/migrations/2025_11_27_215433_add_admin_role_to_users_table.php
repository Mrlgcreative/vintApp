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
        Schema::table('users', function (Blueprint $table) {
            // Ajouter le champ admin_role (boolean, défaut false)
            $table->boolean('admin_role')->default(false)->after('email');
            
            // Index pour améliorer les performances des requêtes WHERE admin_role = 1
            $table->index('admin_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['admin_role']);
            $table->dropColumn('admin_role');
        });
    }
};
