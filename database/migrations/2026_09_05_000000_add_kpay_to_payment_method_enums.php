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
        // Ajouter 'kpay' comme méthode de décaissement (payout)
        DB::statement("ALTER TABLE `withdrawal_requests` MODIFY COLUMN `payment_method` ENUM('orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash', 'maishapay', 'cinetpay', 'kpay', 'agent') NOT NULL");

        // Ajouter 'kpay' comme méthode d'encaissement (payin)
        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `payment_method` ENUM('wallet', 'airtel_money', 'orange_money', 'mpesa', 'afrimoney', 'bank', 'kpay') NOT NULL DEFAULT 'orange_money'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `withdrawal_requests` MODIFY COLUMN `payment_method` ENUM('orange_money', 'airtel_money', 'mpesa', 'africell', 'illicocash', 'maishapay', 'cinetpay', 'agent') NOT NULL");

        DB::statement("ALTER TABLE `transactions` MODIFY COLUMN `payment_method` ENUM('wallet', 'airtel_money', 'orange_money', 'mpesa', 'afrimoney', 'bank') NOT NULL DEFAULT 'orange_money'");
    }
};
