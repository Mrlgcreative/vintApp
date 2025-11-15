# Script d'ajout automatique des classes dark mode dans les fichiers Blade
# VintApp - Systeme de theme dark mode

Write-Host "Script d'ajout automatique du dark mode" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$viewsPath = "resources/views"
$excludeFolders = @("vendor", "node_modules", "storage")

# Mapping des classes pour le dark mode
$darkModeRules = @{
    # Backgrounds
    'bg-white\b'                       = 'bg-white dark:bg-gray-800'
    'bg-gray-50\b'                     = 'bg-gray-50 dark:bg-gray-900'
    'bg-gray-100\b'                    = 'bg-gray-100 dark:bg-gray-800'
    'bg-gray-200\b'                    = 'bg-gray-200 dark:bg-gray-700'
    
    # Textes
    'text-gray-900\b(?!.*dark:)'       = 'text-gray-900 dark:text-white'
    'text-gray-800\b(?!.*dark:)'       = 'text-gray-800 dark:text-gray-100'
    'text-gray-700\b(?!.*dark:)'       = 'text-gray-700 dark:text-gray-200'
    'text-gray-600\b(?!.*dark:)'       = 'text-gray-600 dark:text-gray-300'
    'text-gray-500\b(?!.*dark:)'       = 'text-gray-500 dark:text-gray-400'
    
    # Bordures
    'border-gray-200\b(?!.*dark:)'     = 'border-gray-200 dark:border-gray-700'
    'border-gray-300\b(?!.*dark:)'     = 'border-gray-300 dark:border-gray-600'
    
    # Hover states
    'hover:bg-gray-50\b(?!.*dark:)'    = 'hover:bg-gray-50 dark:hover:bg-gray-700'
    'hover:bg-gray-100\b(?!.*dark:)'   = 'hover:bg-gray-100 dark:hover:bg-gray-700'
    'hover:text-gray-900\b(?!.*dark:)' = 'hover:text-gray-900 dark:hover:text-white'
}

# Fonction pour vérifier si un fichier doit être exclu
function Should-ExcludeFile {
    param($filePath)
    
    foreach ($folder in $excludeFolders) {
        if ($filePath -like "*$folder*") {
            return $true
        }
    }
    return $false
}

# Fonction pour ajouter les classes dark mode
function Add-DarkModeClasses {
    param(
        [string]$filePath
    )
    
    if (Should-ExcludeFile $filePath) {
        return $false
    }
    
    $content = Get-Content $filePath -Raw -Encoding UTF8
    $originalContent = $content
    $modified = $false
    
    foreach ($pattern in $darkModeRules.Keys) {
        $replacement = $darkModeRules[$pattern]
        
        # Chercher les occurrences qui n'ont pas déjà la classe dark:
        if ($content -match $pattern) {
            $newContent = $content -replace $pattern, $replacement
            
            if ($newContent -ne $content) {
                $content = $newContent
                $modified = $true
            }
        }
    }
    
    if ($modified) {
        Set-Content -Path $filePath -Value $content -Encoding UTF8 -NoNewline
        return $true
    }
    
    return $false
}

# Fonction principale
function Process-BladeFiles {
    Write-Host "Recherche des fichiers Blade dans $viewsPath..." -ForegroundColor Yellow
    Write-Host ""
    
    $bladeFiles = Get-ChildItem -Path $viewsPath -Filter "*.blade.php" -Recurse | 
    Where-Object { -not (Should-ExcludeFile $_.FullName) }
    
    $totalFiles = $bladeFiles.Count
    $modifiedFiles = 0
    $processedFiles = 0
    
    Write-Host "Trouve $totalFiles fichiers Blade a analyser" -ForegroundColor Green
    Write-Host ""
    Write-Host "Traitement en cours..." -ForegroundColor Cyan
    Write-Host ""
    
    foreach ($file in $bladeFiles) {
        $processedFiles++
        $relativePath = $file.FullName.Replace((Get-Location).Path + "\", "")
        
        Write-Progress -Activity "Ajout du dark mode" `
            -Status "Fichier $processedFiles sur $totalFiles" `
            -PercentComplete (($processedFiles / $totalFiles) * 100) `
            -CurrentOperation $relativePath
        
        try {
            $wasModified = Add-DarkModeClasses -filePath $file.FullName
            
            if ($wasModified) {
                $modifiedFiles++
                Write-Host "  [OK] " -ForegroundColor Green -NoNewline
                Write-Host $relativePath -ForegroundColor White
            }
        }
        catch {
            Write-Host "  [ERR] Erreur dans $relativePath : $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    
    Write-Progress -Activity "Ajout du dark mode" -Completed
    
    Write-Host ""
    Write-Host "=========================================="  -ForegroundColor Cyan
    Write-Host "Traitement termine !" -ForegroundColor Green
    Write-Host ""
    Write-Host "Statistiques :" -ForegroundColor Yellow
    Write-Host "  - Fichiers analyses : $totalFiles" -ForegroundColor White
    Write-Host "  - Fichiers modifies : $modifiedFiles" -ForegroundColor Green
    Write-Host "  - Fichiers inchanges : $($totalFiles - $modifiedFiles)" -ForegroundColor Gray
    Write-Host ""
    
    if ($modifiedFiles -gt 0) {
        Write-Host "Classes dark mode ajoutees avec succes !" -ForegroundColor Green
        Write-Host ""
        Write-Host "Prochaines etapes :" -ForegroundColor Yellow
        Write-Host "  1. Vérifiez les modifications avec : git diff" -ForegroundColor White
        Write-Host "  2. Testez votre application" -ForegroundColor White
        Write-Host "  3. Exécutez : npm run build" -ForegroundColor White
        Write-Host ""
    }
}

# Demander confirmation
Write-Host "ATTENTION: Ce script va modifier tous les fichiers Blade dans $viewsPath" -ForegroundColor Yellow
Write-Host ""
$confirm = Read-Host "Voulez-vous continuer ? (O/N)"

if ($confirm -eq "O" -or $confirm -eq "o" -or $confirm -eq "Y" -or $confirm -eq "y") {
    Write-Host ""
    Process-BladeFiles
}
else {
    Write-Host ""
    Write-Host "Operation annulee" -ForegroundColor Red
    Write-Host ""
}

Write-Host "Appuyez sur une touche pour quitter..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
