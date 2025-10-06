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
        Schema::create('users_waiting', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('country')->default('Congo (RDC)');
            $table->text('message')->nullable(); // Pourquoi ils veulent rejoindre
            $table->string('confirmation_token')->unique();
            $table->enum('status', ['pending', 'confirmed', 'approved', 'rejected', 'converted'])->default('pending');
            $table->timestamp('email_confirmed_at')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('converted_at')->nullable(); // Quand converti en user réel
            $table->foreignId('converted_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('admin_notes')->nullable(); // Notes admin
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performance
            $table->index('status');
            $table->index('email_confirmed_at');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_waiting');
    }
};
