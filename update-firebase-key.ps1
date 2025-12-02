# Script de mise à jour de la clé API Firebase
# Usage: .\update-firebase-key.ps1

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  Mise à jour de la clé API Firebase" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host ""

# Demander la nouvelle clé API
$newApiKey = Read-Host "Entrez la nouvelle clé API Firebase"

if ([string]::IsNullOrWhiteSpace($newApiKey)) {
    Write-Host "❌ Erreur: La clé API ne peut pas être vide" -ForegroundColor Red
    exit 1
}

# Vérifier que le fichier .env existe
if (-not (Test-Path ".env")) {
    Write-Host "❌ Erreur: Fichier .env non trouvé" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "📝 Mise à jour du fichier .env..." -ForegroundColor Yellow

# Lire le contenu du fichier .env
$envContent = Get-Content ".env" -Raw

# Remplacer l'ancienne clé API
$envContent = $envContent -replace 'FIREBASE_API_KEY=.*', "FIREBASE_API_KEY=$newApiKey"

# Écrire le nouveau contenu
$envContent | Set-Content ".env" -NoNewline

Write-Host "✅ Fichier .env mis à jour" -ForegroundColor Green

# Vider les caches
Write-Host ""
Write-Host "🧹 Nettoyage des caches..." -ForegroundColor Yellow

php artisan config:clear | Out-Null
php artisan cache:clear | Out-Null
php artisan route:clear | Out-Null

Write-Host "✅ Caches vidés" -ForegroundColor Green

# Afficher la nouvelle clé (masquée partiellement)
$maskedKey = $newApiKey.Substring(0, 10) + "..." + $newApiKey.Substring($newApiKey.Length - 10)
Write-Host ""
Write-Host "✅ Nouvelle clé API configurée: $maskedKey" -ForegroundColor Green

Write-Host ""
Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "  Configuration terminée!" -ForegroundColor Green
Write-Host "  Rechargez votre page pour tester la connexion" -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan
