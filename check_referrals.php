<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

echo "=== VÉRIFICATION DE LA BASE DE DONNÉES PARRAINS ===\n\n";

try {
    // Vérifier le nombre total d'utilisateurs
    $totalUsers = User::count();
    echo "📊 Total utilisateurs: {$totalUsers}\n\n";
    
    // Vérifier les utilisateurs avec des codes de parrainage
    $usersWithReferralCodes = User::whereNotNull('referral_code')->count();
    echo "🔗 Utilisateurs avec codes de parrainage: {$usersWithReferralCodes}\n";
    
    if ($usersWithReferralCodes > 0) {
        echo "\n📋 Exemples de codes de parrainage:\n";
        $sampleCodes = User::whereNotNull('referral_code')->limit(5)->get(['id', 'name', 'referral_code']);
        foreach ($sampleCodes as $user) {
            echo "   - {$user->name} (ID: {$user->id}): {$user->referral_code}\n";
        }
    }
    
    // Vérifier la table referrals
    $referralsCount = DB::table('referrals')->count();
    echo "\n🤝 Total parrainages effectués: {$referralsCount}\n";
    
    if ($referralsCount > 0) {
        echo "\n📋 Exemples de parrainages:\n";
        $sampleReferrals = DB::table('referrals')
            ->join('users as referrer', 'referrals.referrer_id', '=', 'referrer.id')
            ->join('users as referred', 'referrals.referred_id', '=', 'referred.id')
            ->select('referrals.*', 'referrer.name as referrer_name', 'referred.name as referred_name')
            ->limit(5)
            ->get();
            
        foreach ($sampleReferrals as $referral) {
            echo "   - {$referral->referrer_name} a parrainé {$referral->referred_name} (le {$referral->created_at})\n";
        }
    }
    
    // Vérifier les points utilisateurs
    $pointsCount = DB::table('user_points')->count();
    echo "\n💰 Utilisateurs avec des points: {$pointsCount}\n";
    
    if ($pointsCount > 0) {
        $totalPoints = DB::table('user_points')->sum('balance');
        echo "💎 Total points en circulation: {$totalPoints}\n";
        
        $topPointsUsers = DB::table('user_points')
            ->join('users', 'user_points.user_id', '=', 'users.id')
            ->select('users.name', 'user_points.balance', 'user_points.total_earned')
            ->orderBy('user_points.balance', 'desc')
            ->limit(5)
            ->get();
            
        echo "\n🏆 Top 5 utilisateurs par points:\n";
        foreach ($topPointsUsers as $user) {
            echo "   - {$user->name}: {$user->balance} points (total gagné: {$user->total_earned})\n";
        }
    }
    
    // Vérifier les transactions de points
    $pointTransactionsCount = DB::table('point_transactions')->count();
    echo "\n📈 Transactions de points: {$pointTransactionsCount}\n";
    
    // Vérifier les récompenses affiliés
    $rewardsCount = DB::table('affiliate_rewards')->count();
    echo "🎁 Récompenses d'affiliation données: {$rewardsCount}\n";
    
    // Statistiques par niveau de parrainage
    if ($referralsCount > 0) {
        echo "\n📊 STATISTIQUES PAR PARRAIN:\n";
        $referrerStats = DB::table('referrals')
            ->select('referrer_id', DB::raw('COUNT(*) as total_referrals'))
            ->join('users', 'referrals.referrer_id', '=', 'users.id')
            ->groupBy('referrer_id')
            ->orderBy('total_referrals', 'desc')
            ->limit(10)
            ->get();
            
        foreach ($referrerStats as $stat) {
            $user = User::find($stat->referrer_id);
            echo "   - {$user->name}: {$stat->total_referrals} parrainages\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VÉRIFICATION ===\n";