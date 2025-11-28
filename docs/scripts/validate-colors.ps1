# Script de validation pour vérifier la conversion des couleurs
# VintApp - Validation du système de couleurs dynamiques

Write-Host "🔍 VALIDATION DU SYSTÈME DE COULEURS DYNAMIQUES" -ForegroundColor Magenta
Write-Host "=" * 60 -ForegroundColor Cyan

# Définir le répertoire des vues
$viewsPath = "resources\views"

# Fonction pour analyser un fichier
function AnalyzeFile {
    param(
        [string]$filePath
    )
    
    $content = Get-Content -Path $filePath -Raw
    $relativePath = $filePath -replace [regex]::Escape($PWD.Path + "\"), ""
    
    # Chercher les références purple-* restantes
    $purpleMatches = [regex]::Matches($content, 'purple-[0-9]+')
    $pinkMatches = [regex]::Matches($content, 'pink-[0-9]+')
    
    # Chercher les nouvelles références dynamiques
    $primaryMatches = [regex]::Matches($content, 'primary-[0-9]+')
    $accentMatches = [regex]::Matches($content, 'accent-[0-9]+')
    $secondaryMatches = [regex]::Matches($content, 'secondary-[0-9]+')
    
    $hasOldColors = $purpleMatches.Count -gt 0 -or $pinkMatches.Count -gt 0
    $hasNewColors = $primaryMatches.Count -gt 0 -or $accentMatches.Count -gt 0 -or $secondaryMatches.Count -gt 0
    
    return @{
        'FilePath'       = $relativePath
        'PurpleCount'    = $purpleMatches.Count
        'PinkCount'      = $pinkMatches.Count
        'PrimaryCount'   = $primaryMatches.Count
        'AccentCount'    = $accentMatches.Count
        'SecondaryCount' = $secondaryMatches.Count
        'HasOldColors'   = $hasOldColors
        'HasNewColors'   = $hasNewColors
        'PurpleMatches'  = $purpleMatches
        'PinkMatches'    = $pinkMatches
    }
}

# Obtenir tous les fichiers .blade.php
$bladeFiles = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse

Write-Host "📊 Analyse de $($bladeFiles.Count) fichiers Blade..." -ForegroundColor Yellow
Write-Host ""

$results = @()
$filesWithOldColors = @()
$totalOldReferences = 0
$totalNewReferences = 0

foreach ($file in $bladeFiles) {
    $analysis = AnalyzeFile -filePath $file.FullName
    $results += $analysis
    
    $totalOldReferences += $analysis.PurpleCount + $analysis.PinkCount
    $totalNewReferences += $analysis.PrimaryCount + $analysis.AccentCount + $analysis.SecondaryCount
    
    if ($analysis.HasOldColors) {
        $filesWithOldColors += $analysis
    }
}

# Afficher les statistiques générales
Write-Host "📈 STATISTIQUES GÉNÉRALES" -ForegroundColor Magenta
Write-Host "=" * 40 -ForegroundColor Cyan
Write-Host "📁 Fichiers analysés: $($bladeFiles.Count)" -ForegroundColor White
Write-Host "🔴 Références anciennes (purple/pink): $totalOldReferences" -ForegroundColor Red
Write-Host "🟢 Références dynamiques (primary/accent/secondary): $totalNewReferences" -ForegroundColor Green
Write-Host "⚠️  Fichiers nécessitant une attention: $($filesWithOldColors.Count)" -ForegroundColor Yellow

# Afficher les fichiers avec des anciennes références
if ($filesWithOldColors.Count -gt 0) {
    Write-Host ""
    Write-Host "⚠️  FICHIERS AVEC ANCIENNES RÉFÉRENCES" -ForegroundColor Yellow
    Write-Host "=" * 40 -ForegroundColor Cyan
    
    foreach ($file in $filesWithOldColors) {
        Write-Host "📄 $($file.FilePath)" -ForegroundColor White
        if ($file.PurpleCount -gt 0) {
            Write-Host "   🟣 purple-*: $($file.PurpleCount) références" -ForegroundColor Magenta
            # Afficher les lignes spécifiques
            foreach ($match in $file.PurpleMatches) {
                Write-Host "      → $($match.Value)" -ForegroundColor Gray
            }
        }
        if ($file.PinkCount -gt 0) {
            Write-Host "   🩷 pink-*: $($file.PinkCount) références" -ForegroundColor Magenta
            # Afficher les lignes spécifiques
            foreach ($match in $file.PinkMatches) {
                Write-Host "      → $($match.Value)" -ForegroundColor Gray
            }
        }
        Write-Host ""
    }
}

# Afficher les fichiers entièrement convertis
$fullyConvertedFiles = $results | Where-Object { $_.HasNewColors -and -not $_.HasOldColors }
if ($fullyConvertedFiles.Count -gt 0) {
    Write-Host "✅ FICHIERS ENTIÈREMENT CONVERTIS" -ForegroundColor Green
    Write-Host "=" * 40 -ForegroundColor Cyan
    
    foreach ($file in $fullyConvertedFiles) {
        Write-Host "📄 $($file.FilePath)" -ForegroundColor White
        Write-Host "   🟢 primary-*: $($file.PrimaryCount) | accent-*: $($file.AccentCount) | secondary-*: $($file.SecondaryCount)" -ForegroundColor Green
    }
    Write-Host ""
}

# Vérifier le fichier de configuration des couleurs
Write-Host "🔧 VÉRIFICATION DE LA CONFIGURATION" -ForegroundColor Magenta
Write-Host "=" * 40 -ForegroundColor Cyan

$configExists = Test-Path "config\colors.php"
$serviceExists = Test-Path "app\Services\ColorPaletteService.php"
$cssExists = Test-Path "resources\css\colors.css"

Write-Host "📁 config/colors.php: $(if ($configExists) { '✅ Trouvé' } else { '❌ Manquant' })" -ForegroundColor $(if ($configExists) { 'Green' } else { 'Red' })
Write-Host "📁 ColorPaletteService: $(if ($serviceExists) { '✅ Trouvé' } else { '❌ Manquant' })" -ForegroundColor $(if ($serviceExists) { 'Green' } else { 'Red' })
Write-Host "📁 CSS des couleurs: $(if ($cssExists) { '✅ Trouvé' } else { '❌ Manquant' })" -ForegroundColor $(if ($cssExists) { 'Green' } else { 'Red' })

# Recommandations
Write-Host ""
Write-Host "💡 RECOMMANDATIONS" -ForegroundColor Magenta
Write-Host "=" * 40 -ForegroundColor Cyan

if ($totalOldReferences -eq 0) {
    Write-Host "🎉 Parfait ! Toutes les références ont été converties." -ForegroundColor Green
    Write-Host "🚀 Vous pouvez maintenant utiliser le système de couleurs dynamiques." -ForegroundColor Green
}
else {
    Write-Host "⚠️  Il reste $totalOldReferences références à convertir." -ForegroundColor Yellow
    Write-Host "🔧 Exécutez le script de conversion: .\convert-purple-to-dynamic.ps1" -ForegroundColor Yellow
}

if (-not $configExists -or -not $serviceExists) {
    Write-Host "⚠️  Configuration incomplète détectée." -ForegroundColor Red
    Write-Host "🔧 Vérifiez que tous les fichiers du système sont en place." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "🔄 Pour re-générer les couleurs CSS: php artisan colors:inject" -ForegroundColor Cyan
Write-Host "🔨 Pour compiler les assets: npm run build" -ForegroundColor Cyan
Write-Host ""