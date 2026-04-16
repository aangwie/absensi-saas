import 'package:geolocator/geolocator.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:latlong2/latlong.dart';

class LocationService {
  /// Hasil pengecekan keamanan lokasi saat startup
  /// Return Map berisi status permission, GPS, mock, dan error message
  Future<Map<String, dynamic>> checkLocationSecurity() async {
    Map<String, dynamic> result = {
      'permissionGranted': false,
      'permissionPermanentlyDenied': false,
      'gpsEnabled': false,
      'isMocked': false,
      'passed': false,
      'error': null,
    };

    // 1. Cek dan minta Permission Lokasi
    var status = await Permission.location.status;
    if (status.isDenied) {
      status = await Permission.location.request();
    }

    if (status.isPermanentlyDenied) {
      result['permissionPermanentlyDenied'] = true;
      result['error'] = 'Izin lokasi ditolak permanen. Silakan buka Pengaturan Aplikasi untuk mengizinkan akses lokasi.';
      return result;
    }

    if (status.isDenied) {
      result['error'] = 'Izin lokasi ditolak. Aplikasi membutuhkan akses lokasi untuk berfungsi.';
      return result;
    }

    result['permissionGranted'] = true;

    // 2. Cek apakah GPS / Location Service aktif
    bool isLocationServiceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!isLocationServiceEnabled) {
      result['error'] = 'GPS tidak aktif. Mohon nyalakan GPS/Layanan Lokasi di perangkat Anda.';
      return result;
    }

    result['gpsEnabled'] = true;

    // 3. Ambil posisi dan cek Mock Location
    try {
      Position position = await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.high,
        ),
      );

      if (position.isMocked) {
        result['isMocked'] = true;
        result['error'] = 'Fake GPS / Mock Location terdeteksi! Matikan aplikasi Fake GPS Anda dan restart aplikasi ini.';
        return result;
      }
    } catch (e) {
      result['error'] = 'Gagal mendapatkan lokasi: ${e.toString()}';
      return result;
    }

    // Semua pengecekan berhasil
    result['passed'] = true;
    return result;
  }

  /// Mendapatkan posisi saat ini dengan akurasi tinggi dan mengecek Fake GPS
  Future<Position> getCurrentPosition() async {
    // 1. Cek Permission Lokasi
    var status = await Permission.location.status;
    if (status.isDenied) {
      status = await Permission.location.request();
      if (status.isDenied) {
        throw Exception('Izin lokasi ditolak. Aplikasi butuh akses lokasi untuk absen.');
      }
    }

    if (status.isPermanentlyDenied) {
      throw Exception('Izin lokasi ditolak permanen. Silakan buka pengaturan aplikasi (Settings).');
    }

    // 2. Cek GPS aktif atau tidak
    bool isLocationServiceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!isLocationServiceEnabled) {
      throw Exception('GPS tidak aktif. Mohon nyalakan GPS di HP Anda.');
    }

    // 3. Ambil Koordinat
    Position position = await Geolocator.getCurrentPosition(
      locationSettings: const LocationSettings(
        accuracy: LocationAccuracy.high,
      ),
    );

    // 4. Deteksi Anti-Fake GPS (Mock location)
    if (position.isMocked) {
      throw Exception('Fake GPS Terdeteksi! Matikan aplikasi Mock Location Anda.');
    }

    return position;
  }

  /// Menghitung jarak dari posisi saat ini ke lokasi tujuan (sekolah)
  double calculateDistanceInMeters({
    required double startLat,
    required double startLng,
    required double endLat,
    required double endLng,
  }) {
    const Distance distance = Distance();
    return distance(
      LatLng(startLat, startLng),
      LatLng(endLat, endLng),
    );
  }

  /// Cek apakah jarak saat ini berada dalam radius yang diperbolehkan
  bool isWithinRadius(double currentDistance, double maxRadius) {
    return currentDistance <= maxRadius;
  }
}
