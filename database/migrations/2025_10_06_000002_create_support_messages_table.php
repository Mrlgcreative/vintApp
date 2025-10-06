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
        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_chat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Qui a envoyé le message
            $table->text('message'); // Le contenu du message
            $table->json('attachments')->nullable(); // Fichiers joints éventuels
            $table->boolean('is_admin')->default(false); // Indique si c'est un message d'admin
            $table->boolean('is_read')->default(false); // Si le message a été lu
            $table->timestamp('read_at')->nullable(); // Quand le message a été lu
            $table->timestamps();
            
            $table->index(['support_chat_id', 'created_at']);
            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_messages');
    }
};