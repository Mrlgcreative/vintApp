# 🚀 Guide d'Utilisation - Génération Automatique VintApp Mobile

## Vue d'Ensemble

Ce guide vous accompagne dans la génération automatique du projet Flutter **VintApp Mobile** basé sur l'analyse complète de votre backend Laravel.

## 📋 Prérequis

### 1. Environnement de Développement

```bash
# Vérifier les installations
flutter --version          # Flutter SDK 3.16.0+
dart --version             # Dart SDK 3.2.0+
git --version              # Git pour le versioning
```

### 2. Configuration Flutter

```bash
# Vérifier la configuration
flutter doctor

# Résultats attendus:
✓ Flutter (Channel stable, 3.16.x)
✓ Android toolchain - develop for Android devices
✓ VS Code (version 1.84.0 or later)
✓ Connected device (1 available)
```

### 3. Permissions Requises

-   **Windows** : Exécution de PowerShell avec droits administrateur
-   **macOS/Linux** : Permissions d'écriture dans le répertoire parent
-   **Git** : Accès au repository pour le versioning

## 🎯 Options d'Exécution

### Option 1: Script PowerShell (Windows - Recommandé)

```powershell
# Exécution avec paramètres par défaut
.\create_vintapp_mobile.ps1

# Exécution avec paramètres personnalisés
.\create_vintapp_mobile.ps1 -ProjectName "ma_vintapp" -OrgName "com.monentreprise"

# Vérifier les permissions d'exécution
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Option 2: Script Bash (macOS/Linux)

```bash
# Rendre le script exécutable
chmod +x create_vintapp_mobile.sh

# Exécution
./create_vintapp_mobile.sh

# Avec variables d'environnement
PROJECT_NAME="ma_vintapp" ORG_NAME="com.monentreprise" ./create_vintapp_mobile.sh
```

### Option 3: Exécution Manuelle Étape par Étape

```bash
# 1. Création du projet Flutter
flutter create vintapp_mobile --org com.vintapp --platforms android,ios

# 2. Navigation vers le projet
cd vintapp_mobile

# 3. Ajout des dépendances (voir pubspec.yaml généré)
flutter pub get

# 4. Génération du code
flutter packages pub run build_runner build --delete-conflicting-outputs
```

## 📁 Structure Générée

Le script crée automatiquement cette architecture :

```
vintapp_mobile/
├── lib/
│   ├── core/                 # 🔧 Configuration & Utilitaires
│   │   ├── constants/        # URLs API, constantes app
│   │   ├── theme/           # Thèmes Material Design
│   │   ├── errors/          # Gestion d'erreurs
│   │   ├── network/         # Configuration réseau
│   │   └── utils/           # Helpers & extensions
│   │
│   ├── data/                 # 📊 Couche Données
│   │   ├── datasources/     # Sources de données
│   │   │   ├── remote/      # API calls
│   │   │   └── local/       # Cache & storage
│   │   ├── models/          # Modèles de données
│   │   └── repositories/    # Implémentation repositories
│   │
│   ├── domain/               # 🎯 Logique Métier
│   │   ├── entities/        # Entités business
│   │   ├── repositories/    # Interfaces repositories
│   │   └── usecases/        # Use cases application
│   │
│   ├── presentation/         # 🎨 Interface Utilisateur
│   │   ├── pages/           # Pages de l'application
│   │   │   ├── auth/        # Authentification
│   │   │   ├── home/        # Accueil & navigation
│   │   │   ├── items/       # Gestion articles
│   │   │   ├── orders/      # Commandes
│   │   │   ├── wallet/      # Portefeuille
│   │   │   ├── messages/    # Messagerie
│   │   │   └── profile/     # Profil utilisateur
│   │   ├── widgets/         # Composants réutilisables
│   │   └── bloc/            # Gestion d'état BLoC
│   │
│   ├── services/             # 🔗 Services Application
│   └── main.dart            # Point d'entrée
│
├── assets/                   # 🎨 Ressources Statiques
│   ├── images/              # Images & logos
│   ├── icons/               # Icônes
│   └── fonts/               # Polices personnalisées
│
├── android/                  # 🤖 Configuration Android
├── ios/                     # 🍎 Configuration iOS
└── pubspec.yaml             # 📦 Dépendances & Metadata
```

## 🔧 Configuration Post-Génération

### 1. Configuration Firebase

#### Android

```bash
# 1. Créer le projet Firebase
# → Aller sur https://console.firebase.google.com
# → Créer un nouveau projet "VintApp Mobile"

