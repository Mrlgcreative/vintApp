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
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('unsubscribe_token')->unique();
            $table->boolean('is_active')->default(true);
            $table->boolean('email_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            
            // Préférences d'abonnement
            $table->boolean('receive_welcome')->default(true);
            $table->boolean('receive_new_items')->default(true);
            $table->boolean('receive_promotions')->default(true);
            $table->boolean('receive_newsletters')->default(true);
            
            // Statistiques
            $table->integer('emails_sent')->default(0);
            $table->integer('emails_opened')->default(0);
            $table->integer('emails_clicked')->default(0);
            $table->timestamp('last_email_sent_at')->nullable();
            
            $table->timestamps();
            
            $table->index('email');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
