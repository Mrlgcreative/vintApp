<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();

            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->string('currency', 3)->default('USD');
            $table->string('reason')->nullable();
            $table->string('status')->default('pending');
            $table->string('external_reference', 128)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('processed_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();
            // Clé de déduplication : un opérateur qui rejoue un webhook ne doit
            // jamais déclencher deux effets.
            $table->string('dedupe_key', 64)->unique();
            $table->string('provider', 32);
            $table->foreignId('payment_callback_id')->nullable();
            $table->timestamp('processed_at');
            $table->timestamps();

            $table->index('provider');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_webhook_events');
        Schema::dropIfExists('refunds');
    }
};
