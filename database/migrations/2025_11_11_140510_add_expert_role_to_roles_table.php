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
        // Insérer le rôle 'expert' s'il n'existe pas
        if (Schema::hasTable('roles')) {
            $exists = \Illuminate\Support\Facades\DB::table('roles')->where('slug', 'expert')->exists();
            if (! $exists) {
                \Illuminate\Support\Facades\DB::table('roles')->insert([
                    'name' => 'Expert',
                    'slug' => 'expert',
                    'description' => 'Expert en vérification d\'authenticité',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            \Illuminate\Support\Facades\DB::table('roles')->where('slug', 'expert')->delete();
        }
    }
};
