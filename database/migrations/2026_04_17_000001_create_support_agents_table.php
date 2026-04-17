<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter le rôle support
        DB::table('roles')->insertOrIgnore([
            'name' => 'Support',
            'slug' => 'support',
            'description' => 'Agent de support client',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Table de métadonnées des agents support
        Schema::create('support_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('max_chats')->default(10);
            $table->json('specialties')->nullable(); // ['technical', 'payment', 'order', ...]
            $table->timestamp('last_assigned_at')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['is_active', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_agents');
        DB::table('roles')->where('slug', 'support')->delete();
    }
};