# 2. Ajouter l'application Android
# → Package name: com.vintapp.mobile
# → Télécharger google-services.json
# → Placer dans android/app/

# 3. Configuration build.gradle (déjà fait par le script)
# → android/app/build.gradle mis à jour automatiquement
```

#### iOS

```bash
# 1. Ajouter l'application iOS dans Firebase
# → Bundle ID: com.vintapp.mobile
# → Télécharger GoogleService-Info.plist
# → Placer dans ios/Runner/

# 2. Configuration Xcode
open ios/Runner.xcworkspace
# → Glisser GoogleService-Info.plist dans Xcode
# → Vérifier le target Runner
```

### 2. Configuration API Backend

Modifier `lib/core/constants/api_constants.dart` :

```dart
class ApiConstants {
  // 🌍 URLs d'Environnement
  static const String baseUrl = 'https://vintapp.com/api/mobile/v1';
  // Pour développement local:
  // static const String baseUrl = 'http://10.0.2.2:8000/api/mobile/v1';

  // 🔐 Endpoints d'Authentification
  static const String loginEndpoint = '/auth/login';
  static const String registerEndpoint = '/auth/register';
  static const String refreshTokenEndpoint = '/auth/refresh';
  static const String logoutEndpoint = '/auth/logout';

  // 📱 Endpoints Métier
  static const String itemsEndpoint = '/items';
  static const String ordersEndpoint = '/orders';
  static const String walletEndpoint = '/wallet';
  static const String messagesEndpoint = '/messages';
  static const String categoriesEndpoint = '/categories';
  static const String brandsEndpoint = '/brands';

  // ⚙️ Configuration Réseau
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  static const Duration sendTimeout = Duration(seconds: 30);
}
```

### 3. Configuration des Permissions

#### Android (`android/app/src/main/AndroidManifest.xml`)

```xml
<manifest xmlns:android="http://schemas.android.com/apk/res/android">
    <!-- Permissions Internet & Réseau -->
    <uses-permission android:name="android.permission.INTERNET" />
    <uses-permission android:name="android.permission.ACCESS_NETWORK_STATE" />

    <!-- Permissions Localisation -->
    <uses-permission android:name="android.permission.ACCESS_FINE_LOCATION" />
    <uses-permission android:name="android.permission.ACCESS_COARSE_LOCATION" />

    <!-- Permissions Caméra & Galerie -->
    <uses-permission android:name="android.permission.CAMERA" />
    <uses-permission android:name="android.permission.READ_EXTERNAL_STORAGE" />
    <uses-permission android:name="android.permission.WRITE_EXTERNAL_STORAGE" />

    <!-- Permissions Notifications -->
    <uses-permission android:name="android.permission.RECEIVE_BOOT_COMPLETED"/>
    <uses-permission android:name="android.permission.VIBRATE" />
</manifest>
```

#### iOS (`ios/Runner/Info.plist`)

```xml
<key>NSCameraUsageDescription</key>
<string>VintApp utilise la caméra pour prendre des photos d'articles</string>

<key>NSPhotoLibraryUsageDescription</key>
<string>VintApp accède à la galerie pour sélectionner des images</string>

<key>NSLocationWhenInUseUsageDescription</key>
<string>VintApp utilise la localisation pour les restrictions géographiques</string>

<key>NSLocationAlwaysAndWhenInUseUsageDescription</key>
<string>VintApp utilise la localisation pour améliorer l'expérience utilisateur</string>
```

## 🚀 Commandes de Développement

### Développement Local

```bash
# Lancement avec hot reload
flutter run

# Spécifier un device
flutter run -d chrome          # Web
flutter run -d android         # Android
flutter run -d ios            # iOS

