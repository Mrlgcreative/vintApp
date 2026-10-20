<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * K-PAY n'inclut pas toujours `currency` dans ses webhooks (ex.
     * payment.completed). En NOT NULL, l'insertion du callback échouait et
     * l'évènement était perdu. Le payload brut reste disponible dans
     * `parsed_data` / `raw_payload` pour retrouver la devise si besoin.
     */
    public function up(): void
    {
        Schema::table('payment_callbacks', function (Blueprint $table) {
            $table->string('currency', 3)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payment_callbacks', function (Blueprint $table) {
            $table->string('currency', 3)->nullable(false)->default('USD')->change();
        });
    }
};
