# Script d'installation automatique Android SDK
# Pour Windows PowerShell

Write-Host "🤖 Installation Android SDK pour VS Code Extension" -ForegroundColor Green
Write-Host "=================================================" -ForegroundColor Green

# Vérifier les prérequis
Write-Host "📋 Vérification des prérequis..." -ForegroundColor Yellow

if (!(Get-Command "java" -ErrorAction SilentlyContinue)) {
    Write-Host "❌ Java n'est pas installé. Installation de Java 11..." -ForegroundColor Red
    
    # Télécharger et installer Java 11 (OpenJDK)
    $javaUrl = "https://download.java.net/java/GA/jdk11/13/GPL/openjdk-11.0.1_windows-x64_bin.zip"
    Write-Host "📥 Téléchargement de Java 11..." -ForegroundColor Yellow
    
    # Pour l'instant, afficher les instructions manuelles
    Write-Host "⚠️  Installez Java manuellement depuis : https://adoptium.net/" -ForegroundColor Yellow
    Write-Host "   Choisir : Temurin 11 (LTS) pour Windows x64" -ForegroundColor Yellow
    Read-Host "Appuyez sur Entrée après avoir installé Java"
}

# Créer le dossier SDK
$sdkPath = "$env:LOCALAPPDATA\Android\Sdk"
Write-Host "📁 Création du dossier SDK : $sdkPath" -ForegroundColor Yellow

if (!(Test-Path $sdkPath)) {
    New-Item -Path $sdkPath -ItemType Directory -Force
    Write-Host "✅ Dossier SDK créé" -ForegroundColor Green
}
else {
    Write-Host "✅ Dossier SDK existe déjà" -ForegroundColor Green
}

# URLs et chemins
$cmdlineToolsUrl = "https://dl.google.com/android/repository/commandlinetools-win-9477386_latest.zip"
$cmdlineToolsZip = "$env:TEMP\commandlinetools-win-latest.zip"
$cmdlineToolsPath = "$sdkPath\cmdline-tools"

Write-Host "📥 Téléchargement des Command Line Tools..." -ForegroundColor Yellow

try {
    # Télécharger les command line tools
    Invoke-WebRequest -Uri $cmdlineToolsUrl -OutFile $cmdlineToolsZip -UseBasicParsing
    Write-Host "✅ Téléchargement terminé" -ForegroundColor Green
    
    # Extraire
    Write-Host "📦 Extraction des outils..." -ForegroundColor Yellow
    Expand-Archive -Path $cmdlineToolsZip -DestinationPath "$cmdlineToolsPath\temp" -Force
    
    # Réorganiser la structure (Android SDK attend cmdline-tools/latest/)
    if (!(Test-Path "$cmdlineToolsPath\latest")) {
        New-Item -Path "$cmdlineToolsPath\latest" -ItemType Directory -Force
    }
    
    Move-Item "$cmdlineToolsPath\temp\cmdline-tools\*" "$cmdlineToolsPath\latest\" -Force
    Remove-Item "$cmdlineToolsPath\temp" -Recurse -Force
    Remove-Item $cmdlineToolsZip -Force
    
    Write-Host "✅ Installation des Command Line Tools terminée" -ForegroundColor Green
    
}
catch {
    Write-Host "❌ Erreur lors du téléchargement : $_" -ForegroundColor Red
    Write-Host "🔧 Solution manuelle :" -ForegroundColor Yellow
    Write-Host "   1. Aller sur https://developer.android.com/studio#command-tools" -ForegroundColor White
    Write-Host "   2. Télécharger 'Command line tools only'" -ForegroundColor White
    Write-Host "   3. Extraire dans : $cmdlineToolsPath\latest\" -ForegroundColor White
    Read-Host "Appuyez sur Entrée après installation manuelle"
}

# Configurer les variables d'environnement
Write-Host "🔧 Configuration des variables d'environnement..." -ForegroundColor Yellow

# ANDROID_HOME
[Environment]::SetEnvironmentVariable("ANDROID_HOME", $sdkPath, [EnvironmentVariableTarget]::User)
[Environment]::SetEnvironmentVariable("ANDROID_SDK_ROOT", $sdkPath, [EnvironmentVariableTarget]::User)

