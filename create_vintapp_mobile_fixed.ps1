# 🚀 Script de génération VintApp Mobile Flutter (PowerShell - Version Corrigée)
# Exécution: .\create_vintapp_mobile_fixed.ps1

param(
    [string]$ProjectName = "vintapp_mobile",
    [string]$OrgName = "com.vintapp"
)

$ErrorActionPreference = "Stop"

Write-Host "🚀 Création de VintApp Mobile Flutter" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green

# Variables de configuration
$ProjectDir = "..\$ProjectName"

# Vérifier Flutter
if (-not (Get-Command flutter -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Flutter n'est pas installé. Veuillez installer Flutter d'abord." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Flutter version:" -ForegroundColor Green
flutter --version

# 1. Créer le projet Flutter
Write-Host ""
Write-Host "📱 Création du projet Flutter..." -ForegroundColor Cyan
flutter create $ProjectName --org $OrgName --platforms android, ios
Set-Location $ProjectName

# 2. Créer la structure des dossiers
Write-Host ""
Write-Host "📁 Création de la structure des dossiers..." -ForegroundColor Cyan

# Core
New-Item -ItemType Directory -Path "lib\core\constants" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\core\errors" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\core\network" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\core\utils" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\core\theme" -Force | Out-Null

# Data Layer
New-Item -ItemType Directory -Path "lib\data\datasources\remote" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\data\datasources\local" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\data\models" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\data\repositories" -Force | Out-Null

# Domain Layer  
New-Item -ItemType Directory -Path "lib\domain\entities" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\domain\repositories" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\domain\usecases" -Force | Out-Null

# Presentation Layer
New-Item -ItemType Directory -Path "lib\presentation\pages\auth" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\pages\home" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\pages\items" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\pages\orders" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\pages\wallet" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\pages\messages" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\pages\profile" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\widgets" -Force | Out-Null
New-Item -ItemType Directory -Path "lib\presentation\bloc" -Force | Out-Null

# Services
New-Item -ItemType Directory -Path "lib\services" -Force | Out-Null

# Assets
New-Item -ItemType Directory -Path "assets\images" -Force | Out-Null
New-Item -ItemType Directory -Path "assets\icons" -Force | Out-Null
New-Item -ItemType Directory -Path "assets\fonts" -Force | Out-Null

Write-Host "✅ Structure des dossiers créée" -ForegroundColor Green

# 3. Créer le fichier pubspec.yaml avec une méthode sûre
Write-Host ""
Write-Host "📦 Configuration de pubspec.yaml..." -ForegroundColor Cyan

# Créer le contenu du pubspec.yaml en évitant les caractères problématiques
$pubspecContent = @"
name: vintapp_mobile
description: "VintApp Mobile - E-commerce marketplace pour l'Afrique"
publish_to: 'none'
version: 1.0.0+1

environment:
  sdk: '>=3.2.0 <4.0.0'
  flutter: ">=3.16.0"

dependencies:
  flutter:
    sdk: flutter
  
  # State Management & Architecture
  flutter_bloc: ^8.1.3
  equatable: ^2.0.5
  get_it: ^7.6.4
  injectable: ^2.3.2
  
  # Network & API
  dio: ^5.3.2
  retrofit: ^4.0.3
  json_annotation: ^4.8.1
  
  # Authentication
  firebase_auth: ^4.15.2
  firebase_messaging: ^14.7.6
  google_sign_in: ^6.1.5
  sign_in_with_apple: ^5.0.0
  
  # Storage
  hive_flutter: ^1.1.0
  shared_preferences: ^2.2.2
  flutter_secure_storage: ^9.0.0
  
  # UI & Navigation
  go_router: ^12.1.1
  cached_network_image: ^3.3.0
  shimmer: ^3.0.0
  flutter_spinkit: ^5.2.0
  
  # Location & QR
  geolocator: ^10.1.0
  permission_handler: ^11.0.1
  qr_code_scanner: ^1.0.1
  qr_flutter: ^4.1.0
  
  # Media
  image_picker: ^1.0.4
  file_picker: ^6.1.1
  
  # Utils
  intl: ^0.19.0
  connectivity_plus: ^5.0.2

dev_dependencies:
  flutter_test:
    sdk: flutter
  
  # Code Generation
  build_runner: ^2.4.7
  json_serializable: ^6.7.1
  retrofit_generator: ^8.0.4
  injectable_generator: ^2.4.1
  
  # Linting
  flutter_lints: ^3.0.0
  mockito: ^5.4.4

flutter:
  uses-material-design: true
  
  assets:
    - assets/images/
    - assets/icons/
    
  fonts:
    - family: Poppins
      fonts:
        - asset: assets/fonts/Poppins-Regular.ttf
        - asset: assets/fonts/Poppins-Medium.ttf
          weight: 500
        - asset: assets/fonts/Poppins-SemiBold.ttf
          weight: 600
        - asset: assets/fonts/Poppins-Bold.ttf
          weight: 700
"@

$pubspecContent | Set-Content -Path "pubspec.yaml" -Encoding UTF8

Write-Host "✅ pubspec.yaml créé" -ForegroundColor Green

# 4. Installation des dépendances
Write-Host ""
Write-Host "📦 Installation des dépendances..." -ForegroundColor Cyan
flutter pub get

Write-Host ""
Write-Host "🎉 VintApp Mobile créé avec succès !" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Projet créé dans: $(Get-Location)" -ForegroundColor Yellow
Write-Host ""
Write-Host "📝 Prochaines étapes:" -ForegroundColor Cyan
Write-Host "1. Configurer Firebase" -ForegroundColor White
Write-Host "2. Implémenter les pages d'authentification" -ForegroundColor White
Write-Host "3. flutter run" -ForegroundColor White
Write-Host ""
Write-Host "✨ Bon développement avec VintApp Mobile ! 🚀" -ForegroundColor Green

exit 0