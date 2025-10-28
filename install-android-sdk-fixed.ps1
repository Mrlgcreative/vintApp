# Script d'installation automatique Android SDK
# Pour Windows PowerShell

Write-Host "Installation Android SDK pour VS Code Extension" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Green

# Verifier Java
Write-Host "Verification des prerequis..." -ForegroundColor Yellow

$javaVersion = java -version 2>&1 | Out-String
if ($javaVersion -match "java") {
    Write-Host "Java detecte : OK" -ForegroundColor Green
}
else {
    Write-Host "Java manquant. Installez Java 11+ depuis https://adoptium.net/" -ForegroundColor Red
    Read-Host "Appuyez sur Entree apres installation de Java"
}

# Creer le dossier SDK
$sdkPath = "$env:LOCALAPPDATA\Android\Sdk"
Write-Host "Creation du dossier SDK : $sdkPath" -ForegroundColor Yellow

if (!(Test-Path $sdkPath)) {
    New-Item -Path $sdkPath -ItemType Directory -Force | Out-Null
    Write-Host "Dossier SDK cree" -ForegroundColor Green
}

# Telecharger Command Line Tools
$cmdlineToolsUrl = "https://dl.google.com/android/repository/commandlinetools-win-9477386_latest.zip"
$cmdlineToolsZip = "$env:TEMP\commandlinetools-win-latest.zip"
$cmdlineToolsPath = "$sdkPath\cmdline-tools"

Write-Host "Telechargement des Command Line Tools..." -ForegroundColor Yellow

try {
    # Telecharger
    Invoke-WebRequest -Uri $cmdlineToolsUrl -OutFile $cmdlineToolsZip -UseBasicParsing
    Write-Host "Telechargement termine" -ForegroundColor Green
    
    # Extraire
    Write-Host "Extraction des outils..." -ForegroundColor Yellow
    if (Test-Path "$cmdlineToolsPath\temp") {
        Remove-Item "$cmdlineToolsPath\temp" -Recurse -Force
    }
    Expand-Archive -Path $cmdlineToolsZip -DestinationPath "$cmdlineToolsPath\temp" -Force
    
    # Reorganiser
    if (!(Test-Path "$cmdlineToolsPath\latest")) {
        New-Item -Path "$cmdlineToolsPath\latest" -ItemType Directory -Force | Out-Null
    }
    
    $sourceFiles = Get-ChildItem "$cmdlineToolsPath\temp\cmdline-tools\*" -Force
    foreach ($file in $sourceFiles) {
        Move-Item $file.FullName "$cmdlineToolsPath\latest\" -Force
    }
    
    Remove-Item "$cmdlineToolsPath\temp" -Recurse -Force
    Remove-Item $cmdlineToolsZip -Force
    
    Write-Host "Installation des Command Line Tools terminee" -ForegroundColor Green
    
}
catch {
    Write-Host "Erreur lors du telechargement : $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Installez manuellement depuis https://developer.android.com/studio#command-tools" -ForegroundColor Yellow
    Read-Host "Appuyez sur Entree apres installation manuelle"
}

# Variables d'environnement
Write-Host "Configuration des variables d'environnement..." -ForegroundColor Yellow

[Environment]::SetEnvironmentVariable("ANDROID_HOME", $sdkPath, [EnvironmentVariableTarget]::User)
[Environment]::SetEnvironmentVariable("ANDROID_SDK_ROOT", $sdkPath, [EnvironmentVariableTarget]::User)

$currentPath = [Environment]::GetEnvironmentVariable("PATH", [EnvironmentVariableTarget]::User)
$platformTools = "$sdkPath\platform-tools"
$emulatorPath = "$sdkPath\emulator"
$cmdlineTools = "$sdkPath\cmdline-tools\latest\bin"

if ($currentPath -notlike "*$platformTools*") {
    $newPath = "$currentPath;$platformTools;$emulatorPath;$cmdlineTools"
    [Environment]::SetEnvironmentVariable("PATH", $newPath, [EnvironmentVariableTarget]::User)
}

Write-Host "Variables d'environnement configurees" -ForegroundColor Green

# Recharger dans la session actuelle
$env:ANDROID_HOME = $sdkPath
$env:PATH = "$env:PATH;$platformTools;$emulatorPath;$cmdlineTools"

# Installer les composants SDK
Write-Host "Installation des composants SDK..." -ForegroundColor Yellow

$sdkmanager = "$sdkPath\cmdline-tools\latest\bin\sdkmanager.bat"

if (Test-Path $sdkmanager) {
    Write-Host "Installation de platform-tools..." -ForegroundColor Yellow
    & $sdkmanager "platform-tools" --sdk_root=$sdkPath
    
    Write-Host "Installation d'Android 33..." -ForegroundColor Yellow
    & $sdkmanager "platforms;android-33" --sdk_root=$sdkPath
    
    Write-Host "Installation de l'emulateur..." -ForegroundColor Yellow
    & $sdkmanager "emulator" --sdk_root=$sdkPath
    
    Write-Host "Installation d'une image systeme..." -ForegroundColor Yellow
    & $sdkmanager "system-images;android-33;google_apis;x86_64" --sdk_root=$sdkPath
    
    Write-Host "Composants SDK installes" -ForegroundColor Green
}

# Creer un emulateur
Write-Host "Creation d'un emulateur par defaut..." -ForegroundColor Yellow

$avdmanager = "$sdkPath\cmdline-tools\latest\bin\avdmanager.bat"

if (Test-Path $avdmanager) {
    $avdName = "VintApp_Pixel7_API33"
    & $avdmanager create avd -n $avdName -k "system-images;android-33;google_apis;x86_64" -d "pixel_7" --force
    Write-Host "Emulateur cree : $avdName" -ForegroundColor Green
}

Write-Host ""
Write-Host "Installation terminee !" -ForegroundColor Green
Write-Host "======================" -ForegroundColor Green
Write-Host ""
Write-Host "REDEMARREZ VS CODE maintenant" -ForegroundColor Yellow
Write-Host ""
Write-Host "Pour verifier :" -ForegroundColor White
Write-Host "  adb version"
Write-Host "  emulator -list-avds"
Write-Host ""
Write-Host "Configuration VS Code :" -ForegroundColor White
Write-Host "  Android SDK Path: $sdkPath"
Write-Host "  Emulator Path: $sdkPath\emulator\emulator.exe"

Read-Host "Appuyez sur Entree pour terminer"