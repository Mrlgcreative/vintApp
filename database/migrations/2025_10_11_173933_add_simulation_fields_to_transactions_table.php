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
            if (!Schema::hasColumn('transactions', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->unique();
            }
            if (!Schema::hasColumn('transactions', 'provider')) {
                $table->string('provider')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('transactions', 'purpose')) {
                $table->string('purpose')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['transaction_id', 'provider', 'phone', 'purpose']);
        });
    }
};
