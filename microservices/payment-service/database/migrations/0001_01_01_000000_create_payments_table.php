<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();

            // Clés métier vers les autres services : jamais de FK.
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('wallet_id')->nullable();

            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('CDF');
            $table->string('method');
            $table->string('provider_key', 32)->default('unknown');
            $table->string('status')->default('pending');
            $table->text('designation')->nullable();

            // Références côté opérateur
            $table->string('reference', 64)->nullable()->index();
            $table->string('external_reference', 128)->nullable()->index();
            $table->string('phone_number', 32)->nullable();

            $table->json('metadata')->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'provider_key']);
            $table->index(['order_id', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
