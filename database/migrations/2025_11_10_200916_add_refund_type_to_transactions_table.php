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
        // Modifier l'enum type pour ajouter 'refund'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('deposit', 'withdraw', 'transfer', 'purchase', 'refund') DEFAULT 'deposit'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à l'ancien enum sans 'refund'
        DB::statement("ALTER TABLE transactions MODIFY COLUMN type ENUM('deposit', 'withdraw', 'transfer', 'purchase') DEFAULT 'deposit'");
    }
};
