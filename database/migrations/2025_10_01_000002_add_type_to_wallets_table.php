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
        Schema::table('wallets', function (Blueprint $table) {
            // Ajout du type de wallet
            $table->enum('type', ['main', 'pending'])->default('main')->after('currency');
            
            // Ajout d'index pour optimiser les requêtes
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'currency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropIndex(['user_id', 'type']);
            $table->dropIndex(['user_id', 'currency']);
        });
    }
};