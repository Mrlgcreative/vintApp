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

  // Geographic restrictions
  static const List<String> allowedCities = [
    'Kinshasa',
    'Lubumbashi',
    'Mbuji-Mayi',
    'Kisangani',
    'Kananga',
    'Goma',
    'Bukavu',
    'Tshikapa',
  ];
}
