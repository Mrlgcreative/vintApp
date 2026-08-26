<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->insertOrIgnore([
            'name' => 'Vendeur',
            'slug' => 'vendeur',
            'description' => 'A publié au moins un produit sur la plateforme',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $vendeurRoleId = DB::table('roles')->where('slug', 'vendeur')->value('id');
        if ($vendeurRoleId) {
            $sellerUserIds = DB::table('items')
                ->pluck('user_id')
                ->unique();

            foreach ($sellerUserIds as $userId) {
                DB::table('role_user')->insertOrIgnore([
                    'user_id' => $userId,
                    'role_id' => $vendeurRoleId,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('roles')->where('slug', 'vendeur')->delete();
    }
};
