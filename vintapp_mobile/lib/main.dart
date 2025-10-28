import 'package:flutter/material.dart';
import 'package:hive_flutter/hive_flutter.dart';

import 'core/theme/app_theme.dart';
import 'presentation/pages/splash/splash_page.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize Hive for local storage
  await Hive.initFlutter();

  // TODO: Initialize Firebase
  // await Firebase.initializeApp();

  runApp(const VintApp());
}

class VintApp extends StatelessWidget {
  const VintApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'VintApp Mobile',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      darkTheme: AppTheme.darkTheme,
      themeMode: ThemeMode.system,
      home: const SplashPage(),
    );
  }
}
