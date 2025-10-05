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
            // Changer la colonne type d'ENUM vers VARCHAR pour plus de flexibilité
            $table->string('type', 50)->default('text')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Revenir à l'ENUM original
            $table->enum('type', ['text', 'image', 'file'])->default('text')->change();
        });
    }
};
