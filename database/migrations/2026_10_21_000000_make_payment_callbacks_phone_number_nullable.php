<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Le téléphone est optionnel selon l'évènement (ex. webhooks K-PAY
     * payout ou refund sans phoneNumber). En NOT NULL, l'insertion du
     * callback échouait et l'évènement était perdu.
     */
    public function up(): void
    {
        Schema::table('payment_callbacks', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_callbacks', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable(false)->change();
        });
    }
};