# Mode debug avec logs détaillés
flutter run --verbose
```

### Build de Production

```bash
# Android APK
flutter build apk --release

# Android App Bundle (recommandé pour Play Store)
flutter build appbundle --release

# iOS (nécessite Xcode sur macOS)
flutter build ios --release

# Web
flutter build web --release
```

### Génération de Code

```bash
# Génération modèles & API clients
flutter packages pub run build_runner build

# Génération avec nettoyage des conflits
flutter packages pub run build_runner build --delete-conflicting-outputs

# Surveillance continue des changements
flutter packages pub run build_runner watch
```

### Tests & Qualité

```bash
# Tests unitaires
flutter test

# Tests d'intégration
flutter test integration_test/

# Analyse du code
flutter analyze

# Formatage du code
dart format lib/
```

## 📊 Prochaines Étapes de Développement

### Phase 1: Foundation (Semaine 1-2) ⚡

-   [ ] **Authentification Firebase**

    -   [ ] Login/Register avec email
    -   [ ] Google Sign-In
    -   [ ] Apple Sign-In
    -   [ ] Gestion des tokens

-   [ ] **Navigation & Routing**

    -   [ ] Configuration GoRouter
    -   [ ] Guards d'authentification
    -   [ ] Navigation bottom bar

-   [ ] **State Management**
    -   [ ] Setup BLoC pattern
    -   [ ] AuthBloc
    -   [ ] NavigationBloc

### Phase 2: Core Features (Semaine 3-4) 🎯

-   [ ] **Gestion des Articles**

    -   [ ] Liste des articles
    -   [ ] Détail article
    -   [ ] Recherche & filtres
    -   [ ] Ajout d'articles

-   [ ] **Système de Commandes**
    -   [ ] Panier
    -   [ ] Checkout
    -   [ ] Historique commandes
    -   [ ] Suivi statuts

### Phase 3: Advanced Features (Semaine 5-6) 🚀

-   [ ] **Portefeuille Électronique**

    -   [ ] Consultation soldes USD/CDF
    -   [ ] Transactions
    -   [ ] Rechargement
    -   [ ] Historique

-   [ ] **Messagerie**
    -   [ ] Chat en temps réel
    -   [ ] Notifications push
    -   [ ] Messages système

### Phase 4: Polish & Deploy (Semaine 7-8) ✨

-   [ ] **Optimisations**

    -   [ ] Performance
    -   [ ] Cache intelligent
    -   [ ] Offline mode

-   [ ] **Déploiement**
    -   [ ] Play Store
    -   [ ] App Store
    -   [ ] CI/CD pipeline

## 🔧 Résolution de Problèmes

### Erreur: "Flutter not found"

```bash
# Ajouter Flutter au PATH
export PATH="$PATH:/path/to/flutter/bin"  # macOS/Linux
# ou éditer variables d'environnement Windows
```

### Erreur: "Android licenses not accepted"

```bash
# Accepter les licences Android
flutter doctor --android-licenses
```

### Erreur: "Pod install failed" (iOS)

```bash
cd ios
pod install
cd ..
flutter run
```

### Problème de Build Android

```bash
# Nettoyer le cache
flutter clean
flutter pub get
cd android
./gradlew clean
cd ..
flutter run
```

## 📚 Ressources & Documentation

-   **Flutter Officiel** : https://docs.flutter.dev
-   **BLoC Pattern** : https://bloclibrary.dev
-   **Firebase Flutter** : https://firebase.flutter.dev
-   **Clean Architecture** : https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html

## 🆘 Support & Assistance

En cas de problème :

1. Consultez `FLUTTER_ANALYSIS_COMPLETE.md` pour l'architecture détaillée
2. Vérifiez `flutter doctor` pour les problèmes d'environnement
3. Examinez les logs avec `flutter run --verbose`
4. Référez-vous aux guides Firebase pour l'authentification

---

**🎯 Objectif** : Avoir une application Flutter fonctionnelle en 6-8 semaines avec toutes les fonctionnalités core de VintApp !

**🚀 Bonne chance avec VintApp Mobile !**
