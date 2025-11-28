# VintApp - Générateur d'Icônes PWA
# Ce script génère toutes les icônes nécessaires pour le PWA

param(
    [Parameter(Mandatory=$false)]
    [string]$LogoPath = "",
    
    [Parameter(Mandatory=$false)]
    [string]$BackgroundColor = "#8B5CF6",
    
    [Parameter(Mandatory=$false)]
    [switch]$UseOnlineGenerator = $false
)

Write-Host "🎨 VintApp - Générateur d'Icônes PWA" -ForegroundColor Cyan
Write-Host "=" * 50 -ForegroundColor Gray

# Tailles requises
$sizes = @(72, 96, 128, 144, 152, 192, 384, 512)
$outputDir = Join-Path $PSScriptRoot "public\icons"

# Fonction pour vérifier ImageMagick
function Test-ImageMagick {
    try {
        $null = magick -version
        return $true
    } catch {
        return $false
    }
}

# Fonction pour vérifier la taille du logo
function Test-LogoSize {
    param([string]$path)
    
    Add-Type -AssemblyName System.Drawing
    $img = [System.Drawing.Image]::FromFile($path)
    $width = $img.Width
    $height = $img.Height
    $img.Dispose()
    
    if ($width -lt 512 -or $height -lt 512) {
        Write-Host "⚠️  Attention: Le logo est petit ($width x $height)" -ForegroundColor Yellow
        Write-Host "   Recommandé: 1024x1024 minimum" -ForegroundColor Yellow
    }
    
    if ($width -ne $height) {
        Write-Host "⚠️  Attention: Le logo n'est pas carré ($width x $height)" -ForegroundColor Yellow
    }
}

