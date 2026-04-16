import 'package:get/get.dart';
import 'package:geolocator/geolocator.dart';
import '../../core/network/api_client.dart';
import '../../core/utils/constants.dart';
import '../../services/location_service.dart';

class AttendanceController extends GetxController {
  final ApiClient apiClient = ApiClient();
  final LocationService locationService = LocationService();

  var isLoading = false.obs;
  var isLocationReady = false.obs;
  var distanceInMeters = 0.0.obs;
  var isEligibleToAttend = false.obs;

  // Koordinat Sekolah / Target (Bisa diambil dari API)
  var targetLat = (-6.2088).obs; // Placeholder Latitude Sekolah
  var targetLng = (106.8456).obs; // Placeholder Longitude Sekolah

  Position? currentPosition;

  @override
  void onInit() {
    super.onInit();
    fetchSchoolTargetLocation();
  }

  void fetchSchoolTargetLocation() async {
    // Di sini Anda bisa memanggil API untuk mengambil koordinat sekolah (Tenant)
    // Contoh call: var response = await apiClient.client.get('/school/location');
    // targetLat.value = response.data['latitude'];
    // targetLng.value = response.data['longitude'];
    
    // Asumsi data didapat
    refreshCurrentLocation();
  }

  Future<void> refreshCurrentLocation() async {
    isLoading.value = true;
    try {
      currentPosition = await locationService.getCurrentPosition();
      
      distanceInMeters.value = locationService.calculateDistanceInMeters(
        startLat: currentPosition!.latitude,
        startLng: currentPosition!.longitude,
        endLat: targetLat.value,
        endLng: targetLng.value,
      );

      isEligibleToAttend.value = locationService.isWithinRadius(
        distanceInMeters.value, 
        AppConstants.maxAttendanceRadius
      );

      isLocationReady.value = true;
    } catch (e) {
      Get.snackbar('Error Lokasi', e.toString());
      isLocationReady.value = false;
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> submitAttendance() async {
    if (!isEligibleToAttend.value) {
      Get.snackbar('Gagal', 'Anda berada di luar jangkauan area absen.');
      return;
    }

    if (currentPosition == null) return;

    isLoading.value = true;
    try {
      var response = await apiClient.client.post('/attendance', data: {
        'latitude': currentPosition!.latitude,
        'longitude': currentPosition!.longitude,
        'timestamp': DateTime.now().toIso8601String(),
      });

      if (response.statusCode == 200 || response.statusCode == 201) {
        Get.snackbar('Sukses', 'Absensi berhasil dikirim!');
      }
    } catch (e) {
      Get.snackbar('Error Api', e.toString());
    } finally {
      isLoading.value = false;
    }
  }
}
