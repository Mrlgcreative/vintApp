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
        Schema::table('payments', function (Blueprint $table) {
            // Modifier la colonne method pour supporter CinetPay et AfribaPay
            $table->string('method', 50)->default('stripe')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revenir à l'enum original
            $table->enum('method', ['stripe', 'mobile_money', 'bank_transfer', 'cash'])->default('stripe')->change();
        });
    }
};
