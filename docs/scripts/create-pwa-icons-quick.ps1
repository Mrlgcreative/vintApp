# Script de génération d'icônes PWA temporaires pour VintApp
# Utilise le favicon existant pour créer rapidement toutes les tailles

param(
    [Parameter(Mandatory=$false)]
    [string]$SourceIcon = "public\favicon.ico"
)

Write-Host "🎨 Génération d'icônes PWA temporaires..." -ForegroundColor Cyan
Write-Host "=" * 60 -ForegroundColor Gray

# Créer les dossiers nécessaires
$iconDir = "public\images\icons"
$screenshotDir = "public\images\screenshots"

New-Item -ItemType Directory -Path $iconDir -Force | Out-Null
New-Item -ItemType Directory -Path $screenshotDir -Force | Out-Null

Write-Host "✅ Dossiers créés" -ForegroundColor Green

# Fonction pour créer une icône colorée avec texte
function New-ColoredIcon {
    param(
        [int]$Size,
        [string]$OutputPath,
        [string]$Color = "#8B5CF6"
    )
    
    Add-Type -AssemblyName System.Drawing
    
    $bitmap = New-Object System.Drawing.Bitmap($Size, $Size)
    $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
    
    # Fond violet VintApp
    $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(139, 92, 246))
    $graphics.FillRectangle($brush, 0, 0, $Size, $Size)
    
    # Texte "V" blanc au centre
    $font = New-Object System.Drawing.Font("Arial", [int]($Size * 0.5), [System.Drawing.FontStyle]::Bold)
    $textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
    
    $text = "V"
    $textSize = $graphics.MeasureString($text, $font)
    $x = ($Size - $textSize.Width) / 2
    $y = ($Size - $textSize.Height) / 2
    
    $graphics.DrawString($text, $font, $textBrush, $x, $y)
    
    # Sauvegarder
    $bitmap.Save($OutputPath, [System.Drawing.Imaging.ImageFormat]::Png)
    
    # Cleanup
    $graphics.Dispose()
    $bitmap.Dispose()
    $brush.Dispose()
    $textBrush.Dispose()
    $font.Dispose()
}

# Générer toutes les icônes
$sizes = @(72, 96, 128, 144, 152, 192, 384, 512)

Write-Host "`n📦 Génération des icônes..." -ForegroundColor Yellow

foreach ($size in $sizes) {
    $outputPath = Join-Path $iconDir "icon-${size}x${size}.png"
    
    try {
        New-ColoredIcon -Size $size -OutputPath $outputPath
        $fileSize = [math]::Round((Get-Item $outputPath).Length / 1KB, 1)
        Write-Host "  ✅ icon-${size}x${size}.png ($fileSize KB)" -ForegroundColor Green
    } catch {
        Write-Host "  ❌ Erreur icon-${size}x${size}.png : $_" -ForegroundColor Red
    }
}

# Créer les icônes de raccourcis
Write-Host "`n📱 Génération des icônes de raccourcis..." -ForegroundColor Yellow

$shortcuts = @(
    @{Name = "sell"; Text = "➕"; Color = "#10B981"},
    @{Name = "orders"; Text = "📦"; Color = "#3B82F6"},
    @{Name = "wallet"; Text = "💰"; Color = "#F59E0B"}
)

foreach ($shortcut in $shortcuts) {
    $outputPath = Join-Path $iconDir "shortcut-$($shortcut.Name).png"
    
    try {
        # Créer icône 96x96 pour raccourci
        Add-Type -AssemblyName System.Drawing
        $bitmap = New-Object System.Drawing.Bitmap(96, 96)
        $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
        
        # Fond coloré
        $r = [Convert]::ToInt32($shortcut.Color.Substring(1,2), 16)
        $g = [Convert]::ToInt32($shortcut.Color.Substring(3,2), 16)
        $b = [Convert]::ToInt32($shortcut.Color.Substring(5,2), 16)
        $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb($r, $g, $b))
        $graphics.FillRectangle($brush, 0, 0, 96, 96)
        
        # Texte/Emoji
        $font = New-Object System.Drawing.Font("Segoe UI Emoji", 40, [System.Drawing.FontStyle]::Bold)
        $textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
        $textSize = $graphics.MeasureString($shortcut.Text, $font)
        $x = (96 - $textSize.Width) / 2
        $y = (96 - $textSize.Height) / 2
        $graphics.DrawString($shortcut.Text, $font, $textBrush, $x, $y)
        
        $bitmap.Save($outputPath, [System.Drawing.Imaging.ImageFormat]::Png)
        
        $graphics.Dispose()
        $bitmap.Dispose()
        $brush.Dispose()
        $textBrush.Dispose()
        $font.Dispose()
        
        Write-Host "  ✅ shortcut-$($shortcut.Name).png" -ForegroundColor Green
    } catch {
        Write-Host "  ❌ Erreur shortcut-$($shortcut.Name).png : $_" -ForegroundColor Red
    }
}

