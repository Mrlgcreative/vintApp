# 🚀 Guide de création VintApp Mobile

## 📱 Création du projet Flutter

### 1️⃣ **Commandes d'initialisation**

```bash
# Créer le projet Flutter
flutter create vintapp_mobile --org com.vintapp --platforms android,ios

cd vintapp_mobile

# Ajouter les dépendances principales
flutter pub add flutter_bloc equatable get_it injectable dio retrofit json_annotation
flutter pub add firebase_auth firebase_messaging google_sign_in sign_in_with_apple
flutter pub add hive_flutter shared_preferences flutter_secure_storage
flutter pub add go_router cached_network_image shimmer
flutter pub add geolocator permission_handler qr_code_scanner qr_flutter
flutter pub add image_picker file_picker

# Dépendances de développement
flutter pub add --dev build_runner json_serializable retrofit_generator injectable_generator
flutter pub add --dev flutter_test mockito build_test
```

### 2️⃣ **Structure des dossiers**

```bash
mkdir -p lib/core/{constants,errors,network,utils,theme}
mkdir -p lib/data/{datasources/{remote,local},models,repositories}
mkdir -p lib/domain/{entities,repositories,usecases}
mkdir -p lib/presentation/{pages/{auth,home,items,orders,wallet,messages,profile},widgets,bloc}
mkdir -p lib/services
mkdir -p assets/{images,icons,fonts}
```

## 🛠️ **Fichiers de configuration**

### **pubspec.yaml**

```yaml
name: vintapp_mobile
description: "VintApp Mobile - E-commerce marketplace"
publish_to: "none"
version: 1.0.0+1

environment:
    sdk: ">=3.2.0 <4.0.0"
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

flutter:
    uses-material-design: true

    assets:
        - assets/images/
        - assets/icons/

    fonts:
        - family: Poppins
          fonts:
              - asset: assets/fonts/Poppins-Regular.ttf
              - asset: assets/fonts/Poppins-Bold.ttf
                weight: 700
```

## 📱 **Modèles de base**

### **lib/domain/entities/user.dart**

```dart
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
  });

  bool get isAdmin => roles.contains('admin');
  double get usdBalance => walletBalances['USD'] ?? 0.0;
  double get cdfBalance => walletBalances['CDF'] ?? 0.0;

  @override
  List<Object?> get props => [
        id,
        name,
        email,
        phone,
        avatar,
        firebaseUid,
        roles,
        walletBalances,
        emailVerified,
      ];
}
```

### **lib/domain/entities/item.dart**

```dart
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
  final ItemStatus status;
  final DateTime createdAt;
  final String? color;
  final String? size;
  final String? itemNumber;

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
    required this.status,
    required this.createdAt,
    this.color,
    this.size,
    this.itemNumber,
  });

  String get mainImage => images.isNotEmpty ? images.first : '';
  bool get isAvailable => status == ItemStatus.active;

  @override
  List<Object?> get props => [
        id,
        name,
        description,
        price,
        currency,
        images,
        categoryId,
        brandId,
        sellerId,
        status,
        createdAt,
        color,
        size,
        itemNumber,
      ];
}
```

## 🌐 **Services réseau**

### **lib/core/network/api_client.dart**

```dart
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:injectable/injectable.dart';

@singleton
class ApiClient {
  late Dio _dio;
  final FlutterSecureStorage _storage;

  ApiClient(this._storage) {
    _dio = Dio(BaseOptions(
      baseUrl: 'https://your-vintapp-api.com/api/mobile/v1',
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ));

    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: _onRequest,
        onError: _onError,
      ),
    );
  }

  Future<void> _onRequest(
    RequestOptions options,
    RequestInterceptorHandler handler,
  ) async {
    final token = await _storage.read(key: 'auth_token');
    if (token != null) {
      options.headers['Authorization'] = 'Bearer $token';
    }
    handler.next(options);
  }

  Future<void> _onError(
    DioException error,
    ErrorInterceptorHandler handler,
  ) async {
    if (error.response?.statusCode == 401) {
      // Token expired, redirect to login
      await _storage.delete(key: 'auth_token');
      // Navigate to login screen
    }
    handler.next(error);
  }

  Dio get dio => _dio;
}
```

