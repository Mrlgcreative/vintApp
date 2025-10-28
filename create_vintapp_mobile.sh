#!/bin/bash

# 🚀 Script de génération VintApp Mobile Flutter
# Exécution: ./create_vintapp_mobile.sh

set -e  # Arrêter en cas d'erreur

echo "🚀 Création de VintApp Mobile Flutter"
echo "===================================="

# Variables de configuration
PROJECT_NAME="vintapp_mobile"
ORG_NAME="com.vintapp"
PROJECT_DIR="../$PROJECT_NAME"

# Vérifier Flutter
if ! command -v flutter &> /dev/null; then
    echo "❌ Flutter n'est pas installé. Veuillez installer Flutter d'abord."
    exit 1
fi

echo "✅ Flutter version:"
flutter --version

# 1. Créer le projet Flutter
echo ""
echo "📱 Création du projet Flutter..."
flutter create $PROJECT_NAME --org $ORG_NAME --platforms android,ios
cd $PROJECT_NAME

# 2. Créer la structure des dossiers
echo ""
echo "📁 Création de la structure des dossiers..."

# Core
mkdir -p lib/core/{constants,errors,network,utils,theme}

# Data Layer
mkdir -p lib/data/datasources/{remote,local}
mkdir -p lib/data/{models,repositories}

# Domain Layer  
mkdir -p lib/domain/{entities,repositories,usecases}

# Presentation Layer
mkdir -p lib/presentation/pages/{auth,home,items,orders,wallet,messages,profile}
mkdir -p lib/presentation/{widgets,bloc}

# Services
mkdir -p lib/services

# Assets
mkdir -p assets/{images,icons,fonts}

echo "✅ Structure des dossiers créée"

# 3. Remplacer pubspec.yaml
echo ""
echo "📦 Configuration de pubspec.yaml..."

cat > pubspec.yaml << 'EOF'
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
EOF

# 4. Créer les fichiers de base
echo ""
echo "📄 Création des fichiers de base..."

# Constants
cat > lib/core/constants/api_constants.dart << 'EOF'
class ApiConstants {
  static const String baseUrl = 'https://your-vintapp-api.com/api/mobile/v1';
  static const String loginEndpoint = '/auth/login';
  static const String registerEndpoint = '/auth/register';
  static const String itemsEndpoint = '/items';
  static const String ordersEndpoint = '/orders';
  static const String walletEndpoint = '/wallet';
  static const String messagesEndpoint = '/messages';
  
  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
}
EOF

cat > lib/core/constants/app_constants.dart << 'EOF'
class AppConstants {
  // App Info
  static const String appName = 'VintApp';
  static const String appVersion = '1.0.0';
  
  // Storage Keys
  static const String authTokenKey = 'auth_token';
  static const String userDataKey = 'user_data';
  static const String themeKey = 'theme_mode';
  
  // Currencies
  static const String usdCurrency = 'USD';
  static const String cdfCurrency = 'CDF';
  
  // Pagination
  static const int defaultPageSize = 20;
}
EOF

# User Entity
cat > lib/domain/entities/user.dart << 'EOF'
import 'package:equatable/equatable.dart';

class User extends Equatable {
  final String id;
  final String name;
  final String email;
  final String? phone;
  final String? avatar;
  final String? firebaseUid;
  final List<String> roles;
  final Map<String, double> walletBalances;
  final bool emailVerified;
  final DateTime? lastSeen;

  const User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.avatar,
    this.firebaseUid,
    required this.roles,
    required this.walletBalances,
    required this.emailVerified,
    this.lastSeen,
  });

  bool get isAdmin => roles.contains('admin');
  double get usdBalance => walletBalances['USD'] ?? 0.0;
  double get cdfBalance => walletBalances['CDF'] ?? 0.0;
  bool get isOnline => lastSeen != null && 
      DateTime.now().difference(lastSeen!).inMinutes < 2;

  @override
  List<Object?> get props => [
        id, name, email, phone, avatar, firebaseUid,
        roles, walletBalances, emailVerified, lastSeen,
      ];
}
EOF

# Item Entity
cat > lib/domain/entities/item.dart << 'EOF'
import 'package:equatable/equatable.dart';