# PATH
$currentPath = [Environment]::GetEnvironmentVariable("PATH", [EnvironmentVariableTarget]::User)
$androidPaths = @(
    "$sdkPath\platform-tools",
    "$sdkPath\emulator", 
    "$sdkPath\cmdline-tools\latest\bin"
)

foreach ($androidPath in $androidPaths) {
    if ($currentPath -notlike "*$androidPath*") {
        $currentPath = "$currentPath;$androidPath"
    }
}

[Environment]::SetEnvironmentVariable("PATH", $currentPath, [EnvironmentVariableTarget]::User)

Write-Host "✅ Variables d'environnement configurées" -ForegroundColor Green
Write-Host "   ANDROID_HOME = $sdkPath" -ForegroundColor White
Write-Host "   PATH mis à jour" -ForegroundColor White

# Recharger les variables d'environnement dans la session actuelle
$env:ANDROID_HOME = $sdkPath
$env:ANDROID_SDK_ROOT = $sdkPath
$env:PATH = "$env:PATH;$sdkPath\platform-tools;$sdkPath\emulator;$sdkPath\cmdline-tools\latest\bin"

Write-Host "🔄 Variables rechargées dans cette session" -ForegroundColor Green

# Installer les composants SDK essentiels
Write-Host "📦 Installation des composants SDK..." -ForegroundColor Yellow

$sdkmanager = "$sdkPath\cmdline-tools\latest\bin\sdkmanager.bat"

if (Test-Path $sdkmanager) {
    Write-Host "📥 Installation de platform-tools..." -ForegroundColor Yellow
    & $sdkmanager "platform-tools" --sdk_root=$sdkPath
    
    Write-Host "📥 Installation d'Android 33 (API Level 33)..." -ForegroundColor Yellow  
    & $sdkmanager "platforms;android-33" --sdk_root=$sdkPath
    
    Write-Host "📥 Installation de l'émulateur..." -ForegroundColor Yellow
    & $sdkmanager "emulator" --sdk_root=$sdkPath
    
    Write-Host "📥 Installation d'une image système..." -ForegroundColor Yellow
    & $sdkmanager "system-images;android-33;google_apis;x86_64" --sdk_root=$sdkPath
    
    Write-Host "✅ Composants SDK installés" -ForegroundColor Green
}
else {
    Write-Host "❌ sdkmanager introuvable à : $sdkmanager" -ForegroundColor Red
}

# Créer un émulateur par défaut
Write-Host "📱 Création d'un émulateur par défaut..." -ForegroundColor Yellow

$avdmanager = "$sdkPath\cmdline-tools\latest\bin\avdmanager.bat"

if (Test-Path $avdmanager) {
    # Créer l'AVD
    $avdName = "VintApp_Pixel7_API33"
    & $avdmanager create avd -n $avdName -k "system-images;android-33;google_apis;x86_64" -d "pixel_7" --force
    Write-Host "✅ Émulateur '$avdName' créé" -ForegroundColor Green
}
else {
    Write-Host "❌ avdmanager introuvable" -ForegroundColor Red
}

# Instructions finales
Write-Host ""
Write-Host "🎉 Installation terminée !" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host ""
Write-Host "🔄 IMPORTANT : Redémarrez VS Code pour appliquer les variables d'environnement" -ForegroundColor Yellow
Write-Host ""
Write-Host "✅ Pour vérifier l'installation :" -ForegroundColor White
Write-Host "   adb version" -ForegroundColor Gray
Write-Host "   emulator -list-avds" -ForegroundColor Gray
Write-Host ""
Write-Host "📱 Pour lancer l'émulateur :" -ForegroundColor White  
Write-Host "   emulator -avd VintApp_Pixel7_API33" -ForegroundColor Gray
Write-Host ""
Write-Host "⚙️  Configuration VS Code Extension :" -ForegroundColor White
Write-Host "   Android SDK Path: $sdkPath" -ForegroundColor Gray
Write-Host "   Emulator Path: $sdkPath\emulator\emulator.exe" -ForegroundColor Gray

Read-Host "Appuyez sur Entrée pour terminer"