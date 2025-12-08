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
        // Ajouter colonne fcm_token à la table users
        if (!Schema::hasColumn('users', 'fcm_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->text('fcm_token')->nullable()->comment('Firebase Cloud Messaging token');
            });
        }

        // Créer table pour les notifications experts
        Schema::create('expert_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Expert user');
            $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade')->comment('Related item');
            $table->string('type')->comment('Notification type: item_pending, item_verified, etc');
            $table->string('title')->comment('Notification title');
            $table->text('message')->comment('Notification message');
            $table->string('icon')->nullable()->comment('Font Awesome icon class');
            $table->string('action_url')->nullable()->comment('URL to navigate to');
            $table->boolean('read')->default(false)->index();
            $table->timestamp('read_at')->nullable();
            $table->json('data')->nullable()->comment('Extra JSON data');
            $table->timestamps();
            
            // Indexes pour les requêtes fréquentes
            $table->index(['user_id', 'read']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expert_notifications');
        
        if (Schema::hasColumn('users', 'fcm_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
};
