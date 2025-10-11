<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Vérifier si la table existe déjà
        if (Schema::hasTable('settings')) {
            return;
        }
        
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Clé du paramètre
            $table->text('value')->nullable(); // Valeur (peut être JSON)
            $table->string('type')->default('string'); // Type: string, boolean, json, integer
            $table->text('description')->nullable(); // Description du paramètre
            $table->timestamps();
            
            $table->index('key');
        });
        
        // Insérer les paramètres par défaut
        DB::table('settings')->insert([
            [
                'key' => 'enable_location_restrictions',
                'value' => '1', // Activé par défaut
                'type' => 'boolean',
                'description' => 'Active ou désactive les restrictions géographiques pour les articles',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'site_name',
                'value' => 'VintApp',
                'type' => 'string',
                'description' => 'Nom du site',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'enable_preregistration_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Active le mode préinscription (empêche les inscriptions normales)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
