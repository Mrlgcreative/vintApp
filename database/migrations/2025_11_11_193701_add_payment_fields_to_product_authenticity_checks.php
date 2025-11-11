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
        Schema::table('product_authenticity_checks', function (Blueprint $table) {
            $table->timestamp('payment_completed_at')->nullable()->after('payment_completed');
            $table->string('payment_method')->nullable()->after('payment_completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_authenticity_checks', function (Blueprint $table) {
            $table->dropColumn(['payment_completed_at', 'payment_method']);
        });
    }
};
