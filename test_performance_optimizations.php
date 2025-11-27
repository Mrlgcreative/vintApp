<?php

/**
 * Script de test des optimisations de performance
 * 
 * Usage: php test_performance_optimizations.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\CacheService;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;

echo "🚀 TEST DES OPTIMISATIONS DE PERFORMANCE\n";
echo "=========================================\n\n";

// Test 1: CacheService
echo "1️⃣ Test CacheService...\n";
try {
    $cacheService = app(CacheService::class);
    
    // Test categories
    $start = microtime(true);
    $categories = $cacheService->getCategories();
    $time1 = round((microtime(true) - $start) * 1000, 2);
    
    $start = microtime(true);
    $categoriesCached = $cacheService->getCategories();
    $time2 = round((microtime(true) - $start) * 1000, 2);
    
    echo "   ✅ Categories (1st call): {$time1}ms\n";
    echo "   ✅ Categories (cached): {$time2}ms\n";
    echo "   📊 Gain: " . round(($time1 - $time2) / $time1 * 100, 1) . "%\n\n";
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// Test 2: Database indexes
echo "2️⃣ Test Index Database...\n";
try {
    // Vérifier si les index existent
    $indexes = DB::select("SHOW INDEX FROM items WHERE Key_name LIKE 'idx_%'");
    
    if (count($indexes) > 0) {
        echo "   ✅ " . count($indexes) . " index de performance détectés\n";
        foreach ($indexes as $index) {
            echo "      - {$index->Key_name} sur {$index->Column_name}\n";
        }
    } else {
        echo "   ⚠️ Aucun index de performance trouvé (migration non exécutée ?)\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ⚠️ Impossible de vérifier les index: " . $e->getMessage() . "\n\n";
}

// Test 3: Eager Loading
echo "3️⃣ Test Eager Loading...\n";
try {
    DB::enableQueryLog();
    
    // Sans eager loading
    DB::flushQueryLog();
    $items1 = Item::limit(5)->get();
    foreach ($items1 as $item) {
        $_ = $item->category;
        $_ = $item->brand;
        $_ = $item->user;
    }
    $queries1 = count(DB::getQueryLog());
    
    // Avec eager loading
    DB::flushQueryLog();
    $items2 = Item::with(['category', 'brand', 'user'])->limit(5)->get();
    foreach ($items2 as $item) {
        $_ = $item->category;
        $_ = $item->brand;
        $_ = $item->user;
    }
    $queries2 = count(DB::getQueryLog());
    
    echo "   ✅ Sans eager loading: {$queries1} requêtes\n";
    echo "   ✅ Avec eager loading: {$queries2} requêtes\n";
    echo "   📊 Réduction: " . round(($queries1 - $queries2) / $queries1 * 100, 1) . "%\n\n";
    
    DB::disableQueryLog();
} catch (Exception $e) {
    echo "   ⚠️ Test impossible: " . $e->getMessage() . "\n\n";
}

// Test 4: Cache configuration
echo "4️⃣ Vérification Configuration Cache...\n";
try {
    $cacheDriver = config('cache.default');
    echo "   ✅ Driver de cache: {$cacheDriver}\n";
    
    if ($cacheDriver === 'redis') {
        echo "   ✅ Redis activé (optimal)\n";
    } elseif ($cacheDriver === 'database') {
        echo "   ⚠️ Database cache (fonctionnel mais Redis recommandé)\n";
    } else {
        echo "   ⚠️ Driver {$cacheDriver} utilisé\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// Test 5: Middlewares
echo "5️⃣ Vérification Middlewares...\n";
try {
    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $reflection = new ReflectionClass($kernel);
    $property = $reflection->getProperty('middlewareAliases');
    $property->setAccessible(true);
    $aliases = $property->getValue($kernel);
    
    $requiredMiddlewares = ['cache.response', 'compress.response'];
    $found = 0;
    
    foreach ($requiredMiddlewares as $middleware) {
        if (isset($aliases[$middleware])) {
            echo "   ✅ Middleware '{$middleware}' enregistré\n";
            $found++;
        } else {
            echo "   ❌ Middleware '{$middleware}' manquant\n";
        }
    }
    
    echo "\n   📊 {$found}/" . count($requiredMiddlewares) . " middlewares de performance activés\n\n";
} catch (Exception $e) {
    echo "   ⚠️ Vérification impossible: " . $e->getMessage() . "\n\n";
}

// Test 6: Performance globale
echo "6️⃣ Test Performance Globale...\n";
try {
    $start = microtime(true);
    $cacheService = app(CacheService::class);
    
    // Simule plusieurs opérations courantes
    $categories = $cacheService->getCategories();
    $brands = $cacheService->getBrands();
    $popularItems = $cacheService->getPopularItems(5);
    
    $totalTime = round((microtime(true) - $start) * 1000, 2);
    
    echo "   ✅ Temps total pour 3 opérations: {$totalTime}ms\n";
    
    if ($totalTime < 100) {
        echo "   🎉 Performance excellente !\n";
    } elseif ($totalTime < 500) {
        echo "   ✅ Performance bonne\n";
    } else {
        echo "   ⚠️ Performance à améliorer\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n\n";
}

// Résumé
echo "=========================================\n";
echo "✅ Tests terminés !\n\n";
echo "📝 Recommandations:\n";
echo "   1. Exécutez 'php artisan migrate' si les index manquent\n";
echo "   2. Configurez Redis pour de meilleures performances\n";
echo "   3. Activez OPcache en production\n";
echo "   4. Utilisez 'php artisan config:cache' en production\n\n";
