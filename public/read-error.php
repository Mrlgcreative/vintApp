<?php
// Script pour lire l'erreur exacte du log Laravel
// SUPPRIMER APRES UTILISATION

header('Content-Type: text/plain; charset=utf-8');
echo "=== LECTURE ERREUR LARAVEL ===\n\n";

$logDir = __DIR__ . '/../storage/logs';

// Trouver le fichier log le plus récent
$logFiles = glob($logDir . '/laravel*.log');
if (empty($logFiles)) {
    echo "Aucun fichier log trouvé dans: $logDir\n";
    echo "Contenu du dossier logs:\n";
    foreach (scandir($logDir) as $f) echo "  $f\n";
    exit;
}

// Trier par date de modification
usort($logFiles, function($a, $b) {
    return filemtime($b) - filemtime($a);
});

$latestLog = $logFiles[0];
echo "Fichier log: " . basename($latestLog) . "\n";
echo "Taille: " . number_format(filesize($latestLog)) . " octets\n";
echo "Modifié: " . date('Y-m-d H:i:s', filemtime($latestLog)) . "\n\n";

// Lire les 200 dernières lignes
$lines = file($latestLog);
$lastLines = array_slice($lines, -200);

// Chercher la dernière erreur avec stack trace
$errorStart = null;
for ($i = count($lastLines) - 1; $i >= 0; $i--) {
    if (preg_match('/^\[[\d\-\s:]+\]\s+(production|local)\.\w+:/', $lastLines[$i])) {
        $errorStart = $i;
        break;
    }
}

if ($errorStart !== null) {
    echo "=== DERNIERE ERREUR ===\n";
    // Afficher depuis le début de la dernière erreur (max 80 lignes)
    $errorLines = array_slice($lastLines, $errorStart, 80);
    echo implode('', $errorLines);
} else {
    echo "=== 50 DERNIERES LIGNES ===\n";
    $tail = array_slice($lastLines, -50);
    echo implode('', $tail);
}

echo "\n\n=== PHP VERSION ===\n";
echo PHP_VERSION . "\n";

echo "\n=== EXTENSIONS CHARGEES ===\n";
$important = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo', 'redis'];
foreach ($important as $ext) {
    echo ($ext === 'redis' ? '⚠️' : '  ') . " $ext: " . (extension_loaded($ext) ? '✅' : '❌') . "\n";
}

echo "\n=== TEST AUTOLOADER ===\n";
$autoloader = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloader)) {
    echo "✅ vendor/autoload.php existe\n";
    try {
        require $autoloader;
        echo "✅ Autoloader chargé\n";
        
        // Tester si l'app Laravel peut booter
        try {
            $app = require __DIR__ . '/../bootstrap/app.php';
            echo "✅ bootstrap/app.php chargé\n";
            
            $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
            echo "✅ Kernel HTTP résolu\n";
        } catch (\Throwable $e) {
            echo "❌ Erreur boot Laravel:\n";
            echo "   " . get_class($e) . ": " . $e->getMessage() . "\n";
            echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
            
            // Afficher la cause profonde
            $prev = $e->getPrevious();
            while ($prev) {
                echo "   Causé par: " . get_class($prev) . ": " . $prev->getMessage() . "\n";
                echo "   Fichier: " . $prev->getFile() . ":" . $prev->getLine() . "\n";
                $prev = $prev->getPrevious();
            }
        }
    } catch (\Throwable $e) {
        echo "❌ Erreur autoloader: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ vendor/autoload.php MANQUANT!\n";
}
