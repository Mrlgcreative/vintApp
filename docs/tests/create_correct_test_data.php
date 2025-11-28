<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Str;

echo "=== CRÉATION DE DONNÉES DE TEST CORRECTES ===\n\n";

try {
    DB::beginTransaction();
    
    // Vérifier s'il y a déjà des données
    $referralsCount = DB::table('referrals')->count();
    echo "Parrainages existants: {$referralsCount}\n";
    
    if ($referralsCount == 0) {
        echo "Création de données de test...\n";
        
        // 1. Créer des codes de parrainage dans la table referral_codes
        $users = User::limit(5)->get();
        $referralCodeIds = [];
        
        foreach ($users as $user) {
            $code = strtoupper(Str::random(6));
            
            $referralCodeId = DB::table('referral_codes')->insertGetId([
                'user_id' => $user->id,
                'code' => $code,
                'title' => "Code de {$user->name}",
                'description' => 'Code de parrainage généré automatiquement',
                'is_active' => 1,
                'max_uses' => 100,
                'current_uses' => 0,
                'bonus_points' => 100,
                'expires_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Mettre à jour l'utilisateur avec son code
            $user->referral_code = $code;
            $user->save();
            
            $referralCodeIds[$user->id] = $referralCodeId;
            echo "Code créé pour {$user->name}: {$code}\n";
        }
        
        // 2. Créer quelques parrainages
        $referrers = User::whereNotNull('referral_code')->take(3)->get();
        $otherUsers = User::whereNull('referred_by')
            ->whereNotIn('id', $referrers->pluck('id'))
            ->take(8)
            ->get();
        
        $count = 0;
        foreach ($referrers as $index => $referrer) {
            $numReferrals = [3, 2, 1][$index] ?? 1;
            
            for ($i = 0; $i < $numReferrals && $count < count($otherUsers); $i++) {
                $referred = $otherUsers[$count];
                
                // Mettre à jour l'utilisateur référé
                $referred->referred_by = $referrer->id;
                $referred->save();
                
                // Créer l'entrée dans la table referrals
                DB::table('referrals')->insert([
                    'referrer_id' => $referrer->id,
                    'referred_id' => $referred->id,
                    'referral_code_id' => $referralCodeIds[$referrer->id],
                    'status' => 'completed',
                    'points_awarded' => 100,
                    'created_at' => now()->subDays(rand(1, 15)),
                    'updated_at' => now(),
                ]);
                
                // Mettre à jour le compteur d'utilisation du code
                DB::table('referral_codes')
                    ->where('id', $referralCodeIds[$referrer->id])
                    ->increment('current_uses');
                
                echo "Parrainage: {$referrer->name} -> {$referred->name}\n";
                $count++;
            }
        }
        
        echo "\nDonnées de test créées avec succès!\n";
    } else {
        echo "Des données existent déjà.\n";
    }
    
    // Afficher le résumé
    $totalReferrals = DB::table('referrals')->count();
    $totalReferrers = User::whereHas('referrals')->count();
    
    echo "\n=== RÉSUMÉ ===\n";
    echo "Total parrainages: {$totalReferrals}\n";
    echo "Parrains actifs: {$totalReferrers}\n";
    
    if ($totalReferrers > 0) {
        echo "\nParrains avec nombre de parrainages:\n";
        $stats = DB::table('referrals')
            ->join('users', 'referrals.referrer_id', '=', 'users.id')
            ->select('users.name', DB::raw('COUNT(*) as count'))
            ->groupBy('referrals.referrer_id', 'users.name')
            ->orderBy('count', 'desc')
            ->get();
            
        foreach ($stats as $stat) {
            echo "- {$stat->name}: {$stat->count} parrainages\n";
        }
    }
    
    DB::commit();
    echo "\n✅ Terminé! Vous pouvez maintenant tester l'interface d'affiliation.\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . " ligne " . $e->getLine() . "\n";
}