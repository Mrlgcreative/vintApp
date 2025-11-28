# 🚀 Script de génération VintApp Mobile Flutter (PowerShell)
# Exécution: .\create_vintapp_mobile.ps1

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

# 3. Remplacer pubspec.yaml
Write-Host ""
Write-Host "📦 Configuration de pubspec.yaml..." -ForegroundColor Cyan

@'
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
'@ | Out-File -FilePath "pubspec.yaml" -Encoding UTF8

# 4. Créer les fichiers de base
Write-Host ""
Write-Host "📄 Création des fichiers de base..." -ForegroundColor Cyan

# Constants
@'
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
'@ | Out-File -FilePath "lib\core\constants\api_constants.dart" -Encoding UTF8

@'
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
'@ | Out-File -FilePath "lib\core\constants\app_constants.dart" -Encoding UTF8

# User Entity
@'
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
'@ | Out-File -FilePath "lib\domain\entities\user.dart" -Encoding UTF8

# Item Entity
@'
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
'@ | Out-File -FilePath "lib\domain\entities\item.dart" -Encoding UTF8

# Auth Repository Interface
@'
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
'@ | Out-File -FilePath "lib\domain\repositories\auth_repository.dart" -Encoding UTF8

# Theme Configuration
@'
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
'@ | Out-File -FilePath "lib\core\theme\app_theme.dart" -Encoding UTF8

# Main app file
@'
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
'@ | Out-File -FilePath "lib\main.dart" -Encoding UTF8

# Splash Page
New-Item -ItemType Directory -Path "lib\presentation\pages\splash" -Force | Out-Null
@'
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
'@ | Out-File -FilePath "lib\presentation\pages\splash\splash_page.dart" -Encoding UTF8

# 5. Installation des dépendances
Write-Host ""
Write-Host "📦 Installation des dépendances..." -ForegroundColor Cyan
flutter pub get

# 6. Créer des assets par défaut
Write-Host ""
Write-Host "🎨 Création des assets par défaut..." -ForegroundColor Cyan

# Créer un placeholder pour les assets
"Placeholder for VintApp logo" | Out-File -FilePath "assets\images\logo.png" -Encoding UTF8
"Placeholder for Google icon" | Out-File -FilePath "assets\icons\google.png" -Encoding UTF8

# 7. Configuration Android basique
Write-Host ""
Write-Host "🤖 Configuration Android..." -ForegroundColor Cyan

# Créer le fichier build.gradle Android mis à jour
@'
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
'@ | Out-File -FilePath "android\app\build.gradle" -Encoding UTF8

# 8. Instructions finales
Write-Host ""
Write-Host "🎉 VintApp Mobile créé avec succès !" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Projet créé dans: $(Get-Location)" -ForegroundColor Yellow
Write-Host ""
Write-Host "📝 Prochaines étapes:" -ForegroundColor Cyan
Write-Host "1. cd $ProjectName" -ForegroundColor White
Write-Host "2. Configurer Firebase (ajout de google-services.json)" -ForegroundColor White
Write-Host "3. Mettre à jour les URLs d'API dans lib\core\constants\api_constants.dart" -ForegroundColor White
Write-Host "4. Implémenter les pages d'authentification" -ForegroundColor White
Write-Host "5. flutter run" -ForegroundColor White
Write-Host ""
Write-Host "📚 Documentation complète dans FLUTTER_ANALYSIS_COMPLETE.md" -ForegroundColor Magenta
Write-Host ""
Write-Host "✨ Bon développement avec VintApp Mobile ! 🚀" -ForegroundColor Green

exit 0