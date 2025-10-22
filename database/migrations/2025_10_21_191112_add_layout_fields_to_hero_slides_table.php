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
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->string('background_color', 7)->default('#6A0DAD')->after('image_path'); // Couleur de fond personnalisable (format hex)
            $table->string('text_position', 20)->default('left')->after('background_color'); // Position du texte (left, right, center)
            $table->string('image_position', 20)->default('right')->after('text_position'); // Position de l'image (left, right)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_slides', function (Blueprint $table) {
            $table->dropColumn(['background_color', 'text_position', 'image_position']);
        });
    }
};