# Fonction pour générer avec ImageMagick
function New-IconsWithImageMagick {
    param(
        [string]$logo,
        [string]$output,
        [array]$iconSizes
    )
    
    Write-Host "`n📦 Génération avec ImageMagick..." -ForegroundColor Green
    
    foreach ($size in $iconSizes) {
        $outputFile = Join-Path $output "icon-${size}x${size}.png"
        
        try {
            # Commande ImageMagick avec fond violet
            magick convert "$logo" `
                -background "$BackgroundColor" `
                -alpha remove `
                -alpha off `
                -resize "${size}x${size}" `
                "$outputFile"
            
            Write-Host "  ✅ icon-${size}x${size}.png" -ForegroundColor Green
        } catch {
            Write-Host "  ❌ Erreur pour icon-${size}x${size}.png : $_" -ForegroundColor Red
        }
    }
}

# Fonction pour générer avec .NET (fallback)
function New-IconsWithDotNet {
    param(
        [string]$logo,
        [string]$output,
        [array]$iconSizes
    )
    
    Write-Host "`n📦 Génération avec .NET System.Drawing..." -ForegroundColor Green
    
    Add-Type -AssemblyName System.Drawing
    
    $sourceImg = [System.Drawing.Image]::FromFile($logo)
    
    foreach ($size in $iconSizes) {
        $outputFile = Join-Path $output "icon-${size}x${size}.png"
        
        try {
            # Créer bitmap de la bonne taille
            $bitmap = New-Object System.Drawing.Bitmap($size, $size)
            $graphics = [System.Drawing.Graphics]::FromImage($bitmap)
            
            # Fond violet
            $brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(139, 92, 246))
            $graphics.FillRectangle($brush, 0, 0, $size, $size)
            
            # Dessiner le logo redimensionné
            $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
            $graphics.DrawImage($sourceImg, 0, 0, $size, $size)
            
            # Sauvegarder
            $bitmap.Save($outputFile, [System.Drawing.Imaging.ImageFormat]::Png)
            
            # Libérer ressources
            $graphics.Dispose()
            $bitmap.Dispose()
            $brush.Dispose()
            
            Write-Host "  ✅ icon-${size}x${size}.png" -ForegroundColor Green
        } catch {
            Write-Host "  ❌ Erreur pour icon-${size}x${size}.png : $_" -ForegroundColor Red
        }
    }
    
    $sourceImg.Dispose()
}

# Fonction pour ouvrir générateur en ligne
function Open-OnlineGenerator {
    Write-Host "`n🌐 Ouverture du générateur en ligne..." -ForegroundColor Cyan
    
    $generators = @(
        @{
            Name = "RealFaviconGenerator"
            Url = "https://realfavicongenerator.net/"
            Description = "Recommandé - Génère tous les formats"
        },
        @{
            Name = "PWA Builder"
            Url = "https://www.pwabuilder.com/imageGenerator"
            Description = "Optimisé pour PWA"
        },
        @{
            Name = "Favicon.io"
            Url = "https://favicon.io/favicon-converter/"
            Description = "Simple et rapide"
        }
    )
    
    Write-Host "`nGénérateurs disponibles:" -ForegroundColor Yellow
    for ($i = 0; $i -lt $generators.Count; $i++) {
        Write-Host "  $($i + 1). $($generators[$i].Name) - $($generators[$i].Description)" -ForegroundColor Gray
    }
    
    $choice = Read-Host "`nChoisir un générateur (1-$($generators.Count))"
    
    if ($choice -match '^\d+$' -and [int]$choice -ge 1 -and [int]$choice -le $generators.Count) {
        $selected = $generators[[int]$choice - 1]
        Write-Host "Ouverture de $($selected.Name)..." -ForegroundColor Green
        Start-Process $selected.Url
        
        Write-Host "`n📋 Instructions:" -ForegroundColor Yellow
        Write-Host "  1. Téléchargez votre logo sur le site"
        Write-Host "  2. Configurez les options (theme color: $BackgroundColor)"
        Write-Host "  3. Téléchargez le ZIP généré"
        Write-Host "  4. Extrayez les fichiers dans: $outputDir"
        Write-Host "  5. Renommez les fichiers en icon-SIZExSIZE.png"
    } else {
        Write-Host "❌ Choix invalide" -ForegroundColor Red
    }
}

# Fonction pour vérifier les icônes générées
function Test-GeneratedIcons {
    param([string]$directory)
    
    Write-Host "`n🔍 Vérification des icônes générées..." -ForegroundColor Cyan
    
    Add-Type -AssemblyName System.Drawing
    
    $allValid = $true
    
    foreach ($size in $sizes) {
        $filename = "icon-${size}x${size}.png"
        $filepath = Join-Path $directory $filename
        
        if (Test-Path $filepath) {
            $img = [System.Drawing.Image]::FromFile($filepath)
            $width = $img.Width
            $height = $img.Height
            $fileSize = (Get-Item $filepath).Length / 1KB
            $img.Dispose()
            
            if ($width -eq $size -and $height -eq $size) {
                Write-Host "  ✅ $filename ($width x $height, $([math]::Round($fileSize, 1)) KB)" -ForegroundColor Green
            } else {
                Write-Host "  ❌ $filename - Dimensions incorrectes ($width x $height au lieu de $size x $size)" -ForegroundColor Red
                $allValid = $false
            }
        } else {
            Write-Host "  ❌ $filename - Fichier manquant" -ForegroundColor Red
            $allValid = $false
        }
    }
    
    if ($allValid) {
        Write-Host "`n🎉 Toutes les icônes sont valides!" -ForegroundColor Green
    } else {
        Write-Host "`n⚠️  Certaines icônes sont manquantes ou invalides" -ForegroundColor Yellow
    }
    
    return $allValid
}

# MAIN SCRIPT

# Créer le dossier de sortie
if (-not (Test-Path $outputDir)) {
    New-Item -ItemType Directory -Path $outputDir -Force | Out-Null
    Write-Host "📁 Dossier créé: $outputDir" -ForegroundColor Green
}

# Mode générateur en ligne
if ($UseOnlineGenerator) {
    Open-OnlineGenerator
    exit
}

# Chercher un logo si non spécifié
if ([string]::IsNullOrEmpty($LogoPath)) {
    Write-Host "`n🔍 Recherche d'un logo..." -ForegroundColor Yellow
    
    $possibleLogos = @(
        "logo.png",
        "public\logo.png",
        "public\images\logo.png",
        "resources\images\logo.png",
        "public\favicon.ico"
    )
    
    foreach ($logo in $possibleLogos) {
        $fullPath = Join-Path $PSScriptRoot $logo
        if (Test-Path $fullPath) {
            $LogoPath = $fullPath
            Write-Host "  ✅ Logo trouvé: $logo" -ForegroundColor Green
            break
        }
    }
    
    if ([string]::IsNullOrEmpty($LogoPath)) {
        Write-Host "`n❌ Aucun logo trouvé automatiquement" -ForegroundColor Red
        Write-Host "`nOptions:" -ForegroundColor Yellow
        Write-Host "  1. Spécifier le chemin: .\generate-pwa-icons.ps1 -LogoPath 'C:\path\to\logo.png'" -ForegroundColor Gray
        Write-Host "  2. Utiliser un générateur en ligne: .\generate-pwa-icons.ps1 -UseOnlineGenerator" -ForegroundColor Gray
        Write-Host "  3. Placer votre logo dans l'un de ces emplacements:" -ForegroundColor Gray
        $possibleLogos | ForEach-Object { Write-Host "     - $_" -ForegroundColor Gray }
        exit 1
    }
}

# Vérifier que le logo existe
if (-not (Test-Path $LogoPath)) {
    Write-Host "❌ Fichier logo introuvable: $LogoPath" -ForegroundColor Red
    exit 1
}

Write-Host "`n📄 Logo source: $LogoPath" -ForegroundColor Cyan
Test-LogoSize -path $LogoPath

# Vérifier ImageMagick
$hasImageMagick = Test-ImageMagick

if ($hasImageMagick) {
    Write-Host "✅ ImageMagick détecté" -ForegroundColor Green
    New-IconsWithImageMagick -logo $LogoPath -output $outputDir -iconSizes $sizes
} else {
    Write-Host "⚠️  ImageMagick non détecté, utilisation de .NET (qualité réduite)" -ForegroundColor Yellow
    Write-Host "   Installer ImageMagick: choco install imagemagick" -ForegroundColor Gray
    New-IconsWithDotNet -logo $LogoPath -output $outputDir -iconSizes $sizes
}

# Vérifier les icônes générées
$valid = Test-GeneratedIcons -directory $outputDir

# Résumé
Write-Host "`n" + ("=" * 50) -ForegroundColor Gray
Write-Host "📊 RÉSUMÉ" -ForegroundColor Cyan
Write-Host "=" * 50 -ForegroundColor Gray
Write-Host "Dossier de sortie : $outputDir" -ForegroundColor White
Write-Host "Icônes générées   : $($sizes.Count)" -ForegroundColor White
Write-Host "Couleur de fond   : $BackgroundColor" -ForegroundColor White

if ($valid) {
    Write-Host "`n✅ SUCCÈS - Toutes les icônes sont prêtes!" -ForegroundColor Green
    Write-Host "`nProchaines étapes:" -ForegroundColor Yellow
    Write-Host "  1. Vérifier les icônes dans: $outputDir" -ForegroundColor Gray
    Write-Host "  2. Tester avec: php artisan serve" -ForegroundColor Gray
    Write-Host "  3. DevTools → Application → Manifest" -ForegroundColor Gray
    Write-Host "  4. Lighthouse → Generate report (PWA score)" -ForegroundColor Gray
} else {
    Write-Host "`n⚠️  ATTENTION - Certaines icônes nécessitent une vérification" -ForegroundColor Yellow
}

Write-Host "`n🔗 Documentation: PWA_DOCUMENTATION.md" -ForegroundColor Cyan
Write-Host "=" * 50 -ForegroundColor Gray
