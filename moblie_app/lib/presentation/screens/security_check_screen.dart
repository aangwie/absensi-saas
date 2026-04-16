import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:permission_handler/permission_handler.dart';
import '../../services/location_service.dart';
import '../controllers/auth_controller.dart';
import 'login_screen.dart';
import 'home_screen.dart';

class SecurityCheckScreen extends StatefulWidget {
  const SecurityCheckScreen({super.key});

  @override
  State<SecurityCheckScreen> createState() => _SecurityCheckScreenState();
}

class _SecurityCheckScreenState extends State<SecurityCheckScreen>
    with SingleTickerProviderStateMixin {
  final LocationService _locationService = LocationService();

  // Status states
  String _statusMessage = 'Mempersiapkan aplikasi...';
  String _detailMessage = '';
  bool _isChecking = true;
  bool _isMockBlocked = false;
  bool _isPermissionDenied = false;
  bool _isPermissionPermanentlyDenied = false;
  bool _isGpsDisabled = false;
  bool _hasError = false;

  late AnimationController _pulseController;
  late Animation<double> _pulseAnimation;

  @override
  void initState() {
    super.initState();
    _pulseController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat(reverse: true);
    _pulseAnimation = Tween<double>(begin: 0.8, end: 1.0).animate(
      CurvedAnimation(parent: _pulseController, curve: Curves.easeInOut),
    );

    _runSecurityCheck();
  }

  @override
  void dispose() {
    _pulseController.dispose();
    super.dispose();
  }

  Future<void> _runSecurityCheck() async {
    setState(() {
      _isChecking = true;
      _isMockBlocked = false;
      _isPermissionDenied = false;
      _isPermissionPermanentlyDenied = false;
      _isGpsDisabled = false;
      _hasError = false;
      _statusMessage = 'Memeriksa izin lokasi...';
      _detailMessage = '';
    });

    await Future.delayed(const Duration(milliseconds: 800));

    final result = await _locationService.checkLocationSecurity();

    if (result['passed'] == true) {
      setState(() {
        _statusMessage = 'Semua pengecekan berhasil ✓';
        _detailMessage = 'Mengalihkan ke aplikasi...';
      });
      await Future.delayed(const Duration(milliseconds: 600));
      _navigateToApp();
      return;
    }

    // Handle specific errors
    if (result['permissionPermanentlyDenied'] == true) {
      setState(() {
        _isChecking = false;
        _isPermissionPermanentlyDenied = true;
        _statusMessage = 'Izin Lokasi Ditolak Permanen';
        _detailMessage =
            'Aplikasi membutuhkan akses lokasi untuk menjalankan fitur absensi. Silakan buka Pengaturan dan izinkan akses lokasi.';
      });
      return;
    }

    if (result['permissionGranted'] != true) {
      setState(() {
        _isChecking = false;
        _isPermissionDenied = true;
        _statusMessage = 'Izin Lokasi Diperlukan';
        _detailMessage =
            'Aplikasi membutuhkan akses lokasi untuk menjalankan fitur absensi. Mohon izinkan akses lokasi.';
      });
      return;
    }

    if (result['gpsEnabled'] != true) {
      setState(() {
        _isChecking = false;
        _isGpsDisabled = true;
        _statusMessage = 'GPS Tidak Aktif';
        _detailMessage =
            'Mohon nyalakan GPS / Layanan Lokasi di perangkat Anda untuk melanjutkan.';
      });
      return;
    }

    if (result['isMocked'] == true) {
      setState(() {
        _isChecking = false;
        _isMockBlocked = true;
        _statusMessage = '⚠ Fake GPS Terdeteksi!';
        _detailMessage =
            'Aplikasi mendeteksi penggunaan Mock Location / Fake GPS di perangkat Anda. Matikan aplikasi Fake GPS, lalu tekan tombol di bawah untuk memeriksa ulang.';
      });
      return;
    }

    // Generic error
    setState(() {
      _isChecking = false;
      _hasError = true;
      _statusMessage = 'Terjadi Kesalahan';
      _detailMessage = result['error'] ?? 'Gagal melakukan pengecekan keamanan.';
    });
  }

  void _navigateToApp() {
    final authController = Get.find<AuthController>();
    if (authController.isLoggedIn.value) {
      Get.offAll(() => const HomeScreen());
    } else {
      Get.offAll(() => LoginScreen());
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        width: double.infinity,
        height: double.infinity,
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [
              Color(0xFF0D1B2A),
              Color(0xFF1B2838),
              Color(0xFF1A237E),
            ],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: SafeArea(
          child: Column(
            children: [
              const Spacer(flex: 2),
              // Icon with animation
              _buildIcon(),
              const SizedBox(height: 32),
              // Status message
              _buildStatusMessage(),
              const SizedBox(height: 16),
              // Detail message
              if (_detailMessage.isNotEmpty) _buildDetailMessage(),
              const Spacer(flex: 1),
              // Action buttons
              if (!_isChecking) _buildActionButtons(),
              const Spacer(flex: 2),
              // Footer
              _buildFooter(),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildIcon() {
    IconData icon;
    Color iconColor;
    Color bgColor;

    if (_isChecking) {
      icon = Icons.shield_outlined;
      iconColor = Colors.white;
      bgColor = const Color(0xFF1565C0);
    } else if (_isMockBlocked) {
      icon = Icons.gpp_bad;
      iconColor = Colors.white;
      bgColor = const Color(0xFFD32F2F);
    } else if (_isPermissionDenied || _isPermissionPermanentlyDenied) {
      icon = Icons.location_off;
      iconColor = Colors.white;
      bgColor = const Color(0xFFFF6F00);
    } else if (_isGpsDisabled) {
      icon = Icons.gps_off;
      iconColor = Colors.white;
      bgColor = const Color(0xFFFF6F00);
    } else if (_hasError) {
      icon = Icons.error_outline;
      iconColor = Colors.white;
      bgColor = const Color(0xFFD32F2F);
    } else {
      icon = Icons.check_circle_outline;
      iconColor = Colors.white;
      bgColor = const Color(0xFF2E7D32);
    }

    return AnimatedBuilder(
      animation: _pulseAnimation,
      builder: (context, child) {
        return Transform.scale(
          scale: _isChecking ? _pulseAnimation.value : 1.0,
          child: Container(
            width: 100,
            height: 100,
            decoration: BoxDecoration(
              color: bgColor.withOpacity(0.2),
              shape: BoxShape.circle,
              border: Border.all(color: bgColor.withOpacity(0.5), width: 3),
              boxShadow: [
                BoxShadow(
                  color: bgColor.withOpacity(0.3),
                  blurRadius: 24,
                  spreadRadius: 4,
                ),
              ],
            ),
            child: Icon(icon, color: iconColor, size: 48),
          ),
        );
      },
    );
  }

  Widget _buildStatusMessage() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 32),
      child: Text(
        _statusMessage,
        textAlign: TextAlign.center,
        style: TextStyle(
          color: _isMockBlocked ? const Color(0xFFFF5252) : Colors.white,
          fontSize: 22,
          fontWeight: FontWeight.bold,
          letterSpacing: 0.5,
        ),
      ),
    );
  }

  Widget _buildDetailMessage() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 40),
      child: Text(
        _detailMessage,
        textAlign: TextAlign.center,
        style: TextStyle(
          color: Colors.white.withOpacity(0.7),
          fontSize: 14,
          height: 1.5,
        ),
      ),
    );
  }

  Widget _buildActionButtons() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 40),
      child: Column(
        children: [
          // Mock location blocked — retry button
          if (_isMockBlocked) ...[
            _buildButton(
              label: 'Periksa Ulang',
              icon: Icons.refresh,
              color: const Color(0xFFD32F2F),
              onPressed: _runSecurityCheck,
            ),
          ],

          // Permission denied — retry
          if (_isPermissionDenied) ...[
            _buildButton(
              label: 'Izinkan Lokasi',
              icon: Icons.location_on,
              color: const Color(0xFF1565C0),
              onPressed: _runSecurityCheck,
            ),
          ],

          // Permission permanently denied — open settings
          if (_isPermissionPermanentlyDenied) ...[
            _buildButton(
              label: 'Buka Pengaturan',
              icon: Icons.settings,
              color: const Color(0xFFFF6F00),
              onPressed: () async {
                await openAppSettings();
              },
            ),
            const SizedBox(height: 12),
            _buildButton(
              label: 'Coba Lagi',
              icon: Icons.refresh,
              color: const Color(0xFF1565C0),
              onPressed: _runSecurityCheck,
              outlined: true,
            ),
          ],

          // GPS disabled — retry
          if (_isGpsDisabled) ...[
            _buildButton(
              label: 'Coba Lagi',
              icon: Icons.refresh,
              color: const Color(0xFF1565C0),
              onPressed: _runSecurityCheck,
            ),
          ],

          // Generic error — retry
          if (_hasError) ...[
            _buildButton(
              label: 'Coba Lagi',
              icon: Icons.refresh,
              color: const Color(0xFF1565C0),
              onPressed: _runSecurityCheck,
            ),
          ],
        ],
      ),
    );
  }

  Widget _buildButton({
    required String label,
    required IconData icon,
    required Color color,
    required VoidCallback onPressed,
    bool outlined = false,
  }) {
    if (outlined) {
      return SizedBox(
        width: double.infinity,
        height: 50,
        child: OutlinedButton.icon(
          style: OutlinedButton.styleFrom(
            foregroundColor: Colors.white,
            side: BorderSide(color: color.withOpacity(0.6), width: 1.5),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(14),
            ),
          ),
          onPressed: onPressed,
          icon: Icon(icon, size: 20),
          label: Text(
            label,
            style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
          ),
        ),
      );
    }

    return SizedBox(
      width: double.infinity,
      height: 50,
      child: ElevatedButton.icon(
        style: ElevatedButton.styleFrom(
          backgroundColor: color,
          foregroundColor: Colors.white,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(14),
          ),
          elevation: 4,
          shadowColor: color.withOpacity(0.4),
        ),
        onPressed: onPressed,
        icon: Icon(icon, size: 20),
        label: Text(
          label,
          style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w600),
        ),
      ),
    );
  }

  Widget _buildFooter() {
    return Column(
      children: [
        if (_isChecking)
          const SizedBox(
            width: 24,
            height: 24,
            child: CircularProgressIndicator(
              strokeWidth: 2,
              color: Colors.white38,
            ),
          ),
        const SizedBox(height: 12),
        Text(
          '🔒 Pengecekan Keamanan Perangkat',
          style: TextStyle(
            color: Colors.white.withOpacity(0.4),
            fontSize: 12,
          ),
        ),
      ],
    );
  }
}
