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
        Schema::table('transactions', function (Blueprint $table) {
            // Ajouter la colonne currency si elle n'existe pas
            if (!Schema::hasColumn('transactions', 'currency')) {
                $table->enum('currency', ['USD', 'CDF'])->default('USD')->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