### **lib/data/datasources/remote/auth_remote_datasource.dart**

```dart
import 'package:dio/dio.dart';
import 'package:injectable/injectable.dart';
import 'package:retrofit/retrofit.dart';

part 'auth_remote_datasource.g.dart';

@RestApi()
@injectable
abstract class AuthRemoteDataSource {
  @factoryMethod
  factory AuthRemoteDataSource(Dio dio) = _AuthRemoteDataSource;

  @POST('/auth/login')
  Future<AuthResponse> login(@Body() LoginRequest request);

  @POST('/auth/register')
  Future<AuthResponse> register(@Body() RegisterRequest request);

  @POST('/auth/firebase-login')
  Future<AuthResponse> firebaseLogin(@Body() FirebaseLoginRequest request);

  @POST('/auth/logout')
  Future<void> logout();

  @POST('/auth/refresh')
  Future<AuthResponse> refreshToken();
}

class LoginRequest {
  final String email;
  final String password;

  LoginRequest({required this.email, required this.password});

  Map<String, dynamic> toJson() => {
        'email': email,
        'password': password,
      };
}

class AuthResponse {
  final User user;
  final String token;
  final int expiresIn;

  AuthResponse({
    required this.user,
    required this.token,
    required this.expiresIn,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) => AuthResponse(
        user: User.fromJson(json['user']),
        token: json['token'],
        expiresIn: json['expires_in'],
      );
}
```

## 🏗️ **State Management (BLoC)**

### **lib/presentation/bloc/auth/auth_bloc.dart**

```dart
import 'package:equatable/equatable.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:injectable/injectable.dart';

// Events
abstract class AuthEvent extends Equatable {
  @override
  List<Object?> get props => [];
}

class AuthLoginRequested extends AuthEvent {
  final String email;
  final String password;

  AuthLoginRequested({required this.email, required this.password});

  @override
  List<Object?> get props => [email, password];
}

class AuthLogoutRequested extends AuthEvent {}

class AuthStatusChecked extends AuthEvent {}

// States
abstract class AuthState extends Equatable {
  @override
  List<Object?> get props => [];
}

class AuthInitial extends AuthState {}

class AuthLoading extends AuthState {}

class AuthAuthenticated extends AuthState {
  final User user;

  AuthAuthenticated(this.user);

  @override
  List<Object?> get props => [user];
}

class AuthUnauthenticated extends AuthState {}

class AuthError extends AuthState {
  final String message;

  AuthError(this.message);

  @override
  List<Object?> get props => [message];
}

// BLoC
@injectable
class AuthBloc extends Bloc<AuthEvent, AuthState> {
  final AuthRepository _authRepository;

  AuthBloc(this._authRepository) : super(AuthInitial()) {
    on<AuthLoginRequested>(_onLoginRequested);
    on<AuthLogoutRequested>(_onLogoutRequested);
    on<AuthStatusChecked>(_onStatusChecked);
  }

  Future<void> _onLoginRequested(
    AuthLoginRequested event,
    Emitter<AuthState> emit,
  ) async {
    emit(AuthLoading());
    try {
      final user = await _authRepository.login(event.email, event.password);
      emit(AuthAuthenticated(user));
    } catch (e) {
      emit(AuthError(e.toString()));
    }
  }

  Future<void> _onLogoutRequested(
    AuthLogoutRequested event,
    Emitter<AuthState> emit,
  ) async {
    await _authRepository.logout();
    emit(AuthUnauthenticated());
  }

  Future<void> _onStatusChecked(
    AuthStatusChecked event,
    Emitter<AuthState> emit,
  ) async {
    final user = await _authRepository.getCurrentUser();
    if (user != null) {
      emit(AuthAuthenticated(user));
    } else {
      emit(AuthUnauthenticated());
    }
  }
}
```

## 🎨 **Interface utilisateur**

### **lib/presentation/pages/auth/login_page.dart**

