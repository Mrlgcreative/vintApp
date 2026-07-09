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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('item_id');
            $table->string('item_name');
            $table->decimal('price', 12, 2);
            $table->string('currency', 10)->default('CDF');
            $table->integer('quantity')->default(1);
            $table->string('image')->nullable();
            $table->decimal('original_price', 12, 2)->nullable();
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->boolean('has_discount')->default(false);
            $table->timestamps();

            $table->unique(['session_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
