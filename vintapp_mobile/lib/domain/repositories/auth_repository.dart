import '../entities/user.dart';

abstract class AuthRepository {
  // Authentication avec email/password
  Future<User> login(String email, String password);
  Future<User> register(String name, String email, String password);

  // Authentication via réseaux sociaux
  Future<User> loginWithGoogle();
  Future<User> loginWithApple();
  Future<User> loginWithFirebase(String idToken);

  // Gestion de session
  Future<void> logout();
  Future<User?> getCurrentUser();
  Future<bool> isLoggedIn();

  // Tokens et stockage sécurisé
  Future<void> saveAuthToken(String token);
  Future<String?> getAuthToken();
  Future<void> removeAuthToken();

  // Vérification email
  Future<void> sendEmailVerification();
  Future<void> verifyEmail(String verificationCode);

  // Réinitialisation mot de passe
  Future<void> sendPasswordResetEmail(String email);
  Future<void> resetPassword(String email, String code, String newPassword);

  // Mise à jour profil
  Future<User> updateProfile(User user);
  Future<User> updateAvatar(String imagePath);

  // Suppression de compte
  Future<void> deleteAccount();
}
