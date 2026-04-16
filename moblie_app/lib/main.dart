import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'presentation/controllers/auth_controller.dart';
import 'presentation/screens/security_check_screen.dart';

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
      title: 'Absensi-Jenius',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.blue),
        useMaterial3: true,
      ),
      // SecurityCheckScreen melakukan pengecekan lokasi, GPS, mock location
      // sebelum navigasi ke Login atau Home
      home: const SecurityCheckScreen(),
    );
  }
}
