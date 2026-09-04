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
        Schema::create('expositions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            // 'active' | 'paused' | 'ended'
            $table->enum('status', ['active', 'paused', 'ended'])->default('active');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Exposition -> produits exposés
        Schema::create('exposition_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exposition_id');
            $table->unsignedBigInteger('item_id');
            $table->foreign('exposition_id')->references('id')->on('expositions')->cascadeOnDelete();
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
            $table->unique(['exposition_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exposition_item');
        Schema::dropIfExists('expositions');
    }
};