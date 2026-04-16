import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../controllers/auth_controller.dart';
import 'home_screen.dart';

class LoginScreen extends StatelessWidget {
  LoginScreen({super.key});

  final AuthController authController = Get.put(AuthController());
  final TextEditingController idController = TextEditingController();
  final TextEditingController passwordController = TextEditingController();
  final RxString loginType = 'student'.obs;
  final RxBool obscurePassword = true.obs;

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;

    return Scaffold(
      body: SingleChildScrollView(
        child: SizedBox(
          height: size.height,
          child: Stack(
            children: [
              // Colorful wave background
              _buildBackground(size),
              // Login form card
              _buildLoginForm(context, size),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildBackground(Size size) {
    return Stack(
      children: [
        // Base gradient
        Container(
          height: size.height * 0.45,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [
                Color(0xFF1A237E), // Deep indigo
                Color(0xFF1565C0), // Blue
                Color(0xFF0097A7), // Teal
                Color(0xFF00BFA5), // Mint green
              ],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
        ),
        // Orange-coral blob (top right)
        Positioned(
          top: -30,
          right: -40,
          child: Container(
            width: 200,
            height: 200,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFFFF6D00), Color(0xFFFF9100)],
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
              borderRadius: BorderRadius.circular(100),
            ),
          ),
        ),
        // Pink-coral blob (top center)
        Positioned(
          top: 30,
          right: 60,
          child: Container(
            width: 150,
            height: 150,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFFFF5252), Color(0xFFFF8A80)],
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
              ),
              borderRadius: BorderRadius.circular(75),
            ),
          ),
        ),
        // Teal blob (left)
        Positioned(
          top: 80,
          left: -30,
          child: Container(
            width: 120,
            height: 120,
            decoration: BoxDecoration(
              color: const Color(0xFF26C6DA).withOpacity(0.6),
              borderRadius: BorderRadius.circular(60),
            ),
          ),
        ),
        // Wave curve at the bottom of the gradient
        Positioned(
          top: size.height * 0.35,
          left: 0,
          right: 0,
          child: Container(
            height: size.height * 0.12,
            decoration: const BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.only(
                topLeft: Radius.circular(50),
                topRight: Radius.circular(50),
              ),
            ),
          ),
        ),
        // App title on gradient
        Positioned(
          top: size.height * 0.15,
          left: 0,
          right: 0,
          child: Column(
            children: [
              Container(
                width: 70,
                height: 70,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(0.2),
                  borderRadius: BorderRadius.circular(20),
                  border: Border.all(color: Colors.white.withOpacity(0.3), width: 2),
                ),
                child: const Icon(Icons.access_time_filled, color: Colors.white, size: 40),
              ),
              const SizedBox(height: 12),
              const Text(
                'Absensi-Jenius',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 28,
                  fontWeight: FontWeight.bold,
                  letterSpacing: 1.2,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                'Sistem Absensi Digital',
                style: TextStyle(
                  color: Colors.white.withOpacity(0.8),
                  fontSize: 14,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget _buildLoginForm(BuildContext context, Size size) {
    return Positioned(
      top: size.height * 0.38,
      left: 0,
      right: 0,
      bottom: 0,
      child: Container(
        color: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 30),
        child: Column(
          children: [
            const SizedBox(height: 10),
            const Text(
              'Login',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.bold,
                color: Color(0xFF1A237E),
              ),
            ),
            const SizedBox(height: 20),
            // Student/Teacher toggle
            Obx(() => Container(
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(16),
              ),
              padding: const EdgeInsets.all(4),
              child: Row(
                children: [
                  _buildToggleButton('Siswa', 'student', const Color(0xFF1565C0)),
                  _buildToggleButton('Guru', 'teacher', const Color(0xFF00897B)),
                ],
              ),
            )),
            const SizedBox(height: 24),
            // ID field
            Obx(() => _buildTextField(
              controller: idController,
              icon: loginType.value == 'student' ? Icons.badge_outlined : Icons.work_outline,
              label: loginType.value == 'student' ? 'NISN' : 'NIP',
              hint: loginType.value == 'student' ? 'Masukkan NISN Anda' : 'Masukkan NIP Anda',
              isPassword: false,
            )),
            const SizedBox(height: 16),
            // Password field
            Obx(() => _buildTextField(
              controller: passwordController,
              icon: Icons.lock_outline,
              label: 'Password',
              hint: 'Masukkan password',
              isPassword: true,
              obscure: obscurePassword.value,
              onToggleObscure: () => obscurePassword.value = !obscurePassword.value,
            )),
            const SizedBox(height: 28),
            // Login button
            Obx(() => authController.isLoading.value
                ? const CircularProgressIndicator()
                : Container(
                    width: double.infinity,
                    height: 52,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(16),
                      gradient: const LinearGradient(
                        colors: [Color(0xFF1565C0), Color(0xFF0097A7), Color(0xFF00BFA5)],
                        begin: Alignment.centerLeft,
                        end: Alignment.centerRight,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF1565C0).withOpacity(0.3),
                          blurRadius: 12,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: ElevatedButton(
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.transparent,
                        shadowColor: Colors.transparent,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                      ),
                      onPressed: () => _handleLogin(context),
                      child: const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            'Masuk',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 17,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 0.5,
                            ),
                          ),
                          SizedBox(width: 8),
                          Icon(Icons.arrow_forward_rounded, color: Colors.white, size: 22),
                        ],
                      ),
                    ),
                  )),
            const Spacer(),
            // Footer
            Padding(
              padding: const EdgeInsets.only(bottom: 24),
              child: Text(
                '© 2026 Absensi-Jenius by Billnesia',
                style: TextStyle(
                  color: Colors.grey.shade400,
                  fontSize: 12,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Handle login process dengan device binding check
  Future<void> _handleLogin(BuildContext context) async {
    Map<String, dynamic> result;

    if (loginType.value == 'student') {
      result = await authController.loginAsStudent(
        idController.text,
        passwordController.text,
      );
    } else {
      result = await authController.loginAsTeacher(
        idController.text,
        passwordController.text,
      );
    }

    if (result['success'] == true) {
      Get.offAll(() => const HomeScreen());
    } else if (result['isDeviceMismatch'] == true) {
      // Tampilkan dialog khusus device mismatch
      if (context.mounted) {
        _showDeviceMismatchDialog(context, result['message']);
      }
    } else {
      // Tampilkan snackbar error biasa
      Get.snackbar(
        'Login Gagal',
        result['message'] ?? 'Terjadi kesalahan.',
        backgroundColor: Colors.red.shade50,
        colorText: Colors.red.shade800,
        icon: const Icon(Icons.error_outline, color: Colors.red),
        snackPosition: SnackPosition.TOP,
        duration: const Duration(seconds: 4),
      );
    }
  }

  /// Dialog khusus untuk device binding mismatch
  void _showDeviceMismatchDialog(BuildContext context, String message) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
        child: Container(
          padding: const EdgeInsets.all(28),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(24),
            color: Colors.white,
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Icon warning
              Container(
                width: 72,
                height: 72,
                decoration: BoxDecoration(
                  color: const Color(0xFFD32F2F).withOpacity(0.1),
                  shape: BoxShape.circle,
                  border: Border.all(
                    color: const Color(0xFFD32F2F).withOpacity(0.3),
                    width: 2,
                  ),
                ),
                child: const Icon(
                  Icons.phonelink_lock,
                  color: Color(0xFFD32F2F),
                  size: 36,
                ),
              ),
              const SizedBox(height: 20),
              // Title
              const Text(
                'Perangkat Tidak Terdaftar',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFFD32F2F),
                ),
              ),
              const SizedBox(height: 16),
              // Message
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFF3E0),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFFFCC80)),
                ),
                child: Column(
                  children: [
                    const Row(
                      children: [
                        Icon(Icons.info_outline, color: Color(0xFFE65100), size: 18),
                        SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'ID perangkat Anda tidak cocok dengan yang terdaftar di server.',
                            style: TextStyle(
                              fontSize: 13,
                              color: Color(0xFFE65100),
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        const Icon(Icons.support_agent, color: Color(0xFFE65100), size: 18),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            'Hubungi SuperAdmin untuk mereset perangkat.',
                            style: TextStyle(
                              fontSize: 13,
                              color: Colors.orange.shade900,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
              // OK button
              SizedBox(
                width: double.infinity,
                height: 48,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFD32F2F),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(14),
                    ),
                    elevation: 2,
                  ),
                  onPressed: () => Navigator.pop(context),
                  child: const Text(
                    'Kembali ke Login',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildToggleButton(String label, String type, Color activeColor) {
    return Expanded(
      child: GestureDetector(
        onTap: () {
          loginType.value = type;
          idController.clear();
        },
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 250),
          padding: const EdgeInsets.symmetric(vertical: 12),
          decoration: BoxDecoration(
            color: loginType.value == type ? activeColor : Colors.transparent,
            borderRadius: BorderRadius.circular(12),
            boxShadow: loginType.value == type
                ? [BoxShadow(color: activeColor.withOpacity(0.3), blurRadius: 8, offset: const Offset(0, 2))]
                : null,
          ),
          child: Text(
            label,
            textAlign: TextAlign.center,
            style: TextStyle(
              color: loginType.value == type ? Colors.white : Colors.grey.shade600,
              fontWeight: FontWeight.w600,
              fontSize: 15,
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required IconData icon,
    required String label,
    required String hint,
    required bool isPassword,
    bool obscure = false,
    VoidCallback? onToggleObscure,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.grey.shade50,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: TextField(
        controller: controller,
        obscureText: isPassword ? obscure : false,
        keyboardType: isPassword ? TextInputType.text : TextInputType.number,
        decoration: InputDecoration(
          prefixIcon: Icon(icon, color: const Color(0xFF1565C0), size: 22),
          suffixIcon: isPassword
              ? IconButton(
                  icon: Icon(
                    obscure ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                    color: Colors.grey.shade400,
                    size: 22,
                  ),
                  onPressed: onToggleObscure,
                )
              : null,
          labelText: label,
          hintText: hint,
          hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 14),
          labelStyle: const TextStyle(color: Color(0xFF1565C0), fontWeight: FontWeight.w500),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        ),
      ),
    );
  }
}
