<?php
/**
 * Script temporaire pour créer le symlink storage sur Hostinger
 * IMPORTANT : Supprimez ce fichier après utilisation !
 */

echo "<h2>Diagnostic Hostinger - Symlink Storage</h2>";

// Afficher les chemins détectés pour debug
$documentRoot = $_SERVER['DOCUMENT_ROOT'];
$scriptDir = __DIR__;
$parentDir = dirname($scriptDir);

echo "<h3>1. Chemins détectés :</h3>";
echo "DOCUMENT_ROOT : <code>$documentRoot</code><br>";
echo "__DIR__ (ce script) : <code>$scriptDir</code><br>";
echo "Dossier parent : <code>$parentDir</code><br><br>";

// Utiliser __DIR__ qui est plus fiable que DOCUMENT_ROOT
// __DIR__ = le dossier où se trouve CE script (= public/)
// Le dossier Laravel racine est un niveau au-dessus
$laravelRoot = realpath($scriptDir . '/..');
$target = $laravelRoot . '/storage/app/public';
$link = $scriptDir . '/storage';

echo "<h3>2. Chemins calculés :</h3>";
echo "Racine Laravel : <code>$laravelRoot</code><br>";
echo "Cible (storage/app/public) : <code>$target</code><br>";
echo "Lien à créer : <code>$link</code><br><br>";

// Vérifier la structure
echo "<h3>3. Vérifications :</h3>";
echo "Racine Laravel existe : " . (is_dir($laravelRoot) ? '✅ OUI' : '❌ NON') . "<br>";
echo "storage/ existe : " . (is_dir($laravelRoot . '/storage') ? '✅ OUI' : '❌ NON') . "<br>";
echo "storage/app/ existe : " . (is_dir($laravelRoot . '/storage/app') ? '✅ OUI' : '❌ NON') . "<br>";
echo "storage/app/public/ existe : " . (is_dir($target) ? '✅ OUI' : '❌ NON') . "<br><br>";

// Créer storage/app/public si absent
if (!is_dir($target)) {
    echo "⚙️ Création du dossier storage/app/public...<br>";
    if (mkdir($target, 0755, true)) {
        echo "✅ Dossier créé.<br><br>";
    } else {
        echo "❌ Impossible de créer le dossier. Erreur : " . error_get_last()['message'] . "<br>";
        exit;
    }
}

// Vérifier si le lien existe déjà
if (file_exists($link) || is_link($link)) {
    $type = is_link($link) ? 'symlink' : (is_dir($link) ? 'dossier' : 'fichier');
    echo "⚠️ '$link' existe déjà (type: $type).<br>";
    
    if (is_link($link)) {
        echo "Cible actuelle du symlink : " . readlink($link) . "<br>";
    }
    
    echo "<br>Pour recréer : supprimez public/storage via le File Manager Hostinger, puis relancez ce script.";
    exit;
}

// Créer le symlink avec chemin relatif (plus portable)
echo "<h3>4. Création du symlink :</h3>";

// Essai 1 : chemin relatif (préférable)
$relativeTarget = '../storage/app/public';
echo "Tentative avec chemin relatif : <code>$relativeTarget</code> ...<br>";

if (@symlink($relativeTarget, $link)) {
    echo "✅ <strong>Symlink créé avec succès !</strong><br>";
    echo "Lien : <code>$link</code><br>";
    echo "Pointe vers : <code>$relativeTarget</code><br><br>";
    
    // Vérifier que le lien fonctionne
    if (is_dir($link)) {
        echo "✅ Le lien est fonctionnel.<br><br>";
    } else {
        echo "⚠️ Le lien existe mais ne semble pas fonctionnel.<br><br>";
    }
    
    echo "<strong style='color:red;'>⚠️ SUPPRIMEZ CE FICHIER (create-symlink.php) IMMÉDIATEMENT !</strong>";
    exit;
}

// Essai 2 : chemin absolu
echo "❌ Chemin relatif échoué. Tentative avec chemin absolu...<br>";

if (@symlink($target, $link)) {
    echo "✅ <strong>Symlink créé avec chemin absolu !</strong><br>";
    echo "Lien : <code>$link</code><br>";
    echo "Cible : <code>$target</code><br><br>";
    echo "<strong style='color:red;'>⚠️ SUPPRIMEZ CE FICHIER (create-symlink.php) IMMÉDIATEMENT !</strong>";
    exit;
}

// Tout a échoué
$error = error_get_last();
echo "❌ <strong>Impossible de créer le symlink.</strong><br>";
echo "Erreur : " . ($error['message'] ?? 'inconnue') . "<br><br>";

echo "<h3>Solutions alternatives :</h3>";
echo "1. <strong>Via SSH Hostinger</strong> (hPanel > Avancé > Terminal) :<br>";
echo "<code>cd " . $scriptDir . " && ln -s ../storage/app/public storage</code><br><br>";
echo "2. <strong>Via le Terminal Hostinger</strong> dans hPanel :<br>";
echo "<code>ln -s " . $target . " " . $link . "</code><br>";