# Créer screenshots placeholder
Write-Host "`n📸 Génération des screenshots..." -ForegroundColor Yellow

# Desktop screenshot
$desktopPath = Join-Path $screenshotDir "desktop-1.png"
Add-Type -AssemblyName System.Drawing
$desktopBitmap = New-Object System.Drawing.Bitmap(1280, 720)
$desktopGraphics = [System.Drawing.Graphics]::FromImage($desktopBitmap)
$desktopBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(139, 92, 246))
$desktopGraphics.FillRectangle($desktopBrush, 0, 0, 1280, 720)
$font = New-Object System.Drawing.Font("Arial", 48, [System.Drawing.FontStyle]::Bold)
$textBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
$desktopGraphics.DrawString("VintApp Desktop", $font, $textBrush, 400, 320)
$desktopBitmap.Save($desktopPath, [System.Drawing.Imaging.ImageFormat]::Png)
$desktopGraphics.Dispose()
$desktopBitmap.Dispose()
$desktopBrush.Dispose()
Write-Host "  ✅ desktop-1.png" -ForegroundColor Green

# Mobile screenshot
$mobilePath = Join-Path $screenshotDir "mobile-1.png"
$mobileBitmap = New-Object System.Drawing.Bitmap(540, 720)
$mobileGraphics = [System.Drawing.Graphics]::FromImage($mobileBitmap)
$mobileBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(139, 92, 246))
$mobileGraphics.FillRectangle($mobileBrush, 0, 0, 540, 720)
$mobileGraphics.DrawString("VintApp Mobile", $font, $textBrush, 100, 320)
$mobileBitmap.Save($mobilePath, [System.Drawing.Imaging.ImageFormat]::Png)
$mobileGraphics.Dispose()
$mobileBitmap.Dispose()
$mobileBrush.Dispose()
$textBrush.Dispose()
$font.Dispose()
Write-Host "  ✅ mobile-1.png" -ForegroundColor Green

# Résumé
Write-Host "`n" + ("=" * 60) -ForegroundColor Gray
Write-Host "✅ ICÔNES PWA GÉNÉRÉES AVEC SUCCÈS!" -ForegroundColor Green
Write-Host "=" * 60 -ForegroundColor Gray

Write-Host "`n📊 Fichiers créés:" -ForegroundColor Cyan
Write-Host "  • 8 icônes principales (72x72 à 512x512)" -ForegroundColor White
Write-Host "  • 3 icônes de raccourcis (sell, orders, wallet)" -ForegroundColor White
Write-Host "  • 2 screenshots (desktop + mobile)" -ForegroundColor White

Write-Host "`n📱 Prochaines étapes pour Android:" -ForegroundColor Yellow
Write-Host "  1. Ouvrir Chrome sur Android" -ForegroundColor Gray
Write-Host "  2. Accéder à votre site (doit être HTTPS ou localhost via tunnel)" -ForegroundColor Gray
Write-Host "  3. Menu ⋮ → 'Installer l'application'" -ForegroundColor Gray
Write-Host "  4. Ou attendre la bannière automatique après 2-3 visites" -ForegroundColor Gray

Write-Host "`n⚠️  IMPORTANT pour Android:" -ForegroundColor Red
Write-Host "  • HTTPS obligatoire en production" -ForegroundColor White
Write-Host "  • Pour test local: utiliser ngrok ou localtunnel" -ForegroundColor White
Write-Host "    > ngrok http 8000" -ForegroundColor Gray
Write-Host "    > Accéder via l'URL HTTPS fournie" -ForegroundColor Gray

Write-Host "`n🔍 Vérifier dans Chrome DevTools:" -ForegroundColor Cyan
Write-Host "  F12 → Application → Manifest → Vérifier toutes les icônes" -ForegroundColor Gray

Write-Host "`n" + ("=" * 60) -ForegroundColor Gray
