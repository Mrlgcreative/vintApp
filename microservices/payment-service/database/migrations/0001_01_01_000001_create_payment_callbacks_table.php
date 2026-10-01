<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_callbacks', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();

            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->string('provider', 32);
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('currency', 3)->default('USD');
            $table->string('phone_number', 32)->nullable();
            $table->string('callback_type')->default('webhook');

            $table->string('external_transaction_id', 128)->nullable()->index();
            $table->string('reference', 64)->nullable()->index();

            $table->json('raw_payload');
            $table->json('parsed_data')->nullable();
            $table->text('signature')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->text('processing_error')->nullable();
            $table->unsignedSmallInteger('retry_count')->default(0);

            $table->timestamps();

            $table->index(['provider', 'status']);
            $table->index('is_processed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_callbacks');
    }
};
