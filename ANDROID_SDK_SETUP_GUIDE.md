# 🤖 Guide d'installation Android SDK & Émulateur - VintApp

## ❌ Problème détecté

L'extension VS Code ne trouve pas le SDK Android car il n'est pas installé sur votre système.

## 🛠️ Solution : Installation Android Studio

### 1️⃣ **Télécharger Android Studio**

-   Aller sur : https://developer.android.com/studio
-   Télécharger "Android Studio" pour Windows
-   Taille : ~1GB

### 2️⃣ **Installation**

1. Lancer l'installateur Android Studio
2. **Important** : Cocher "Android SDK" et "Android Virtual Device (AVD)"
3. Accepter les emplacements par défaut :
    - Android Studio : `C:\Program Files\Android\Android Studio`
    - SDK : `C:\Users\gloir\AppData\Local\Android\Sdk`

### 3️⃣ **Configuration après installation**

#### Variables d'environnement Windows :

```cmd
# Ajouter dans les variables système :
ANDROID_HOME = C:\Users\gloir\AppData\Local\Android\Sdk
ANDROID_SDK_ROOT = C:\Users\gloir\AppData\Local\Android\Sdk

# Ajouter au PATH :
%ANDROID_HOME%\platform-tools
%ANDROID_HOME%\emulator
%ANDROID_HOME%\tools
```

#### Comment ajouter les variables :

1. Windows + R → `sysdm.cpl` → OK
2. Onglet "Avancé" → "Variables d'environnement"
3. Variables système → "Nouvelle"
4. Redémarrer VS Code

## 🚀 Alternative rapide : SDK Command Line Tools seulement

Si vous ne voulez pas installer tout Android Studio :

### 1️⃣ **Télécharger SDK Command Line Tools**

-   Aller sur : https://developer.android.com/studio#command-tools
-   Télécharger "Command line tools only" (~150MB)

### 2️⃣ **Installation manuelle**

```cmd
# Créer le dossier SDK
mkdir C:\Android\Sdk
cd C:\Android\Sdk

# Extraire les command-line tools dans :
# C:\Android\Sdk\cmdline-tools\latest\

# Installer les composants requis :
cmdline-tools\latest\bin\sdkmanager.bat "platform-tools" "emulator"
cmdline-tools\latest\bin\sdkmanager.bat "platforms;android-30"
cmdline-tools\latest\bin\sdkmanager.bat "system-images;android-30;google_apis;x86_64"
```

### 3️⃣ **Variables d'environnement**

```cmd
ANDROID_HOME = C:\Android\Sdk
PATH += %ANDROID_HOME%\platform-tools;%ANDROID_HOME%\emulator
```

## 📱 Créer un émulateur

### Via Android Studio (recommandé) :

1. Ouvrir Android Studio
2. "More Actions" → "AVD Manager"
3. "Create Virtual Device"
4. Choisir un téléphone (ex: Pixel 7)
5. Télécharger une image système (Android 13/API 33)
6. Finaliser la création

### Via command line :

```cmd
# Créer l'AVD
avdmanager create avd -n "Pixel7API33" -k "system-images;android-33;google_apis;x86_64"

# Lister les AVD créés
emulator -list-avds

# Lancer l'émulateur
emulator -avd Pixel7API33
```

## 🔧 Configuration VS Code Extension

Une fois le SDK installé, configurer l'extension :

### 1️⃣ **Settings VS Code** (Ctrl+,)

Chercher "android emulator" et définir :

-   **Android SDK Path** : `C:\Users\gloir\AppData\Local\Android\Sdk`
-   **Emulator Path** : `C:\Users\gloir\AppData\Local\Android\Sdk\emulator\emulator.exe`

### 2️⃣ **Vérifier la configuration**

-   Ouvrir Command Palette (Ctrl+Shift+P)
-   Taper "Android Emulator: Run Android Emulator"
-   Vos émulateurs devraient apparaître

## ✅ Test de vérification

### Commands à tester dans PowerShell :

```powershell
# Vérifier ADB
adb version

# Lister les émulateurs
emulator -list-avds

# Vérifier les variables d'environnement
echo $env:ANDROID_HOME
```

## 📱 Pour développement Flutter/React Native

Si vous développez des apps mobiles :

### Flutter :

```cmd
flutter doctor
flutter emulators
flutter emulators --launch Pixel7API33
```

### React Native :

```cmd
npx react-native doctor
npx react-native run-android
```

## 🎯 Résumé des étapes

1. ✅ **Installer Android Studio** (ou Command Line Tools)
2. ✅ **Configurer variables d'environnement**
3. ✅ **Créer un AVD (émulateur)**
4. ✅ **Configurer l'extension VS Code**
5. ✅ **Tester avec `emulator -list-avds`**

## 💡 Conseils

### Performance :

-   Activer la virtualisation dans le BIOS
-   Allouer au moins 4GB RAM à l'émulateur
-   Utiliser images x86_64 (plus rapides)

### Troubleshooting :

-   Redémarrer VS Code après installation
-   Vérifier que Windows Hypervisor est activé
-   Désactiver Hyper-V si problèmes de performance

---

**Temps estimé** : 30-45 minutes (téléchargement inclus)  
**Espace requis** : ~8-10GB pour installation complète
