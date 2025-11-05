<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

echo "=== VÉRIFICATION SIMPLE DE LA BASE DE DONNÉES ===\n\n";

try {
    // Vérifier le nombre total d'utilisateurs
    $totalUsers = User::count();
    echo "📊 Total utilisateurs: {$totalUsers}\n";
    
    // Vérifier les utilisateurs avec des codes de parrainage
    $usersWithReferralCodes = User::whereNotNull('referral_code')->count();
    echo "🔗 Utilisateurs avec codes de parrainage: {$usersWithReferralCodes}\n";
    
    if ($usersWithReferralCodes > 0) {
        echo "\n📋 Utilisateurs avec codes de parrainage:\n";
        $users = User::whereNotNull('referral_code')->get(['id', 'name', 'referral_code']);
        foreach ($users as $user) {
            echo "   - {$user->name} (ID: {$user->id}): {$user->referral_code}\n";
        }
    }
    
    // Vérifier la table referrals
    $referralsCount = DB::table('referrals')->count();
    echo "\n🤝 Total parrainages effectués: {$referralsCount}\n";
    
    if ($referralsCount > 0) {
        echo "\n📋 Parrainages existants:\n";
        $referrals = DB::table('referrals')->limit(10)->get();
        foreach ($referrals as $referral) {
            $referrer = User::find($referral->referrer_id);
            $referred = User::find($referral->referred_id);
            echo "   - {$referrer->name} a parrainé {$referred->name}\n";
        }
    }
    
    // Vérifier les colonnes de la table user_points
    if (Schema::hasTable('user_points')) {
        $columns = Schema::getColumnListing('user_points');
        echo "\n💰 Structure table user_points: " . implode(', ', $columns) . "\n";
        
        $pointsCount = DB::table('user_points')->count();
        echo "💰 Entrées dans user_points: {$pointsCount}\n";
        
        if ($pointsCount > 0) {
            $points = DB::table('user_points')->limit(5)->get();
            foreach ($points as $point) {
                $user = User::find($point->user_id);
                echo "   - {$user->name}: " . json_encode($point) . "\n";
            }
        }
    }
    
    // Vérifier les récompenses
    if (Schema::hasTable('affiliate_rewards')) {
        $rewardsCount = DB::table('affiliate_rewards')->count();
        echo "\n🎁 Récompenses d'affiliation: {$rewardsCount}\n";
    }
    
    // Vérifier les transactions de points
    if (Schema::hasTable('point_transactions')) {
        $transactionsCount = DB::table('point_transactions')->count();
        echo "📈 Transactions de points: {$transactionsCount}\n";
    }
    
    // Statistiques utilisateurs
    echo "\n👥 RÉSUMÉ UTILISATEURS:\n";
    $activeUsers = User::whereNotNull('email_verified_at')->count();
    echo "   - Utilisateurs vérifiés: {$activeUsers}\n";
    
    $recentUsers = User::where('created_at', '>', now()->subDays(30))->count();
    echo "   - Nouveaux utilisateurs (30 derniers jours): {$recentUsers}\n";
    
    // Vérifier s'il y a des utilisateurs référés
    $referredUsers = User::whereNotNull('referred_by')->count();
    echo "   - Utilisateurs qui ont été parrainés: {$referredUsers}\n";
    
    if ($referredUsers > 0) {
        echo "\n📋 Utilisateurs parrainés:\n";
        $referred = User::whereNotNull('referred_by')->limit(10)->get(['id', 'name', 'referred_by']);
        foreach ($referred as $user) {
            $referrer = User::find($user->referred_by);
            echo "   - {$user->name} parrainé par " . ($referrer ? $referrer->name : "ID: {$user->referred_by}") . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA VÉRIFICATION ===\n";