<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modifier l'ENUM pour ajouter 'cinetpay' comme méthode de décaissement
        DB::statement("ALTER TABLE `withdrawal_requests` MODIFY COLUMN `payment_method` ENUM('orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash', 'maishapay', 'cinetpay', 'agent') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre l'ancien ENUM
        DB::statement("ALTER TABLE `withdrawal_requests` MODIFY COLUMN `payment_method` ENUM('orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash', 'maishapay', 'agent') NOT NULL");
    }
};
