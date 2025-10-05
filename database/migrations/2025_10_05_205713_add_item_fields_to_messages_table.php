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
        Schema::table('messages', function (Blueprint $table) {
            // Ajouter item_id seulement si elle n'existe pas
            if (!Schema::hasColumn('messages', 'item_id')) {
                $table->foreignId('item_id')->nullable()->constrained('items')->onDelete('cascade')->after('receiver_id');
            }
            
            // Ajouter subject seulement si elle n'existe pas
            if (!Schema::hasColumn('messages', 'subject')) {
                $table->string('subject')->nullable()->after('conversation_id');
            }
            
            // Renommer attachment_url en attachment si nécessaire
            if (Schema::hasColumn('messages', 'attachment_url') && !Schema::hasColumn('messages', 'attachment')) {
                $table->renameColumn('attachment_url', 'attachment');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'item_id')) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }
            
            if (Schema::hasColumn('messages', 'subject')) {
                $table->dropColumn('subject');
            }
            
            // Restaurer attachment_url si nécessaire
            if (Schema::hasColumn('messages', 'attachment') && !Schema::hasColumn('messages', 'attachment_url')) {
                $table->renameColumn('attachment', 'attachment_url');
            }
        });
    }
};
