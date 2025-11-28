# 🤖 Configuration Extension Android Emulator VS Code

## 🎯 **Problème à résoudre**

L'extension Android Emulator de VS Code affiche cette erreur :

```
Error fetching your Android emulators! Make sure your path is correct.
Try running this command: "~\Library\Android\sdk\emulator\emulator" -list-avds
```

**Cause :** L'extension utilise un chemin macOS au lieu du chemin Windows correct.

## 🔧 **Solutions**

### **Solution 1 : Configuration via l'interface VS Code**

1. **Ouvrir les paramètres**

    - Appuyez sur `Ctrl + ,` (virgule)
    - Ou : Menu File > Preferences > Settings

2. **Rechercher l'extension**

    - Dans la barre de recherche, tapez : `android emulator`
    - Cherchez le paramètre `Android Emulator: Emulator Path`

3. **Définir le bon chemin**
    - Cliquez sur "Edit in settings.json"
    - Ou entrez directement le chemin :
    ```
    C:\Users\gloir\AppData\Local\Android\sdk\emulator\emulator.exe
    ```

### **Solution 2 : Configuration via settings.json**

1. **Ouvrir settings.json**

    - Appuyez sur `Ctrl + Shift + P`
    - Tapez : `Preferences: Open Settings (JSON)`
    - Appuyez sur Entrée

2. **Ajouter la configuration**
    ```json
    {
        "androidEmulator.emulatorPath": "C:\\Users\\gloir\\AppData\\Local\\Android\\sdk\\emulator\\emulator.exe",
        "androidEmulator.androidHome": "C:\\Users\\gloir\\AppData\\Local\\Android\\sdk"
    }
    ```

### **Solution 3 : Variables d'environnement**

1. **Ouvrir les variables d'environnement**

    - Appuyez sur `Win + R`
    - Tapez : `sysdm.cpl`
    - Onglet "Avancé" > Variables d'environnement

2. **Ajouter/Vérifier ANDROID_HOME**

    ```
    ANDROID_HOME = C:\Users\gloir\AppData\Local\Android\sdk
    ```

3. **Ajouter au PATH**
    ```
    %ANDROID_HOME%\emulator
    %ANDROID_HOME%\tools
    %ANDROID_HOME%\platform-tools
    ```

## 🎯 **Vérifications**

### **1. Vérifier que l'émulateur existe**

Ouvrez PowerShell et tapez :

```powershell
Test-Path "C:\Users\gloir\AppData\Local\Android\sdk\emulator\emulator.exe"
```

**Résultat attendu :** `True`

### **2. Lister les AVD disponibles**

```powershell
& "C:\Users\gloir\AppData\Local\Android\sdk\emulator\emulator.exe" -list-avds
```

### **3. Créer un AVD si nécessaire**

Si aucun AVD n'existe :

```powershell
& "C:\Users\gloir\AppData\Local\Android\sdk\cmdline-tools\latest\bin\avdmanager.bat" create avd -n "VintApp_Emulator" -k "system-images;android-30;google_apis;x86_64"
```

## 🚀 **Configuration recommandée pour VintApp**

### **Settings VS Code complets**

```json
{
    "androidEmulator.emulatorPath": "C:\\Users\\gloir\\AppData\\Local\\Android\\sdk\\emulator\\emulator.exe",
    "androidEmulator.androidHome": "C:\\Users\\gloir\\AppData\\Local\\Android\\sdk",
    "dart.flutterSdkPath": "C:\\flutter",
    "dart.showInspectorNotificationsForWidgetErrors": false,
    "flutter.debugExternalPackageLibraries": true,
    "flutter.debugSdkLibraries": false
}
```

## 📱 **Créer un émulateur optimal pour VintApp**

### **Via Android Studio (Recommandé)**

1. **Ouvrir Android Studio**
2. **Tools > AVD Manager**
3. **Create Virtual Device**
4. **Configuration recommandée :**
    - **Device :** Pixel 6 Pro
    - **System Image :** Android 13 (API 33) avec Google APIs
    - **RAM :** 4096 MB
    - **Internal Storage :** 8 GB
    - **SD Card :** 2 GB

### **Via ligne de commande**

```powershell
# 1. Lister les images système disponibles
& "C:\Users\gloir\AppData\Local\Android\sdk\cmdline-tools\latest\bin\sdkmanager.bat" --list | findstr "system-images"

# 2. Installer une image système
& "C:\Users\gloir\AppData\Local\Android\sdk\cmdline-tools\latest\bin\sdkmanager.bat" "system-images;android-33;google_apis;x86_64"

# 3. Créer l'AVD
& "C:\Users\gloir\AppData\Local\Android\sdk\cmdline-tools\latest\bin\avdmanager.bat" create avd -n "VintApp_Pixel6" -k "system-images;android-33;google_apis;x86_64" -d "pixel_6_pro"
```

## 🔄 **Redémarrage et test**

### **1. Redémarrer VS Code**

-   Fermez VS Code complètement
-   Rouvrez le projet VintApp

### **2. Tester l'extension**

-   Appuyez sur `Ctrl + Shift + P`
-   Tapez : `Android Emulator: Run Android Emulator`
-   L'extension devrait maintenant fonctionner

### **3. Lancer VintApp sur émulateur**

```powershell
# Naviguer vers le projet
cd "C:\Users\gloir\Desktop\projet\vintapp\vintapp_mobile"

# Lancer sur émulateur
flutter run
```

## 📋 **Checklist de vérification**

-   [ ] Extension Android Emulator installée dans VS Code
-   [ ] Chemin de l'émulateur configuré dans settings.json
-   [ ] Variables d'environnement ANDROID_HOME définies
-   [ ] Au moins un AVD créé
-   [ ] VS Code redémarré après configuration
-   [ ] Test de l'extension réussi

## 🆘 **En cas de problème**

### **Extension non trouvée**

1. Extensions > Rechercher "Android iOS Emulator"
2. Installer l'extension de DiemasMichiels

### **AVD Manager ne fonctionne pas**

1. Ouvrir Android Studio
2. Tools > SDK Manager
3. Vérifier que "Android SDK Command-line Tools" est installé

### **Émulateur lent**

1. Activer la virtualisation dans le BIOS (VT-x/AMD-V)
2. Installer Intel HAXM ou utiliser Hyper-V
3. Augmenter la RAM de l'AVD

---

**🎯 Après cette configuration, vous pourrez lancer VintApp directement sur l'émulateur Android depuis VS Code ! 🚀**
