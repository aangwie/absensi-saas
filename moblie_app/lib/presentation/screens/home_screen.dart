import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../controllers/auth_controller.dart';
import '../controllers/attendance_controller.dart';
import 'attendance_screen.dart';
import 'history_screen.dart';
import 'status_screen.dart';
import 'login_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => HomeScreenState();
}

class HomeScreenState extends State<HomeScreen> {
  int _currentIndex = 0;
  final AuthController auth = Get.find<AuthController>();
  final GlobalKey<HistoryScreenState> _historyKey = GlobalKey<HistoryScreenState>();

  /// Navigate to history tab and refresh it
  void goToHistory() {
    setState(() => _currentIndex = 1);
    Future.delayed(const Duration(milliseconds: 300), () {
      _historyKey.currentState?.fetchHistory();
    });
  }

  /// Refresh all data in the app
  void refreshAll() async {
    if (Get.isRegistered<AttendanceController>()) {
      final ctrl = Get.find<AttendanceController>();
      await ctrl.initData();
    }
    _historyKey.currentState?.fetchHistory();

    Get.snackbar(
      'Berhasil 🔄',
      'Data berhasil diperbarui dari server.',
      snackPosition: SnackPosition.BOTTOM,
      duration: const Duration(seconds: 2),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: [
          AttendanceScreen(),
          HistoryScreen(key: _historyKey),
          const StatusScreen(),
        ],
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.06),
              blurRadius: 16,
              offset: const Offset(0, -4),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: const BorderRadius.only(
            topLeft: Radius.circular(20),
            topRight: Radius.circular(20),
          ),
          child: BottomNavigationBar(
            currentIndex: _currentIndex,
            onTap: (index) {
              if (index == 4) {
                _showLogoutDialog();
              } else if (index == 3) {
                refreshAll();
              } else {
                setState(() => _currentIndex = index);
                if (index == 1) {
                  Future.delayed(const Duration(milliseconds: 200), () {
                    _historyKey.currentState?.fetchHistory();
                  });
                }
              }
            },
            type: BottomNavigationBarType.fixed,
            selectedItemColor: Colors.blue.shade700,
            unselectedItemColor: Colors.grey,
            selectedFontSize: 11,
            unselectedFontSize: 11,
            items: const [
              BottomNavigationBarItem(
                icon: Icon(Icons.location_on),
                activeIcon: Icon(Icons.location_on, size: 28),
                label: 'Absensi',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.history),
                activeIcon: Icon(Icons.history, size: 28),
                label: 'Riwayat',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.phone_android),
                activeIcon: Icon(Icons.phone_android, size: 28),
                label: 'Status',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.refresh, color: Colors.green),
                label: 'Refresh',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.logout, color: Colors.red),
                label: 'Keluar',
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showLogoutDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text('Konfirmasi Logout'),
        content: const Text('Apakah Anda yakin ingin keluar dari aplikasi?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.red,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
            ),
            onPressed: () {
              Navigator.pop(context);
              auth.logout();
              Get.offAll(() => LoginScreen());
            },
            child: const Text('Keluar'),
          ),
        ],
      ),
    );
  }
}
