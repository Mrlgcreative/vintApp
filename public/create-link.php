<?php
// Créer le lien symbolique storage -> ../storage/app/public
// SUPPRIMER CE FICHIER APRES UTILISATION

header('Content-Type: text/plain; charset=utf-8');

$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

echo "=== Creation lien symbolique storage ===\n\n";
echo "Cible: $target\n";
echo "Lien:  $link\n\n";

if (file_exists($link) || is_link($link)) {
    echo "⚠️  Le lien/dossier 'public/storage' existe deja.\n";
    if (is_link($link)) {
        echo "   C'est un lien symbolique vers: " . readlink($link) . "\n";
        echo "   ✅ Lien deja en place!\n";
    } else {
        echo "   C'est un dossier normal, pas un lien symbolique.\n";
        echo "   Supprimez-le d'abord via le gestionnaire de fichiers Hostinger.\n";
    }
    exit;
}

if (!file_exists($target)) {
    echo "⚠️  Le dossier cible n'existe pas, creation...\n";
    mkdir($target, 0755, true);
    echo "   ✅ Dossier storage/app/public cree\n\n";
}

if (symlink($target, $link)) {
    echo "✅ Lien symbolique cree avec succes!\n";
    echo "   public/storage -> storage/app/public\n";
} else {
    echo "❌ symlink() a echoue. Alternative: copie du dossier...\n\n";

    // Fallback: si symlink ne marche pas, on fait une copie
    // et on crée un .htaccess pour rediriger
    echo "Tentative de creation d'un dossier avec redirection...\n";
    mkdir($link, 0755, true);
    
    // Copier les fichiers existants
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($target, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $item) {
        $dest = $link . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
        if ($item->isDir()) {
            if (!file_exists($dest)) mkdir($dest, 0755, true);
        } else {
            copy($item->getPathname(), $dest);
            $count++;
        }
    }
    echo "✅ $count fichiers copies dans public/storage/\n";
    echo "⚠️  Note: les nouveaux uploads ne seront pas visibles automatiquement.\n";
    echo "   Il faudra relancer ce script pour synchroniser.\n";
}

echo "\n⚠️  SUPPRIMEZ CE FICHIER APRES UTILISATION!\n";
