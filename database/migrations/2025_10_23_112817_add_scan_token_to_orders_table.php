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
        Schema::table('orders', function (Blueprint $table) {
            // Token unique pour le scan QR code
            $table->string('scan_token', 64)->unique()->nullable()->after('tracking_number');
            $table->timestamp('scanned_at')->nullable()->after('scan_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['scan_token', 'scanned_at']);
        });
    }
};
