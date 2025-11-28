# Script de vérification Push Notifications
# Vérifie que tous les fichiers sont en place et configurés

Write-Host "`n╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  🔍 VÉRIFICATION PUSH NOTIFICATIONS                         ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

$errors = 0
$warnings = 0

# Fonction de vérification
function Check-File {
    param($path, $description)
    if (Test-Path $path) {
        Write-Host "  ✓ $description" -ForegroundColor Green
        return $true
    } else {
        Write-Host "  ✗ $description - MANQUANT" -ForegroundColor Red
        $script:errors++
        return $false
    }
}

function Check-FileContent {
    param($path, $pattern, $description)
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        if ($content -match $pattern) {
            Write-Host "  ✓ $description" -ForegroundColor Green
            return $true
        } else {
            Write-Host "  ⚠ $description - PATTERN NON TROUVÉ" -ForegroundColor Yellow
            $script:warnings++
            return $false
        }
    } else {
        Write-Host "  ✗ $description - FICHIER MANQUANT" -ForegroundColor Red
        $script:errors++
        return $false
    }
}

Write-Host "📁 FICHIERS FRONTEND" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

Check-File "public\sw.js" "Service Worker principal"
Check-File "public\firebase-messaging-sw.js" "Service Worker Firebase"
Check-File "public\js\push-manager.js" "Push Manager JavaScript"
Check-File "resources\views\test-push.blade.php" "Page de test"

Write-Host "`n📁 FICHIERS BACKEND" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

Check-File "app\Services\PushNotificationService.php" "Service Push Notifications"
Check-File "app\Http\Controllers\Api\NotificationController.php" "Notification Controller"

Write-Host "`n🔧 CONFIGURATION" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

# Vérifier .env
if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw
    
    if ($envContent -match "FIREBASE_VAPID_KEY=BAE5dM7Fc4f3s7H5") {
        Write-Host "  ✓ FIREBASE_VAPID_KEY configuré" -ForegroundColor Green
    } else {
        Write-Host "  ✗ FIREBASE_VAPID_KEY non configuré" -ForegroundColor Red
        $errors++
    }
    
    if ($envContent -match "FIREBASE_PROJECT_ID=vintapp-e6fa7") {
        Write-Host "  ✓ FIREBASE_PROJECT_ID configuré" -ForegroundColor Green
    } else {
        Write-Host "  ⚠ FIREBASE_PROJECT_ID non configuré" -ForegroundColor Yellow
        $warnings++
    }
} else {
    Write-Host "  ✗ Fichier .env introuvable" -ForegroundColor Red
    $errors++
}

# Vérifier Service Account (CRITIQUE)
Write-Host "`n🔥 SERVICE ACCOUNT (CRITIQUE)" -ForegroundColor Red -BackgroundColor White
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

$serviceAccountPath = "storage\app\firebase-service-account.json"
if (Test-Path $serviceAccountPath) {
    Write-Host "  ✓ Service Account JSON trouvé" -ForegroundColor Green
    
    # Vérifier le contenu
    try {
        $serviceAccount = Get-Content $serviceAccountPath | ConvertFrom-Json
        
        if ($serviceAccount.project_id -eq "vintapp-e6fa7") {
            Write-Host "  ✓ Project ID correct: vintapp-e6fa7" -ForegroundColor Green
        } else {
            Write-Host "  ✗ Project ID incorrect: $($serviceAccount.project_id)" -ForegroundColor Red
            $errors++
        }
        
        if ($serviceAccount.private_key -match "BEGIN PRIVATE KEY") {
            Write-Host "  ✓ Clé privée présente" -ForegroundColor Green
        } else {
            Write-Host "  ✗ Clé privée manquante ou invalide" -ForegroundColor Red
            $errors++
        }
        
        if ($serviceAccount.client_email -match "firebase-adminsdk") {
            Write-Host "  ✓ Client email valide" -ForegroundColor Green
        } else {
            Write-Host "  ✗ Client email invalide" -ForegroundColor Red
            $errors++
        }
    } catch {
        Write-Host "  ✗ Erreur de parsing JSON: $_" -ForegroundColor Red
        $errors++
    }
} else {
    Write-Host "  ✗ Service Account JSON MANQUANT!" -ForegroundColor Red
    Write-Host "`n  ⚠️  ATTENTION: Sans ce fichier, les notifications backend NE FONCTIONNERONT PAS`n" -ForegroundColor Yellow
    Write-Host "  📥 Télécharger depuis:" -ForegroundColor White
    Write-Host "     https://console.firebase.google.com/project/vintapp-e6fa7/settings/serviceaccounts/adminsdk`n" -ForegroundColor Blue
    $errors++
}

Write-Host "`n🛣️  ROUTES" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

Check-FileContent "routes\api.php" "/api/notifications/subscribe" "Route subscribe"
Check-FileContent "routes\api.php" "/api/notifications/test" "Route test"
Check-FileContent "routes\web.php" "/test-push" "Route page de test"

Write-Host "`n📊 RÉSUMÉ" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

if ($errors -eq 0 -and $warnings -eq 0) {
    Write-Host "  🎉 PARFAIT! Aucun problème détecté." -ForegroundColor Green
    Write-Host "`n  ✨ Prochaines étapes:" -ForegroundColor Cyan
    Write-Host "     1. Ouvrir http://localhost:8000/test-push" -ForegroundColor White
    Write-Host "     2. Tester les notifications" -ForegroundColor White
    Write-Host "     3. Intégrer dans OrderController/MessageController`n" -ForegroundColor White
} elseif ($errors -eq 0) {
    Write-Host "  ⚠️  $warnings avertissement(s) détecté(s)" -ForegroundColor Yellow
    Write-Host "     L'application devrait fonctionner mais certaines optimisations sont possibles.`n" -ForegroundColor Gray
} else {
    Write-Host "  ❌ $errors erreur(s) détectée(s)" -ForegroundColor Red
    if ($warnings -gt 0) {
        Write-Host "  ⚠️  $warnings avertissement(s) détecté(s)" -ForegroundColor Yellow
    }
    Write-Host "`n  🔧 Corrigez les erreurs ci-dessus avant de continuer.`n" -ForegroundColor Yellow
}

Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Cyan

# Statistiques
Write-Host "📈 STATISTIQUES D'INSTALLATION" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Gray

$stats = @{
    "Service Workers" = 2
    "Scripts JavaScript" = 1
    "Services Laravel" = 1
    "Controllers API" = 1
    "Routes API" = 4
    "Méthodes notifications" = 8
    "Pages de test" = 1
    "Docs Markdown" = 3
}

foreach ($key in $stats.Keys) {
    Write-Host "  • $key : " -NoNewline
    Write-Host "$($stats[$key])" -ForegroundColor Cyan
}

Write-Host "`n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━`n" -ForegroundColor Cyan

# Code de sortie
if ($errors -gt 0) {
    exit 1
} else {
    exit 0
}
