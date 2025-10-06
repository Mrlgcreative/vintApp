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
        Schema::create('support_chats', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // Référence unique pour le chat (ex: SUP-2025-001)
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Utilisateur qui demande de l'aide
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // Admin qui gère le support
            $table->string('subject')->nullable(); // Sujet de la demande
            $table->enum('status', ['open', 'in_progress', 'waiting_user', 'closed'])->default('open');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('category', ['technical', 'account', 'payment', 'order', 'general'])->default('general');
            $table->timestamp('last_message_at')->nullable(); // Timestamp du dernier message
            $table->timestamp('closed_at')->nullable(); // Quand la conversation a été fermée
            $table->json('metadata')->nullable(); // Données supplémentaires (navigateur, OS, etc.)
            $table->timestamps();
            
            $table->index(['status', 'priority']);
            $table->index(['user_id', 'status']);
            $table->index(['admin_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_chats');
    }
};