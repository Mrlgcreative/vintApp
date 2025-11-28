# Script de generation d'icones PWA pour VintApp
# Version simplifiee sans emojis

Write-Host "Génération d'icônes PWA..." -ForegroundColor Cyan

# Créer les dossiers
$iconDir = "public\images\icons"
$screenshotDir = "public\images\screenshots"

New-Item -ItemType Directory -Path $iconDir -Force | Out-Null
New-Item -ItemType Directory -Path $screenshotDir -Force | Out-Null

Write-Host "Dossiers créés" -ForegroundColor Green

# Fonction pour créer une icône
function New-Icon {
    param([int]$Size, [string]$OutputPath)
    
    Add-Type -AssemblyName System.Drawing
    
    $bitmap = New-Object System.Drawing.Bitmap($Size, $Size)
    $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
    
    # Fond violet VintApp
    $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(139, 92, 246))
    $graphics.FillRectangle($brush, 0, 0, $Size, $Size)
    
    # Texte V blanc
    $fontSize = [int]($Size * 0.5)
    $font = New-Object System.Drawing.Font("Arial", $fontSize, [System.Drawing.FontStyle]::Bold)
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

# Générer icônes principales
$sizes = @(72, 96, 128, 144, 152, 192, 384, 512)

Write-Host "`nGénération des icônes principales..." -ForegroundColor Yellow

foreach ($size in $sizes) {
    $outputPath = Join-Path $iconDir "icon-${size}x${size}.png"
    
    try {
        New-Icon -Size $size -OutputPath $outputPath
        $fileSize = [math]::Round((Get-Item $outputPath).Length / 1KB, 1)
        Write-Host "  OK icon-${size}x${size}.png (${fileSize} KB)" -ForegroundColor Green
    } catch {
        Write-Host "  ERREUR icon-${size}x${size}.png" -ForegroundColor Red
    }
}

# Créer icônes de raccourcis
Write-Host "`nGénération des icônes de raccourcis..." -ForegroundColor Yellow

$shortcuts = @(
    @{Name = "sell"; Color = @(16, 185, 129); Text = "+"},
    @{Name = "orders"; Color = @(59, 130, 246); Text = "O"},
    @{Name = "wallet"; Color = @(245, 158, 11); Text = "W"}
)

foreach ($shortcut in $shortcuts) {
    $outputPath = Join-Path $iconDir "shortcut-$($shortcut.Name).png"
    
    try {
        Add-Type -AssemblyName System.Drawing
        $bitmap = New-Object System.Drawing.Bitmap(96, 96)
        $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
        
        # Fond coloré
        $r = $shortcut.Color[0]
        $g = $shortcut.Color[1]
        $b = $shortcut.Color[2]
        $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb($r, $g, $b))
        $graphics.FillRectangle($brush, 0, 0, 96, 96)
        
        # Texte
        $font = New-Object System.Drawing.Font("Arial", 40, [System.Drawing.FontStyle]::Bold)
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
        
        Write-Host "  OK shortcut-$($shortcut.Name).png" -ForegroundColor Green
    } catch {
        Write-Host "  ERREUR shortcut-$($shortcut.Name).png" -ForegroundColor Red
    }
}

# Screenshots
Write-Host "`nGénération des screenshots..." -ForegroundColor Yellow

# Desktop
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
Write-Host "  OK desktop-1.png" -ForegroundColor Green

# Mobile
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
Write-Host "  OK mobile-1.png" -ForegroundColor Green

Write-Host "`n============================================" -ForegroundColor Gray
Write-Host "ICONES PWA GENEREES AVEC SUCCES!" -ForegroundColor Green
Write-Host "============================================" -ForegroundColor Gray

Write-Host "`nFichiers créés:" -ForegroundColor Cyan
Write-Host "  - 8 icones principales (72x72 à 512x512)" -ForegroundColor White
Write-Host "  - 3 icones de raccourcis" -ForegroundColor White
Write-Host "  - 2 screenshots" -ForegroundColor White

Write-Host "`nPour Android:" -ForegroundColor Yellow
Write-Host "  1. Votre site DOIT être en HTTPS (obligatoire pour PWA)" -ForegroundColor Red
Write-Host "  2. Pour tester localement, utilisez ngrok:" -ForegroundColor White
Write-Host "     > ngrok http 8000" -ForegroundColor Gray
Write-Host "  3. Ouvrez l'URL HTTPS fournie sur Android Chrome" -ForegroundColor White
Write-Host "  4. Menu (3 points) > Installer l'application" -ForegroundColor White
Write-Host "`n  OU attendez la bannière automatique après 2-3 visites" -ForegroundColor Gray

Write-Host "`n============================================" -ForegroundColor Gray
