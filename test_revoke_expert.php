<?php

require_once 'vendor/autoload.php';

// Simuler une requête de test pour la révocation d'expert
echo "Test de révocation d'expert\n";
echo "===========================\n\n";

// Vérifier que la route existe
$routes = [];
exec('php artisan route:list | grep "expert.*revoke"', $routes, $return_code);

if (empty($routes)) {
    echo "❌ Erreur: Route de révocation d'expert introuvable\n";
    exit(1);
}

echo "✅ Route de révocation trouvée:\n";
foreach($routes as $route) {
    echo "   " . trim($route) . "\n";
}
echo "\n";

// Vérifier la structure de la base de données
echo "Vérification de la base de données...\n";

try {
    // Test de connexion à la base de données
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=vintapp', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la base de données réussie\n";
    
    // Vérifier la table expert_profiles
    $stmt = $pdo->query("SHOW TABLES LIKE 'expert_profiles'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table expert_profiles trouvée\n";
        
        // Compter les experts
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM expert_profiles");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "   Nombre d'experts: " . $result['total'] . "\n";
        
        // Lister les experts
        if ($result['total'] > 0) {
            $stmt = $pdo->query("
                SELECT ep.id, ep.user_id, u.name, u.email 
                FROM expert_profiles ep 
                JOIN users u ON ep.user_id = u.id 
                LIMIT 5
            ");
            echo "   Experts disponibles:\n";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "   - ID: {$row['id']}, User: {$row['name']} ({$row['email']})\n";
            }
        }
    } else {
        echo "❌ Table expert_profiles introuvable\n";
    }
    
    // Vérifier la table roles
    $stmt = $pdo->query("SHOW TABLES LIKE 'roles'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Table roles trouvée\n";
        
        // Vérifier le rôle expert
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE slug = 'expert'");
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "✅ Rôle 'expert' trouvé\n";
        } else {
            echo "❌ Rôle 'expert' introuvable\n";
        }
    } else {
        echo "❌ Table roles introuvable\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "\n";
}

echo "\n";
echo "Test terminé. Si tous les éléments sont ✅, le problème pourrait être:\n";
echo "1. Authentification (middleware admin)\n";
echo "2. Token CSRF manquant\n";
echo "3. Permissions insuffisantes\n";
echo "4. Erreur JavaScript dans la console du navigateur\n";
echo "\nVérifiez la console du navigateur (F12) pour plus de détails.\n";