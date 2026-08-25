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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('color', 7)->nullable()->after('image');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->boolean('show_in_menu')->default(true)->after('is_featured');
            $table->string('meta_title')->nullable()->after('sort_order');
            $table->string('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'color',
                'is_featured',
                'show_in_menu',
                'meta_title',
                'meta_description',
                'meta_keywords',
            ]);
        });
    }
};