enum ItemStatus { active, sold, pending, inactive }

class Item extends Equatable {
  final String id;
  final String name;
  final String description;
  final double price;
  final String currency;
  final List<String> images;
  final String categoryId;
  final String brandId;
  final String sellerId;
  final String sellerName;
  final ItemStatus status;
  final DateTime createdAt;
  final String? color;
  final String? size;
  final String? itemNumber;
  final bool isPersonalized;

  const Item({
    required this.id,
    required this.name,
    required this.description,
    required this.price,
    required this.currency,
    required this.images,
    required this.categoryId,
    required this.brandId,
    required this.sellerId,
    required this.sellerName,
    required this.status,
    required this.createdAt,
    this.color,
    this.size,
    this.itemNumber,
    this.isPersonalized = false,
  });

  String get mainImage => images.isNotEmpty ? images.first : '';
  bool get isAvailable => status == ItemStatus.active;
  String get formattedPrice => '$price $currency';

  @override
  List<Object?> get props => [
        id, name, description, price, currency, images,
        categoryId, brandId, sellerId, sellerName, status,
        createdAt, color, size, itemNumber, isPersonalized,
      ];
}
EOF

# Auth Repository Interface
cat > lib/domain/repositories/auth_repository.dart << 'EOF'
import '../entities/user.dart';

abstract class AuthRepository {
  Future<User> login(String email, String password);
  Future<User> register(String name, String email, String password);
  Future<User> loginWithGoogle();
  Future<User> loginWithApple();
  Future<User> loginWithFirebase(String idToken);
  Future<void> logout();
  Future<User?> getCurrentUser();
  Future<bool> isLoggedIn();
  Future<void> saveAuthToken(String token);
  Future<String?> getAuthToken();
}
EOF

# Theme Configuration
cat > lib/core/theme/app_theme.dart << 'EOF'
import 'package:flutter/material.dart';

class AppTheme {
  static const Color primaryColor = Color(0xFF2563EB);
  static const Color secondaryColor = Color(0xFF10B981);
  static const Color errorColor = Color(0xFFEF4444);
  static const Color warningColor = Color(0xFFF59E0B);
  static const Color successColor = Color(0xFF10B981);
  
  static ThemeData lightTheme = ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: primaryColor,
      brightness: Brightness.light,
    ),
    fontFamily: 'Poppins',
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
        padding: const EdgeInsets.symmetric(
          horizontal: 24,
          vertical: 12,
        ),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
      ),
      contentPadding: const EdgeInsets.symmetric(
        horizontal: 16,
        vertical: 12,
      ),
    ),
    cardTheme: CardTheme(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
    ),
  );

  static ThemeData darkTheme = ThemeData(
    useMaterial3: true,
    colorScheme: ColorScheme.fromSeed(
      seedColor: primaryColor,
      brightness: Brightness.dark,
    ),
    fontFamily: 'Poppins',
    elevatedButtonTheme: ElevatedButtonThemeData(
      style: ElevatedButton.styleFrom(
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
        padding: const EdgeInsets.symmetric(
          horizontal: 24,
          vertical: 12,
        ),
      ),
    ),
    inputDecorationTheme: InputDecorationTheme(
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
      ),
      contentPadding: const EdgeInsets.symmetric(
        horizontal: 16,
        vertical: 12,
      ),
    ),
    cardTheme: CardTheme(
      elevation: 2,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
    ),
  );
}
EOF

# Main app file
cat > lib/main.dart << 'EOF'
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:hive_flutter/hive_flutter.dart';

import 'core/theme/app_theme.dart';
import 'presentation/pages/splash/splash_page.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Initialize Hive
  await Hive.initFlutter();
  
  // Initialize Firebase (add firebase configuration here)
  
  runApp(const VintApp());
}

class VintApp extends StatelessWidget {
  const VintApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VintApp Mobile',
      theme: AppTheme.lightTheme,
      darkTheme: AppTheme.darkTheme,
      themeMode: ThemeMode.system,
      debugShowCheckedModeBanner: false,
      home: const SplashPage(),
    );
  }
}
EOF

