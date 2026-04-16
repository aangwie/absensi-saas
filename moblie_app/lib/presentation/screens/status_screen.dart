import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:device_info_plus/device_info_plus.dart';
import '../../core/network/api_client.dart';
import '../controllers/auth_controller.dart';

class StatusScreen extends StatefulWidget {
  const StatusScreen({super.key});

  @override
  State<StatusScreen> createState() => _StatusScreenState();
}

class _StatusScreenState extends State<StatusScreen> {
  final AuthController auth = Get.find<AuthController>();
  final ApiClient apiClient = ApiClient();

  String deviceName = '-';
  String androidVersion = '-';
  String deviceId = '-';
  String serverDeviceId = '';
  bool isDeviceBound = false;
  bool isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadDeviceInfo();
  }

  Future<void> _loadDeviceInfo() async {
    setState(() => isLoading = true);
    try {
      // Get local device info
      final deviceInfo = DeviceInfoPlugin();
      final androidInfo = await deviceInfo.androidInfo;
      deviceName = '${androidInfo.brand} ${androidInfo.model}';
      androidVersion = 'Android ${androidInfo.version.release} (SDK ${androidInfo.version.sdkInt})';
      deviceId = androidInfo.id;

      // Check server binding
      try {
        var response = await apiClient.client.get('/auth/profile');
        if (response.data['success'] == true) {
          final data = response.data['data'];
          serverDeviceId = data['device_id']?.toString() ?? '';
          isDeviceBound = serverDeviceId.isNotEmpty;
        }
      } catch (_) {}
    } catch (e) {
      deviceName = 'Tidak dapat membaca info perangkat';
    } finally {
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          _buildHeader(context),
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : _buildStatusContent(),
          ),
        ],
      ),
    );
  }

  Widget _buildHeader(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(
        top: MediaQuery.of(context).padding.top + 12,
        left: 20,
        right: 20,
        bottom: 16,
      ),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [Colors.teal.shade700, Colors.teal.shade500],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      child: Row(
        children: [
          const Icon(Icons.phone_android, color: Colors.white, size: 28),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Status Perangkat',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Text(
                  auth.userName.value,
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.8),
                    fontSize: 13,
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            onPressed: _loadDeviceInfo,
            icon: const Icon(Icons.refresh, color: Colors.white),
          ),
        ],
      ),
    );
  }

  Widget _buildStatusContent() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Device binding status card
          _buildBindingCard(),
          const SizedBox(height: 20),
          // Device info section
          const Text(
            'Informasi Perangkat',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87),
          ),
          const SizedBox(height: 12),
          _buildInfoCard(Icons.phone_android, 'Nama Perangkat', deviceName),
          _buildInfoCard(Icons.android, 'Versi Android', androidVersion),
          _buildInfoCard(Icons.fingerprint, 'ID Perangkat', deviceId),
          const SizedBox(height: 20),
          // Account info section
          const Text(
            'Informasi Akun',
            style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.black87),
          ),
          const SizedBox(height: 12),
          _buildInfoCard(Icons.person, 'Nama', auth.userName.value),
          _buildInfoCard(Icons.school, 'Sekolah', auth.userSchool.value),
          _buildInfoCard(Icons.badge, 'Tipe', auth.userType.value == 'student' ? 'Siswa' : 'Guru'),
        ],
      ),
    );
  }

  Widget _buildBindingCard() {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: isDeviceBound
              ? [Colors.green.shade400, Colors.teal.shade500]
              : [Colors.orange.shade400, Colors.red.shade400],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: (isDeviceBound ? Colors.teal : Colors.orange).withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 56,
            height: 56,
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.2),
              borderRadius: BorderRadius.circular(16),
            ),
            child: Icon(
              isDeviceBound ? Icons.link : Icons.link_off,
              color: Colors.white,
              size: 28,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  isDeviceBound ? 'Terikat ke Server' : 'Tidak Terikat ke Server',
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  isDeviceBound
                      ? 'Perangkat ini sudah terdaftar dan terikat dengan akun Anda di server.'
                      : 'Perangkat ini belum terikat. Login kembali untuk mengikat perangkat.',
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.85),
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoCard(IconData icon, String label, String value) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Row(
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              color: Colors.teal.shade50,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(icon, color: Colors.teal.shade600, size: 22),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(fontSize: 11, color: Colors.grey.shade500, fontWeight: FontWeight.w500),
                ),
                const SizedBox(height: 2),
                Text(
                  value,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: Colors.black87),
                  overflow: TextOverflow.ellipsis,
                  maxLines: 2,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
