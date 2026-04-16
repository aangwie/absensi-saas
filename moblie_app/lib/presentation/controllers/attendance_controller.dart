import 'package:get/get.dart';
import 'package:dio/dio.dart';
import 'package:geolocator/geolocator.dart';
import '../../core/network/api_client.dart';
import '../../services/location_service.dart';

class AttendanceController extends GetxController {
  final ApiClient apiClient = ApiClient();
  final LocationService locationService = LocationService();

  /// Callback triggered after successful attendance (check-in or check-out)
  Function? onAttendanceSuccess;

  var isLoading = false.obs;
  var isLocationReady = false.obs;
  var distanceInMeters = 0.0.obs;
  var isEligibleToAttend = false.obs;
  var hasCheckedIn = false.obs;
  var hasCheckedOut = false.obs;
  var checkInTime = ''.obs;
  var checkInStatus = ''.obs;

  // School locations from API
  var schoolLocations = <Map<String, dynamic>>[].obs;
  var nearestLocationName = ''.obs;
  var schoolName = ''.obs;

  // Nearest school target coordinates
  var targetLat = 0.0.obs;
  var targetLng = 0.0.obs;
  var targetRadius = 80.0.obs;

  // Schedule
  var hasSchedule = false.obs;
  var scheduleDay = ''.obs;
  var checkInStart = ''.obs;
  var checkInEnd = ''.obs;
  var checkOutStart = ''.obs;
  var checkOutEnd = ''.obs;
  var isCheckInOpen = false.obs;
  var isCheckOutOpen = false.obs;
  var serverTime = ''.obs;

  Position? currentPosition;

  @override
  void onInit() {
    super.onInit();
    initData();
  }

  Future<void> initData() async {
    await fetchSchoolLocations();
    await fetchTodaySchedule();
    await fetchTodayStatus();
    await refreshCurrentLocation();
  }

  /// Fetch school locations from API
  Future<void> fetchSchoolLocations() async {
    try {
      var response = await apiClient.client.get('/school/locations');
      if (response.data['success'] == true) {
        final data = response.data['data'];
        schoolName.value = data['school_name'] ?? '';

        final List<dynamic> locs = data['locations'] is List ? data['locations'] : [];
        schoolLocations.value = locs.map<Map<String, dynamic>>((loc) {
          return {
            'id': loc['id'],
            'name': loc['name'] ?? 'Lokasi',
            'latitude': (loc['latitude'] as num).toDouble(),
            'longitude': (loc['longitude'] as num).toDouble(),
            'radius': (loc['radius'] as num?)?.toDouble() ?? 80.0,
          };
        }).toList();

        if (schoolLocations.isNotEmpty) {
          targetLat.value = schoolLocations[0]['latitude'];
          targetLng.value = schoolLocations[0]['longitude'];
          targetRadius.value = schoolLocations[0]['radius'];
          nearestLocationName.value = schoolLocations[0]['name'];
        }
      }
    } catch (_) {}
  }

  /// Fetch today's schedule from API
  Future<void> fetchTodaySchedule() async {
    try {
      var response = await apiClient.client.get('/school/schedule');
      if (response.data['success'] == true) {
        final data = response.data['data'];
        scheduleDay.value = data['day'] ?? '';
        hasSchedule.value = data['has_schedule'] ?? false;

        if (hasSchedule.value) {
          final ci = data['check_in'];
          final co = data['check_out'];
          checkInStart.value = ci?['start'] ?? '';
          checkInEnd.value = ci?['end'] ?? '';
          checkOutStart.value = co?['start'] ?? '';
          checkOutEnd.value = co?['end'] ?? '';
          isCheckInOpen.value = ci?['is_open'] ?? false;
          isCheckOutOpen.value = co?['is_open'] ?? false;
          serverTime.value = data['server_time'] ?? '';
        }
      }
    } catch (_) {}
  }

  /// Fetch today's attendance status
  Future<void> fetchTodayStatus() async {
    try {
      var response = await apiClient.client.get('/attendance/today');
      if (response.data['success'] == true) {
        final data = response.data['data'];
        hasCheckedIn.value = data['has_checked_in'] ?? false;
        hasCheckedOut.value = data['has_checked_out'] ?? false;
        if (data['check_in'] != null) {
          checkInTime.value = data['check_in']['time'] ?? '';
          checkInStatus.value = data['check_in']['status'] ?? '';
        }
      }
    } catch (_) {}
  }

  Future<void> refreshCurrentLocation() async {
    isLoading.value = true;
    try {
      currentPosition = await locationService.getCurrentPosition();
      isLocationReady.value = true;

      if (schoolLocations.isNotEmpty) {
        _calculateNearestLocation();
      } else {
        await fetchSchoolLocations();
        if (schoolLocations.isNotEmpty) {
          _calculateNearestLocation();
        }
      }

      // Refresh schedule too
      await fetchTodaySchedule();
    } catch (e) {
      Get.snackbar('Error Lokasi', e.toString());
      isLocationReady.value = false;
    } finally {
      isLoading.value = false;
    }
  }