# Splash Page
cat > lib/presentation/pages/splash/splash_page.dart << 'EOF'
import 'package:flutter/material.dart';
import 'package:flutter_spinkit/flutter_spinkit.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage> {
  @override
  void initState() {
    super.initState();
    _checkAuthStatus();
  }

  Future<void> _checkAuthStatus() async {
    // Simulate loading time
    await Future.delayed(const Duration(seconds: 2));
    
    // TODO: Check authentication status
    // For now, navigate to login
    if (mounted) {
      Navigator.of(context).pushReplacementNamed('/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Theme.of(context).colorScheme.primary,
      body: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.shopping_bag,
              size: 100,
              color: Colors.white,
            ),
            SizedBox(height: 24),
            Text(
              'VintApp',
              style: TextStyle(
                fontSize: 32,
                fontWeight: FontWeight.bold,
                color: Colors.white,
              ),
            ),
            SizedBox(height: 8),
            Text(
              'E-commerce pour l\'Afrique',
              style: TextStyle(
                fontSize: 16,
                color: Colors.white70,
              ),
            ),
            SizedBox(height: 48),
            SpinKitWave(
              color: Colors.white,
              size: 50.0,
            ),
          ],
        ),
      ),
    );
  }
}
EOF

# 5. Installation des dépendances
echo ""
echo "📦 Installation des dépendances..."
flutter pub get

# 6. Génération du code
echo ""
echo "🔧 Génération du code initial..."
flutter packages pub run build_runner build --delete-conflicting-outputs

# 7. Créer des assets par défaut
echo ""
echo "🎨 Création des assets par défaut..."

# Créer un logo temporaire (vous pouvez remplacer par votre logo)
mkdir -p assets/images
echo "Placeholder for VintApp logo" > assets/images/logo.png
echo "Placeholder for Google icon" > assets/icons/google.png

# 8. Configuration Android basique
echo ""
echo "🤖 Configuration Android..."

# Mettre à jour build.gradle
cat > android/app/build.gradle << 'EOF'
plugins {
    id "com.android.application"
    id "kotlin-android"
    id "dev.flutter.flutter-gradle-plugin"
}

def localProperties = new Properties()
def localPropertiesFile = rootProject.file('local.properties')
if (localPropertiesFile.exists()) {
    localPropertiesFile.withReader('UTF-8') { reader ->
        localProperties.load(reader)
    }
}

def flutterVersionCode = localProperties.getProperty('flutter.versionCode')
if (flutterVersionCode == null) {
    flutterVersionCode = '1'
}

def flutterVersionName = localProperties.getProperty('flutter.versionName')
if (flutterVersionName == null) {
    flutterVersionName = '1.0'
}

android {
    namespace "com.vintapp.mobile"
    compileSdk 34
    ndkVersion "25.1.8937393"

    compileOptions {
        sourceCompatibility JavaVersion.VERSION_1_8
        targetCompatibility JavaVersion.VERSION_1_8
    }

    kotlinOptions {
        jvmTarget = '1.8'
    }

    sourceSets {
        main.java.srcDirs += 'src/main/kotlin'
    }

    defaultConfig {
        applicationId "com.vintapp.mobile"
        minSdkVersion 21
        targetSdkVersion 34
        versionCode flutterVersionCode.toInteger()
        versionName flutterVersionName
        multiDexEnabled true
    }

    buildTypes {
        release {
            signingConfig signingConfigs.debug
        }
    }
}

flutter {
    source '../..'
}

dependencies {
    implementation 'androidx.multidex:multidex:2.0.1'
}
EOF

# 9. Instructions finales
echo ""
echo "🎉 VintApp Mobile créé avec succès !"
echo "===================================="
echo ""
echo "📍 Projet créé dans: $(pwd)"
echo ""
echo "📝 Prochaines étapes:"
echo "1. cd $PROJECT_NAME"
echo "2. Configurer Firebase (ajout de google-services.json)"
echo "3. Mettre à jour les URLs d'API dans lib/core/constants/api_constants.dart"
echo "4. Implémenter les pages d'authentification"
echo "5. flutter run"
echo ""
echo "📚 Documentation complète dans FLUTTER_ANALYSIS_COMPLETE.md"
echo ""
echo "✨ Bon développement avec VintApp Mobile ! 🚀"

exit 0
EOF