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
            $table->string('order_number')->unique()->after('id');
            $table->string('currency', 3)->default('FC')->after('total_amount');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_phone')->nullable()->after('shipping_city');
            $table->text('notes')->nullable()->after('shipping_phone');
            $table->timestamp('paid_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'order_number',
                'currency',
                'shipping_city',
                'shipping_phone',
                'notes',
                'paid_at'
            ]);
        });
    }
};