  void _calculateNearestLocation() {
    if (currentPosition == null || schoolLocations.isEmpty) return;

    double minDistance = double.infinity;
    Map<String, dynamic>? nearest;

    for (var loc in schoolLocations) {
      double dist = locationService.calculateDistanceInMeters(
        startLat: currentPosition!.latitude,
        startLng: currentPosition!.longitude,
        endLat: loc['latitude'],
        endLng: loc['longitude'],
      );
      if (dist < minDistance) {
        minDistance = dist;
        nearest = loc;
      }
    }

    if (nearest != null) {
      distanceInMeters.value = minDistance;
      targetLat.value = nearest['latitude'];
      targetLng.value = nearest['longitude'];
      targetRadius.value = nearest['radius'];
      nearestLocationName.value = nearest['name'];
      isEligibleToAttend.value = locationService.isWithinRadius(
        minDistance,
        targetRadius.value,
      );
    }
  }

  /// Check if check-in is allowed (location + schedule)
  bool canCheckIn() {
    if (!isEligibleToAttend.value) return false;
    if (hasCheckedIn.value) return false;
    if (isLoading.value) return false;
    // If schedule exists, check time window
    if (hasSchedule.value && !isCheckInOpen.value) return false;
    return true;
  }

  /// Check if check-out is allowed
  bool canCheckOut() {
    if (!isEligibleToAttend.value) return false;
    if (!hasCheckedIn.value) return false;
    if (hasCheckedOut.value) return false;
    if (isLoading.value) return false;
    if (hasSchedule.value && !isCheckOutOpen.value) return false;
    return true;
  }

  /// Get schedule info text for check-in
  String getCheckInScheduleText() {
    if (!hasSchedule.value) return '';
    if (checkInStart.value.isEmpty || checkInEnd.value.isEmpty) return '';
    return '${checkInStart.value} - ${checkInEnd.value}';
  }

  /// Get schedule info text for check-out
  String getCheckOutScheduleText() {
    if (!hasSchedule.value) return '';
    if (checkOutStart.value.isEmpty || checkOutEnd.value.isEmpty) return '';
    return '${checkOutStart.value} - ${checkOutEnd.value}';
  }

  Future<void> submitCheckIn() async {
    if (!canCheckIn()) {
      if (hasSchedule.value && !isCheckInOpen.value) {
        Get.snackbar('Waktu Habis', 'Waktu absen masuk: ${getCheckInScheduleText()}');
      } else {
        Get.snackbar('Gagal', 'Anda berada di luar jangkauan area absen.');
      }
      return;
    }
    if (currentPosition == null) return;

    isLoading.value = true;
    try {
      bool isMock = currentPosition!.isMocked;

      var response = await apiClient.client.post('/attendance/check-in', data: {
        'latitude': currentPosition!.latitude,
        'longitude': currentPosition!.longitude,
        'accuracy': currentPosition!.accuracy,
        'is_mock_suspected': isMock,
        'mock_reasons': isMock ? 'Mock location detected by device' : null,
      });

      if (response.data['success'] == true) {
        String message = response.data['message'] ?? 'Absen masuk berhasil!';
        hasCheckedIn.value = true;
        Get.snackbar('Sukses ✅', message);
        await fetchTodayStatus();
        // Navigate to history
        onAttendanceSuccess?.call();
      }
    } catch (e) {
      _handleError(e, 'Absen Masuk Gagal');
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> submitCheckOut() async {
    if (!canCheckOut()) {
      if (hasSchedule.value && !isCheckOutOpen.value) {
        Get.snackbar('Waktu Habis', 'Waktu absen pulang: ${getCheckOutScheduleText()}');
      } else {
        Get.snackbar('Gagal', 'Anda berada di luar jangkauan area absen.');
      }
      return;
    }
    if (currentPosition == null) return;

    isLoading.value = true;
    try {
      bool isMock = currentPosition!.isMocked;

      var response = await apiClient.client.post('/attendance/check-out', data: {
        'latitude': currentPosition!.latitude,
        'longitude': currentPosition!.longitude,
        'accuracy': currentPosition!.accuracy,
        'is_mock_suspected': isMock,
        'mock_reasons': isMock ? 'Mock location detected by device' : null,
      });

      if (response.data['success'] == true) {
        String message = response.data['message'] ?? 'Absen pulang berhasil!';
        hasCheckedOut.value = true;
        Get.snackbar('Sukses ✅', message);
        await fetchTodayStatus();
        // Navigate to history
        onAttendanceSuccess?.call();
      }
    } catch (e) {
      _handleError(e, 'Absen Pulang Gagal');
    } finally {
      isLoading.value = false;
    }
  }

  void _handleError(dynamic e, String title) {
    String message = 'Terjadi kesalahan.';
    try {
      if (e is DioException && e.response != null) {
        final data = e.response?.data;
        if (data is Map && data.containsKey('message') && data['message'] != null) {
          message = data['message'].toString();
        }
      } else {
        message = e.toString();
      }
    } catch (_) {
      message = 'Terjadi kesalahan yang tidak diketahui.';
    }
    Get.snackbar(title, message);
  }
}
