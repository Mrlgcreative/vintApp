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
        // Modifier l'enum status pour ajouter 'pending_verification'
        DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('active', 'inactive', 'sold', 'pending', 'pending_verification') DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre les items pending_verification en pending
        DB::statement("UPDATE items SET status = 'pending' WHERE status = 'pending_verification'");
        
        // Revenir à l'ancien enum
        DB::statement("ALTER TABLE items MODIFY COLUMN status ENUM('active', 'inactive', 'sold', 'pending') DEFAULT 'active'");
    }
};
