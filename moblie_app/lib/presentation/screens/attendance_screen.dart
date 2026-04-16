import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:get/get.dart';
import 'package:latlong2/latlong.dart';
import '../controllers/attendance_controller.dart';
import '../controllers/auth_controller.dart';
import 'home_screen.dart';

class AttendanceScreen extends StatelessWidget {
  AttendanceScreen({super.key});

  final AttendanceController ctrl = Get.put(AttendanceController());
  final AuthController auth = Get.find<AuthController>();
  final MapController mapController = MapController();

  void _setupCallback(BuildContext context) {
    ctrl.onAttendanceSuccess ??= () {
      final homeState = context.findAncestorStateOfType<HomeScreenState>();
      homeState?.goToHistory();
    };
  }

  @override
  Widget build(BuildContext context) {
    _setupCallback(context);
    return Scaffold(
      body: Obx(() {
        if (ctrl.isLoading.value && !ctrl.isLocationReady.value) {
          return const Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                CircularProgressIndicator(),
                SizedBox(height: 16),
                Text('Mengambil lokasi...', style: TextStyle(color: Colors.grey)),
              ],
            ),
          );
        }

        return Column(
          children: [
            _buildHeader(context),
            Expanded(child: _buildMap()),
            _buildBottomPanel(context),
          ],
        );
      }),
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
          colors: [Colors.blue.shade700, Colors.blue.shade500],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.blue.withOpacity(0.3),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              CircleAvatar(
                backgroundColor: Colors.white.withOpacity(0.2),
                radius: 22,
                child: Icon(
                  auth.userType.value == 'student' ? Icons.school : Icons.person,
                  color: Colors.white,
                  size: 24,
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Selamat Datang 👋',
                      style: TextStyle(
                        color: Colors.white.withOpacity(0.8),
                        fontSize: 12,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      auth.userName.value,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(0.15),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Text(
              '${auth.userType.value == 'student' ? 'Siswa' : 'Guru'} — ${auth.userSchool.value}',
              style: const TextStyle(color: Colors.white, fontSize: 12),
            ),
          ),
          // Schedule info
          if (ctrl.hasSchedule.value) ...[
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.1),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.white.withOpacity(0.2)),
              ),
              child: Row(
                children: [
                  const Icon(Icons.schedule, color: Colors.white, size: 16),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Jadwal ${ctrl.scheduleDay.value}',
                          style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.bold),
                        ),
                        const SizedBox(height: 2),
                        Row(
                          children: [
                            _scheduleTag('Masuk', ctrl.getCheckInScheduleText(), ctrl.isCheckInOpen.value, Colors.green),
                            const SizedBox(width: 8),
                            _scheduleTag('Pulang', ctrl.getCheckOutScheduleText(), ctrl.isCheckOutOpen.value, Colors.orange),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ],
          // Today's status
          if (ctrl.hasCheckedIn.value) ...[
            const SizedBox(height: 8),
            Row(
              children: [
                _statusChip(
                  '✅ Masuk ${ctrl.checkInTime.value}',
                  ctrl.checkInStatus.value == 'on_time' ? Colors.green : Colors.orange,
                ),
                if (ctrl.hasCheckedOut.value) ...[
                  const SizedBox(width: 8),
                  _statusChip('✅ Pulang', Colors.blue),
                ],
              ],
            ),
          ],
        ],
      ),
    );
  }

  Widget _scheduleTag(String label, String time, bool isOpen, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
        decoration: BoxDecoration(
          color: isOpen ? color.withOpacity(0.3) : Colors.white.withOpacity(0.1),
          borderRadius: BorderRadius.circular(6),
        ),
        child: Text(
          '$label: ${time.isNotEmpty ? time : '-'}',
          textAlign: TextAlign.center,
          style: TextStyle(
            color: isOpen ? Colors.white : Colors.white.withOpacity(0.6),
            fontSize: 10,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),
    );
  }

  Widget _statusChip(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: color.withOpacity(0.2),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color.withOpacity(0.4)),
      ),
      child: Text(
        label,
        style: const TextStyle(color: Colors.white, fontSize: 11, fontWeight: FontWeight.w600),
      ),
    );
  }

  Widget _buildMap() {
    if (!ctrl.isLocationReady.value || ctrl.currentPosition == null) {
      return const Center(child: Text('Menunggu lokasi GPS...'));
    }

    final userLatLng = LatLng(
      ctrl.currentPosition!.latitude,
      ctrl.currentPosition!.longitude,
    );
    final schoolLatLng = LatLng(ctrl.targetLat.value, ctrl.targetLng.value);

    // Build circles for all locations
    List<CircleMarker> circles = [];
    List<Marker> markers = [];

    for (var loc in ctrl.schoolLocations) {
      final locLatLng = LatLng(loc['latitude'], loc['longitude']);
      final radius = (loc['radius'] as num).toDouble();

      circles.add(CircleMarker(
        point: locLatLng,
        radius: radius,
        useRadiusInMeter: true,
        color: Colors.blue.withOpacity(0.12),
        borderColor: Colors.blue.withOpacity(0.5),
        borderStrokeWidth: 2,
      ));

      markers.add(Marker(
        point: locLatLng,
        width: 50,
        height: 50,
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: Colors.red,
                shape: BoxShape.circle,
                boxShadow: [
                  BoxShadow(
                    color: Colors.red.withOpacity(0.3),
                    blurRadius: 8,
                    spreadRadius: 2,
                  ),
                ],
              ),
              child: const Icon(Icons.location_city, color: Colors.white, size: 18),
            ),
          ],
        ),
      ));
    }

    // Fallback if no school locations loaded
    if (circles.isEmpty && schoolLatLng.latitude != 0) {
      circles.add(CircleMarker(
        point: schoolLatLng,
        radius: ctrl.targetRadius.value,
        useRadiusInMeter: true,
        color: Colors.blue.withOpacity(0.15),
        borderColor: Colors.blue.withOpacity(0.6),
        borderStrokeWidth: 2,
      ));
      markers.add(Marker(
        point: schoolLatLng,
        width: 50,
        height: 50,
        child: Container(
          padding: const EdgeInsets.all(6),
          decoration: BoxDecoration(
            color: Colors.red,
            shape: BoxShape.circle,
            boxShadow: [BoxShadow(color: Colors.red.withOpacity(0.3), blurRadius: 8, spreadRadius: 2)],
          ),
          child: const Icon(Icons.location_city, color: Colors.white, size: 18),
        ),
      ));
    }

    // User marker
    markers.add(Marker(
      point: userLatLng,
      width: 50,
      height: 50,
      child: Container(
        padding: const EdgeInsets.all(6),
        decoration: BoxDecoration(
          color: Colors.blue,
          shape: BoxShape.circle,
          border: Border.all(color: Colors.white, width: 3),
          boxShadow: [
            BoxShadow(
              color: Colors.blue.withOpacity(0.4),
              blurRadius: 10,
              spreadRadius: 3,
            ),
          ],
        ),
        child: const Icon(Icons.person, color: Colors.white, size: 18),
      ),
    ));

    return FlutterMap(
      mapController: mapController,
      options: MapOptions(
        initialCenter: userLatLng,
        initialZoom: 17.0,
      ),
      children: [
        TileLayer(
          urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
          userAgentPackageName: 'com.billnesia.absensi',
        ),
        CircleLayer(circles: circles),
        MarkerLayer(markers: markers),
      ],
    );
  }

  Widget _buildBottomPanel(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: const BorderRadius.only(
          topLeft: Radius.circular(24),
          topRight: Radius.circular(24),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.08),
            blurRadius: 16,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Distance & location info
          Row(
            children: [
              Icon(
                ctrl.isEligibleToAttend.value ? Icons.check_circle : Icons.warning,
                color: ctrl.isEligibleToAttend.value ? Colors.green : Colors.orange,
                size: 20,
              ),
              const SizedBox(width: 8),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      ctrl.nearestLocationName.value.isNotEmpty
                          ? 'Titik: ${ctrl.nearestLocationName.value}'
                          : 'Menunggu data lokasi...',
                      style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      ctrl.isEligibleToAttend.value
                          ? 'Dalam radius — ${ctrl.distanceInMeters.value.toStringAsFixed(1)}m'
                          : 'Di luar radius — ${ctrl.distanceInMeters.value.toStringAsFixed(1)}m (maks ${ctrl.targetRadius.value.toInt()}m)',
                      style: TextStyle(
                        fontSize: 12,
                        color: ctrl.isEligibleToAttend.value ? Colors.green.shade700 : Colors.orange.shade700,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
              IconButton(
                onPressed: ctrl.isLoading.value ? null : () => ctrl.refreshCurrentLocation(),
                icon: ctrl.isLoading.value
                    ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2))
                    : const Icon(Icons.refresh, color: Colors.grey),
                tooltip: 'Refresh Lokasi',
              ),
            ],
          ),
          // Schedule warning if time window closed
          if (ctrl.hasSchedule.value && (!ctrl.isCheckInOpen.value && !ctrl.hasCheckedIn.value)) ...[
            const SizedBox(height: 8),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.red.shade200),
              ),
              child: Row(
                children: [
                  Icon(Icons.timer_off, size: 16, color: Colors.red.shade400),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Waktu absen masuk telah habis (${ctrl.getCheckInScheduleText()})',
                      style: TextStyle(fontSize: 12, color: Colors.red.shade700, fontWeight: FontWeight.w500),
                    ),
                  ),
                ],
              ),
            ),
          ],
          if (ctrl.hasSchedule.value && ctrl.hasCheckedIn.value && !ctrl.hasCheckedOut.value && !ctrl.isCheckOutOpen.value) ...[
            const SizedBox(height: 8),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.orange.shade50,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.orange.shade200),
              ),
              child: Row(
                children: [
                  Icon(Icons.timer_off, size: 16, color: Colors.orange.shade400),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      'Waktu absen pulang belum/sudah berakhir (${ctrl.getCheckOutScheduleText()})',
                      style: TextStyle(fontSize: 12, color: Colors.orange.shade700, fontWeight: FontWeight.w500),
                    ),
                  ),
                ],
              ),
            ),
          ],
          const SizedBox(height: 12),
          // Action buttons — Absen Masuk & Absen Pulang
          Row(
            children: [
              Expanded(
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _getCheckInColor(),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    elevation: 2,
                  ),
                  onPressed: ctrl.canCheckIn() ? () => ctrl.submitCheckIn() : null,
                  icon: Icon(ctrl.hasCheckedIn.value ? Icons.check : Icons.login, size: 20),
                  label: Text(
                    ctrl.hasCheckedIn.value ? 'Sudah Masuk' : 'Absen Masuk',
                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: ElevatedButton.icon(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _getCheckOutColor(),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                    elevation: 2,
                  ),
                  onPressed: ctrl.canCheckOut() ? () => ctrl.submitCheckOut() : null,
                  icon: Icon(ctrl.hasCheckedOut.value ? Icons.check : Icons.logout, size: 20),
                  label: Text(
                    ctrl.hasCheckedOut.value ? 'Sudah Pulang' : 'Absen Pulang',
                    style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Color _getCheckInColor() {
    if (ctrl.hasCheckedIn.value) return Colors.grey;
    if (!ctrl.canCheckIn()) return Colors.grey.shade400;
    return Colors.green;
  }

  Color _getCheckOutColor() {
    if (ctrl.hasCheckedOut.value) return Colors.grey;
    if (!ctrl.canCheckOut()) return Colors.grey.shade400;
    return Colors.red.shade400;
  }
}
