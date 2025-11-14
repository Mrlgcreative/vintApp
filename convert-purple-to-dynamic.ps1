# Script PowerShell pour convertir les références purple-* vers le système de couleurs dynamiques
# VintApp - Conversion automatique des couleurs

Write-Host "🎨 Conversion automatique des couleurs purple-* vers le système dynamique" -ForegroundColor Magenta
Write-Host "=" * 70 -ForegroundColor Cyan

# Définir le répertoire des vues
$viewsPath = "resources\views"
$backupPath = "resources\views_backup_" + (Get-Date -Format "yyyyMMdd_HHmmss")

# Créer une sauvegarde
Write-Host "📦 Création d'une sauvegarde dans: $backupPath" -ForegroundColor Yellow
Copy-Item -Path $viewsPath -Destination $backupPath -Recurse

# Définir les mappings de conversion
$colorMappings = @{
    # Couleurs principales
    'purple-50'  = 'primary-50'
    'purple-100' = 'primary-100'
    'purple-200' = 'primary-200'
    'purple-300' = 'primary-300'
    'purple-400' = 'primary-400'
    'purple-500' = 'primary-500'
    'purple-600' = 'primary-600'
    'purple-700' = 'primary-700'
    'purple-800' = 'primary-800'
    'purple-900' = 'primary-900'
    
    # Couleurs pink vers accent
    'pink-50'    = 'accent-50'
    'pink-100'   = 'accent-100'
    'pink-200'   = 'accent-200'
    'pink-300'   = 'accent-300'
    'pink-400'   = 'accent-400'
    'pink-500'   = 'accent-500'
    'pink-600'   = 'accent-600'
    'pink-700'   = 'accent-700'
    'pink-800'   = 'accent-800'
    'pink-900'   = 'accent-900'
}

# Fonction pour traiter un fichier
function ConvertColorsInFile {
    param(
        [string]$filePath
    )
    
    $relativePath = $filePath -replace [regex]::Escape($PWD.Path + "\"), ""
    Write-Host "  🔄 Traitement: $relativePath" -ForegroundColor Green
    
    $content = Get-Content -Path $filePath -Raw
    $originalContent = $content
    $changesCount = 0
    
    # Appliquer chaque mapping
    foreach ($oldColor in $colorMappings.Keys) {
        $newColor = $colorMappings[$oldColor]
        
        # Compter les occurrences avant remplacement
        $matches = [regex]::Matches($content, [regex]::Escape($oldColor))
        if ($matches.Count -gt 0) {
            $content = $content -replace [regex]::Escape($oldColor), $newColor
            $changesCount += $matches.Count
            Write-Host "    ✅ $oldColor → $newColor ($($matches.Count) occurrences)" -ForegroundColor Cyan
        }
    }
    
    # Sauvegarder si des changements ont été effectués
    if ($changesCount -gt 0) {
        Set-Content -Path $filePath -Value $content -NoNewline
        Write-Host "    📝 $changesCount changements appliqués" -ForegroundColor Green
        return $changesCount
    }
    else {
        Write-Host "    ⚪ Aucun changement nécessaire" -ForegroundColor Gray
        return 0
    }
}

# Obtenir tous les fichiers .blade.php
$bladeFiles = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse

Write-Host "`n📊 Fichiers trouvés: $($bladeFiles.Count)" -ForegroundColor Yellow
Write-Host "🔍 Analyse et conversion en cours..." -ForegroundColor Yellow
Write-Host ""

$totalChanges = 0
$processedFiles = 0
$modifiedFiles = 0

foreach ($file in $bladeFiles) {
    $changes = ConvertColorsInFile -filePath $file.FullName
    $totalChanges += $changes
    $processedFiles++
    
    if ($changes -gt 0) {
        $modifiedFiles++
    }
}

Write-Host ""
Write-Host "=" * 70 -ForegroundColor Cyan
Write-Host "📈 RÉSULTATS DE LA CONVERSION" -ForegroundColor Magenta
Write-Host "=" * 70 -ForegroundColor Cyan
Write-Host "📁 Fichiers analysés: $processedFiles" -ForegroundColor White
Write-Host "✏️  Fichiers modifiés: $modifiedFiles" -ForegroundColor Green
Write-Host "🔄 Total des changements: $totalChanges" -ForegroundColor Yellow
Write-Host "💾 Sauvegarde créée: $backupPath" -ForegroundColor Blue

if ($totalChanges -gt 0) {
    Write-Host ""
    Write-Host "🚀 ÉTAPES SUIVANTES:" -ForegroundColor Magenta
    Write-Host "1. Injecter les couleurs CSS: php artisan colors:inject" -ForegroundColor Yellow
    Write-Host "2. Compiler les assets: npm run build" -ForegroundColor Yellow
    Write-Host "3. Tester l'application" -ForegroundColor Yellow
    
    # Proposer d'exécuter automatiquement les commandes suivantes
    Write-Host ""
    $response = Read-Host "Voulez-vous exécuter automatiquement ces commandes ? (O/n)"
    if ($response -eq "" -or $response -eq "O" -or $response -eq "o" -or $response -eq "Oui" -or $response -eq "oui") {
        Write-Host ""
        Write-Host "🎨 Injection des couleurs CSS..." -ForegroundColor Yellow
        php artisan colors:inject
        
        Write-Host ""
        Write-Host "🔨 Compilation des assets..." -ForegroundColor Yellow
        npm run build
        
        Write-Host ""
        Write-Host "✨ Conversion terminée avec succès !" -ForegroundColor Green
        Write-Host "🌈 Le système de couleurs dynamiques est maintenant actif" -ForegroundColor Magenta
    }
}
else {
    Write-Host ""
    Write-Host "ℹ️  Aucune conversion nécessaire. Tous les fichiers utilisent déjà le système dynamique !" -ForegroundColor Green
}

Write-Host ""
Write-Host "📝 CONSEIL: Pour restaurer la sauvegarde en cas de problème:" -ForegroundColor Blue
Write-Host "   Remove-Item -Path '$viewsPath' -Recurse -Force" -ForegroundColor Gray
Write-Host "   Rename-Item -Path '$backupPath' -NewName 'views'" -ForegroundColor Gray
Write-Host ""