import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'presentation/controllers/auth_controller.dart';
import 'presentation/screens/login_screen.dart';
import 'presentation/screens/home_screen.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  // Register AuthController secara global
  Get.put(AuthController());
  
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: 'Aplikasi Absensi Multi-SaaS',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue),
        useMaterial3: true,
      ),
      home: Obx(() {
        final authController = Get.find<AuthController>();
        if (authController.isLoggedIn.value) {
          return const HomeScreen();
        } else {
          return LoginScreen();
        }
      }),
    );
  }
}
