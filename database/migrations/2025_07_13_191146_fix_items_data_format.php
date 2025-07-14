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
        // Nettoyer les données des articles
        $items = DB::table('items')->get();
        
        foreach ($items as $item) {
            $updates = [];
            
            // Nettoyer specifications
            if (!empty($item->specifications)) {
                if (is_string($item->specifications)) {
                    $specs = json_decode($item->specifications, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($specs)) {
                        $updates['specifications'] = json_encode($specs);
                    } else {
                        $updates['specifications'] = null;
                    }
                } elseif (!is_array($item->specifications)) {
                    $updates['specifications'] = null;
                }
            } else {
                $updates['specifications'] = null;
            }
            
            // Nettoyer images
            if (!empty($item->images)) {
                if (is_string($item->images)) {
                    $imgs = json_decode($item->images, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($imgs)) {
                        $updates['images'] = json_encode($imgs);
                    } else {
                        $updates['images'] = null;
                    }
                } elseif (!is_array($item->images)) {
                    $updates['images'] = null;
                }
            } else {
                $updates['images'] = null;
            }
            
            // Appliquer les mises à jour si nécessaire
            if (!empty($updates)) {
                DB::table('items')->where('id', $item->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cette migration ne peut pas être annulée car elle corrige des données
    }
};
