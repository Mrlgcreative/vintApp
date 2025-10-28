class ApiConstants {
  static const String baseUrl = 'https://your-vintapp-api.com/api/mobile/v1';
  static const String loginEndpoint = '/auth/login';
  static const String registerEndpoint = '/auth/register';
  static const String refreshTokenEndpoint = '/auth/refresh';
  static const String logoutEndpoint = '/auth/logout';

  // Core endpoints
  static const String itemsEndpoint = '/items';
  static const String ordersEndpoint = '/orders';
  static const String walletEndpoint = '/wallet';
  static const String messagesEndpoint = '/messages';
  static const String categoriesEndpoint = '/categories';
  static const String brandsEndpoint = '/brands';

  // Timeouts
  static const Duration connectTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  static const Duration sendTimeout = Duration(seconds: 30);
}
