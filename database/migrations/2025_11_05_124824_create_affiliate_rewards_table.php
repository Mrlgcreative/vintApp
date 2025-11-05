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
        Schema::create('affiliate_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['points', 'cash', 'badge', 'level_boost', 'custom']);
            $table->decimal('value', 10, 2)->default(0);
            $table->text('description');
            $table->text('reason')->nullable();
            $table->boolean('is_public')->default(false);
            $table->json('metadata')->nullable();
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'status']);
            $table->index(['type', 'created_at']);
            $table->index(['is_public', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_rewards');
    }
};
