import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../controllers/auth_controller.dart';
import 'home_screen.dart';

class LoginScreen extends StatelessWidget {
  LoginScreen({super.key});

  final AuthController authController = Get.put(AuthController());
  final TextEditingController idController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final RxString loginType = 'student'.obs; // 'student' or 'teacher'

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Login Absensi Multi-SaaS')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Toggle login type
            Obx(() => Row(
              children: [
                Expanded(
                  child: GestureDetector(
                    onTap: () {
                      loginType.value = 'student';
                      idController.clear();
                    },
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      decoration: BoxDecoration(
                        color: loginType.value == 'student'
                            ? Theme.of(context).primaryColor
                            : Colors.grey[200],
                        borderRadius: const BorderRadius.only(
                          topLeft: Radius.circular(8),
                          bottomLeft: Radius.circular(8),
                        ),
                      ),
                      child: Text(
                        'Siswa',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: loginType.value == 'student'
                              ? Colors.white
                              : Colors.black87,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                ),
                Expanded(
                  child: GestureDetector(
                    onTap: () {
                      loginType.value = 'teacher';
                      idController.clear();
                    },
                    child: Container(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      decoration: BoxDecoration(
                        color: loginType.value == 'teacher'
                            ? Theme.of(context).primaryColor
                            : Colors.grey[200],
                        borderRadius: const BorderRadius.only(
                          topRight: Radius.circular(8),
                          bottomRight: Radius.circular(8),
                        ),
                      ),
                      child: Text(
                        'Guru',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: loginType.value == 'teacher'
                              ? Colors.white
                              : Colors.black87,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            )),
            const SizedBox(height: 24),
            // ID field (NISN or NIP)
            Obx(() => TextField(
              controller: idController,
              decoration: InputDecoration(
                labelText: loginType.value == 'student' ? 'NISN' : 'NIP',
                hintText: loginType.value == 'student'
                    ? 'Masukkan NISN Anda'
                    : 'Masukkan NIP Anda',
              ),
              keyboardType: TextInputType.number,
            )),
            const SizedBox(height: 16),
            TextField(
              controller: passwordController,
              decoration: const InputDecoration(labelText: 'Password'),
              obscureText: true,
            ),
            const SizedBox(height: 24),
            Obx(() => authController.isLoading.value
                ? const CircularProgressIndicator()
                : SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () async {
                        bool success;
                        if (loginType.value == 'student') {
                          success = await authController.loginAsStudent(
                            idController.text,
                            passwordController.text,
                          );
                        } else {
                          success = await authController.loginAsTeacher(
                            idController.text,
                            passwordController.text,
                          );
                        }
                        if (success) {
                          Get.offAll(() => const HomeScreen());
                        }
                      },
                      child: const Text('Login'),
                    ),
                  )),
          ],
        ),
      ),
    );
  }
}