```dart
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:go_router/go_router.dart';

class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _formKey = GlobalKey<FormState>();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: BlocListener<AuthBloc, AuthState>(
        listener: (context, state) {
          if (state is AuthAuthenticated) {
            context.go('/home');
          } else if (state is AuthError) {
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(content: Text(state.message)),
            );
          }
        },
        child: SafeArea(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Form(
              key: _formKey,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Logo
                  Image.asset(
                    'assets/images/logo.png',
                    height: 120,
                  ),
                  const SizedBox(height: 32),

                  // Title
                  Text(
                    'Bienvenue sur VintApp',
                    style: Theme.of(context).textTheme.headlineMedium,
                  ),
                  const SizedBox(height: 32),

                  // Email Field
                  TextFormField(
                    controller: _emailController,
                    decoration: const InputDecoration(
                      labelText: 'Email',
                      border: OutlineInputBorder(),
                      prefixIcon: Icon(Icons.email),
                    ),
                    validator: (value) {
                      if (value?.isEmpty ?? true) {
                        return 'Veuillez saisir votre email';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),

                  // Password Field
                  TextFormField(
                    controller: _passwordController,
                    obscureText: true,
                    decoration: const InputDecoration(
                      labelText: 'Mot de passe',
                      border: OutlineInputBorder(),
                      prefixIcon: Icon(Icons.lock),
                    ),
                    validator: (value) {
                      if (value?.isEmpty ?? true) {
                        return 'Veuillez saisir votre mot de passe';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 24),

                  // Login Button
                  BlocBuilder<AuthBloc, AuthState>(
                    builder: (context, state) {
                      return SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          onPressed: state is AuthLoading
                              ? null
                              : () => _onLoginPressed(context),
                          child: state is AuthLoading
                              ? const CircularProgressIndicator()
                              : const Text('Se connecter'),
                        ),
                      );
                    },
                  ),

                  const SizedBox(height: 16),

                  // Social Login Buttons
                  _buildSocialButtons(),

                  const SizedBox(height: 16),

                  // Register Link
                  TextButton(
                    onPressed: () => context.push('/register'),
                    child: const Text('Créer un compte'),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildSocialButtons() {
    return Column(
      children: [
        const Text('OU'),
        const SizedBox(height: 16),
        SizedBox(
          width: double.infinity,
          child: OutlinedButton.icon(
            onPressed: () => _onGoogleLogin(context),
            icon: Image.asset('assets/icons/google.png', height: 20),
            label: const Text('Continuer avec Google'),
          ),
        ),
        const SizedBox(height: 8),
        SizedBox(
          width: double.infinity,
          child: OutlinedButton.icon(
            onPressed: () => _onAppleLogin(context),
            icon: const Icon(Icons.apple, size: 20),
            label: const Text('Continuer avec Apple'),
          ),
        ),
      ],
    );
  }

  void _onLoginPressed(BuildContext context) {
    if (_formKey.currentState?.validate() ?? false) {
      context.read<AuthBloc>().add(
            AuthLoginRequested(
              email: _emailController.text.trim(),
              password: _passwordController.text,
            ),
          );
    }
  }

  void _onGoogleLogin(BuildContext context) {
    // Implement Google Sign In
  }

  void _onAppleLogin(BuildContext context) {
    // Implement Apple Sign In
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }
}
```

## 📱 **Configuration Android**

### **android/app/build.gradle**

```gradle
android {
    compileSdk 34
    ndkVersion "25.1.8937393"

    compileOptions {
        sourceCompatibility JavaVersion.VERSION_1_8
        targetCompatibility JavaVersion.VERSION_1_8
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

dependencies {
    implementation 'androidx.multidex:multidex:2.0.1'
}
```

---

## ✅ **Actions suivantes**

1. **Créer le projet** avec les commandes ci-dessus
2. **Configurer Firebase** (authentification + messaging)
3. **Adapter les APIs Laravel** pour mobile
4. **Développer les écrans prioritaires** (auth + home + items)
5. **Tester sur émulateurs/appareils**

Cette structure vous donne une base solide pour développer VintApp Mobile avec une architecture propre et scalable ! 🚀
