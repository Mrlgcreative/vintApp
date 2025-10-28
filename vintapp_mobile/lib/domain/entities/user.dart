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
  final DateTime createdAt;
  final String? googleId;
  final String? appleId;

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
    required this.createdAt,
    this.googleId,
    this.appleId,
  });

  // Getters utiles
  bool get isAdmin => roles.contains('admin');
  bool get isBuyer => roles.contains('buyer');
  bool get isSeller => roles.contains('seller');

  double get usdBalance => walletBalances['USD'] ?? 0.0;
  double get cdfBalance => walletBalances['CDF'] ?? 0.0;

  bool get isOnline =>
      lastSeen != null && DateTime.now().difference(lastSeen!).inMinutes < 2;

  bool get hasGoogleAuth => googleId != null;
  bool get hasAppleAuth => appleId != null;
  bool get hasFirebaseAuth => firebaseUid != null;

  String get displayName => name.isNotEmpty ? name : email.split('@').first;

  String get avatarUrl => avatar ?? '';

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
        lastSeen,
        createdAt,
        googleId,
        appleId,
      ];

  // CopyWith method pour l'immutabilité
  User copyWith({
    String? id,
    String? name,
    String? email,
    String? phone,
    String? avatar,
    String? firebaseUid,
    List<String>? roles,
    Map<String, double>? walletBalances,
    bool? emailVerified,
    DateTime? lastSeen,
    DateTime? createdAt,
    String? googleId,
    String? appleId,
  }) {
    return User(
      id: id ?? this.id,
      name: name ?? this.name,
      email: email ?? this.email,
      phone: phone ?? this.phone,
      avatar: avatar ?? this.avatar,
      firebaseUid: firebaseUid ?? this.firebaseUid,
      roles: roles ?? this.roles,
      walletBalances: walletBalances ?? this.walletBalances,
      emailVerified: emailVerified ?? this.emailVerified,
      lastSeen: lastSeen ?? this.lastSeen,
      createdAt: createdAt ?? this.createdAt,
      googleId: googleId ?? this.googleId,
      appleId: appleId ?? this.appleId,
    );
  }
}
