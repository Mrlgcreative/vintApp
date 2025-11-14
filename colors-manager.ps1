# Script principal pour la gestion du système de couleurs dynamiques VintApp
# Auteur: Assistant IA pour VintApp
# Version: 1.0

param(
    [Parameter(Position = 0)]
    [ValidateSet("convert", "validate", "rollback", "help")]
    [string]$Action = "help"
)

function Show-Header {
    Write-Host ""
    Write-Host "VINTAPP - GESTIONNAIRE DE COULEURS DYNAMIQUES" -ForegroundColor Magenta
    Write-Host "============================================================" -ForegroundColor Cyan
    Write-Host "Version 1.0 - Conversion purple-* vers systeme dynamique" -ForegroundColor Gray
    Write-Host ""
}

function Show-Help {
    Write-Host "UTILISATION:" -ForegroundColor Yellow
    Write-Host "  .\colors-manager.ps1 [action]" -ForegroundColor White
    Write-Host ""
    Write-Host "ACTIONS DISPONIBLES:" -ForegroundColor Yellow
    Write-Host "  convert   - Convertir toutes les references purple-* vers le systeme dynamique" -ForegroundColor Green
    Write-Host "  validate  - Analyser et valider l'etat actuel des couleurs" -ForegroundColor Cyan
    Write-Host "  rollback  - Restaurer les couleurs depuis une sauvegarde" -ForegroundColor Red
    Write-Host "  help      - Afficher cette aide" -ForegroundColor White
    Write-Host ""
    Write-Host "EXEMPLES:" -ForegroundColor Yellow
    Write-Host "  .\colors-manager.ps1 convert     # Lancer la conversion automatique" -ForegroundColor Gray
    Write-Host "  .\colors-manager.ps1 validate    # Verifier l'etat des couleurs" -ForegroundColor Gray
    Write-Host "  .\colors-manager.ps1 rollback    # Restaurer une sauvegarde" -ForegroundColor Gray
    Write-Host ""
    Write-Host "FLUX DE TRAVAIL RECOMMANDE:" -ForegroundColor Magenta
    Write-Host "  1. validate  -> Analyser l'etat actuel" -ForegroundColor White
    Write-Host "  2. convert   -> Effectuer la conversion" -ForegroundColor White
    Write-Host "  3. validate  -> Verifier le resultat" -ForegroundColor White
    Write-Host "  4. rollback  -> Restaurer en cas de probleme (optionnel)" -ForegroundColor White
    Write-Host ""
}

function Test-Prerequisites {
    $errors = @()
    
    # Vérifier que nous sommes dans le bon répertoire
    if (-not (Test-Path "artisan")) {
        $errors += "Fichier 'artisan' non trouve. Assurez-vous d'etre dans le repertoire racine de VintApp."
    }
    
    if (-not (Test-Path "resources\views")) {
        $errors += "Dossier 'resources\views' non trouve."
    }
    
    # Vérifier que PHP est disponible
    try {
        $phpVersion = php -v 2>$null
        if (-not $phpVersion) {
            $errors += "PHP non trouve. Assurez-vous que PHP est installe et dans le PATH."
        }
    }
    catch {
        $errors += "Erreur lors de la verification de PHP."
    }
    
    # Vérifier que npm est disponible
    try {
        $npmVersion = npm --version 2>$null
        if (-not $npmVersion) {
            $errors += "npm non trouve. La compilation automatique des assets ne sera pas possible."
        }
    }
    catch {
        $errors += "Erreur lors de la verification de npm."
    }
    
    if ($errors.Count -gt 0) {
        Write-Host "PROBLEMES DETECTES:" -ForegroundColor Red
        foreach ($error in $errors) {
            Write-Host "  $error" -ForegroundColor Red
        }
        Write-Host ""
        return $false
    }
    
    return $true
}

function Invoke-ConvertColors {
    Write-Host "Lancement de la conversion des couleurs..." -ForegroundColor Green
    
    if (Test-Path ".\convert-purple-to-dynamic.ps1") {
        & ".\convert-purple-to-dynamic.ps1"
    }
    else {
        Write-Host "Script convert-purple-to-dynamic.ps1 non trouve !" -ForegroundColor Red
    }
}

function Invoke-ValidateColors {
    Write-Host "Lancement de la validation des couleurs..." -ForegroundColor Cyan
    
    if (Test-Path ".\validate-colors.ps1") {
        & ".\validate-colors.ps1"
    }
    else {
        Write-Host "Script validate-colors.ps1 non trouve !" -ForegroundColor Red
    }
}

function Invoke-RollbackColors {
    Write-Host "Lancement du rollback des couleurs..." -ForegroundColor Red
    
    if (Test-Path ".\rollback-colors.ps1") {
        & ".\rollback-colors.ps1"
    }
    else {
        Write-Host "Script rollback-colors.ps1 non trouve !" -ForegroundColor Red
    }
}

# Exécution principale
Show-Header

if (-not (Test-Prerequisites)) {
    exit 1
}

switch ($Action) {
    "convert" {
        Invoke-ConvertColors
    }
    "validate" {
        Invoke-ValidateColors
    }
    "rollback" {
        Invoke-RollbackColors
    }
    "help" {
        Show-Help
    }
    default {
        Write-Host "❌ Action inconnue: $Action" -ForegroundColor Red
        Write-Host ""
        Show-Help
    }
}

Write-Host "🎨 Gestionnaire de couleurs VintApp terminé." -ForegroundColor Magenta
Write-Host ""