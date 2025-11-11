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
        // Modifier l'énumération pour inclure 'proposed' au lieu de 'pending'
        DB::statement("ALTER TABLE local_deliveries MODIFY COLUMN status ENUM('proposed', 'accepted', 'in_transit', 'delivered', 'cancelled') DEFAULT 'proposed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retourner à l'ancienne énumération
        DB::statement("ALTER TABLE local_deliveries MODIFY COLUMN status ENUM('pending', 'accepted', 'in_transit', 'delivered', 'cancelled') DEFAULT 'pending'");
    }
};
