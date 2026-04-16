import 'package:flutter/material.dart';
import 'package:get/get.dart';
import '../controllers/attendance_controller.dart';
import '../controllers/auth_controller.dart';
import 'login_screen.dart';

class AttendanceScreen extends StatelessWidget {
  AttendanceScreen({super.key});

  final AttendanceController attendanceController = Get.put(AttendanceController());
  final AuthController authController = Get.find<AuthController>();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Kehadiran Siswa'),
        actions: [
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () {
              authController.logout();
              Get.offAll(() => LoginScreen());
            },
          )
        ],
      ),
      body: Obx(() {
        if (attendanceController.isLoading.value && !attendanceController.isLocationReady.value) {
          return const Center(child: CircularProgressIndicator());
        }

        return Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Icon(Icons.location_on, size: 80, color: Colors.blue),
              const SizedBox(height: 16),
              Text(
                'Jarak ke Sekolah: ${attendanceController.distanceInMeters.value.toStringAsFixed(2)} Meter',
                textAlign: TextAlign.center,
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              Text(
                attendanceController.isEligibleToAttend.value
                    ? 'Anda berada di dalam area absen.'
                    : 'Anda berada di LUAR area absen (Maks. 80m).',
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: attendanceController.isEligibleToAttend.value ? Colors.green : Colors.red,
                  fontWeight: FontWeight.w600,
                ),
              ),
              const SizedBox(height: 32),
              ElevatedButton(
                style: ElevatedButton.styleFrom(
                  backgroundColor: attendanceController.isEligibleToAttend.value ? Colors.blue : Colors.grey,
                  padding: const EdgeInsets.symmetric(vertical: 16),
                ),
                onPressed: attendanceController.isEligibleToAttend.value
                    ? () => attendanceController.submitAttendance()
                    : null,
                child: const Text('Kirim Absen', style: TextStyle(fontSize: 18, color: Colors.white)),
              ),
              const SizedBox(height: 16),
              TextButton.icon(
                icon: const Icon(Icons.refresh),
                label: const Text('Refresh Lokasi'),
                onPressed: () => attendanceController.refreshCurrentLocation(),
              ),
            ],
          ),
        );
      }),
    );
  }
}
