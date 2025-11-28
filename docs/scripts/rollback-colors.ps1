# Script de rollback pour restaurer les couleurs purple-* originales
# VintApp - Restauration des couleurs d'origine

param(
    [string]$BackupPath = ""
)

Write-Host "🔄 ROLLBACK DU SYSTÈME DE COULEURS" -ForegroundColor Red
Write-Host "=" * 50 -ForegroundColor Cyan

# Si aucun chemin de sauvegarde n'est fourni, chercher la plus récente
if ($BackupPath -eq "") {
    $backups = Get-ChildItem -Path "." -Filter "resources_views_backup_*" -Directory | Sort-Object LastWriteTime -Descending
    
    if ($backups.Count -eq 0) {
        Write-Host "❌ Aucune sauvegarde trouvée !" -ForegroundColor Red
        Write-Host "💡 Les sauvegardes suivent le format: resources_views_backup_YYYYMMDD_HHMMSS" -ForegroundColor Yellow
        exit 1
    }
    
    Write-Host "📦 Sauvegardes disponibles:" -ForegroundColor Yellow
    for ($i = 0; $i -lt [Math]::Min($backups.Count, 5); $i++) {
        $backup = $backups[$i]
        Write-Host "  $($i + 1). $($backup.Name) ($(Get-Date $backup.LastWriteTime -Format 'dd/MM/yyyy HH:mm:ss'))" -ForegroundColor Cyan
    }
    
    if ($backups.Count -gt 5) {
        Write-Host "  ... et $($backups.Count - 5) autres" -ForegroundColor Gray
    }
    
    Write-Host ""
    $choice = Read-Host "Choisissez une sauvegarde (1-$([Math]::Min($backups.Count, 5))) ou appuyez sur Entrée pour la plus récente"
    
    if ($choice -eq "") {
        $BackupPath = $backups[0].Name
    }
    elseif ([int]$choice -gt 0 -and [int]$choice -le [Math]::Min($backups.Count, 5)) {
        $BackupPath = $backups[[int]$choice - 1].Name
    }
    else {
        Write-Host "❌ Choix invalide !" -ForegroundColor Red
        exit 1
    }
}

# Vérifier que la sauvegarde existe
if (-not (Test-Path $BackupPath)) {
    Write-Host "❌ Sauvegarde non trouvée: $BackupPath" -ForegroundColor Red
    exit 1
}

Write-Host "📁 Utilisation de la sauvegarde: $BackupPath" -ForegroundColor Green

# Demander confirmation
Write-Host ""
Write-Host "⚠️  ATTENTION: Cette opération va:" -ForegroundColor Yellow
Write-Host "   1. Supprimer le dossier resources/views actuel" -ForegroundColor Yellow
Write-Host "   2. Restaurer les fichiers depuis la sauvegarde" -ForegroundColor Yellow
Write-Host "   3. Perdre toutes les modifications depuis la sauvegarde" -ForegroundColor Yellow
Write-Host ""

$confirmation = Read-Host "Êtes-vous sûr de vouloir continuer ? (tapez 'CONFIRMER' pour continuer)"

if ($confirmation -ne "CONFIRMER") {
    Write-Host "❌ Opération annulée." -ForegroundColor Yellow
    exit 0
}

# Créer une sauvegarde de l'état actuel avant rollback
$currentBackup = "resources_views_pre_rollback_" + (Get-Date -Format "yyyyMMdd_HHmmss")
Write-Host ""
Write-Host "💾 Création d'une sauvegarde de l'état actuel: $currentBackup" -ForegroundColor Cyan
Copy-Item -Path "resources\views" -Destination $currentBackup -Recurse

# Effectuer le rollback
Write-Host "🔄 Suppression du dossier views actuel..." -ForegroundColor Yellow
Remove-Item -Path "resources\views" -Recurse -Force

Write-Host "📦 Restauration depuis la sauvegarde..." -ForegroundColor Yellow
Copy-Item -Path "$BackupPath\*" -Destination "resources\views" -Recurse

# Vérifier le succès
if (Test-Path "resources\views") {
    Write-Host ""
    Write-Host "✅ Rollback terminé avec succès !" -ForegroundColor Green
    Write-Host "🔙 Les fichiers ont été restaurés depuis: $BackupPath" -ForegroundColor Green
    Write-Host "💾 Sauvegarde de l'état précédent: $currentBackup" -ForegroundColor Cyan
    
    # Proposer de reconstruire les assets
    Write-Host ""
    $rebuild = Read-Host "Voulez-vous reconstruire les assets maintenant ? (O/n)"
    if ($rebuild -eq "" -or $rebuild -eq "O" -or $rebuild -eq "o") {
        Write-Host ""
        Write-Host "🔨 Reconstruction des assets..." -ForegroundColor Yellow
        npm run build
        
        Write-Host ""
        Write-Host "🎉 Rollback et reconstruction terminés !" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "💡 NOTES IMPORTANTES:" -ForegroundColor Magenta
    Write-Host "   - Les fichiers de configuration du système de couleurs dynamiques sont toujours présents" -ForegroundColor Yellow
    Write-Host "   - Vous pouvez les supprimer si vous ne voulez plus utiliser le système dynamique" -ForegroundColor Yellow
    Write-Host "   - Pour nettoyer: Remove-Item config\\colors.php, app\\Services\\ColorPaletteService.php" -ForegroundColor Gray
    
}
else {
    Write-Host ""
    Write-Host "❌ Erreur lors du rollback !" -ForegroundColor Red
    Write-Host "🔧 Essayez de restaurer manuellement depuis: $currentBackup" -ForegroundColor Yellow
}

Write-Host ""