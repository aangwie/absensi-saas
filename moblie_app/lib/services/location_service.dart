import 'package:geolocator/geolocator.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:latlong2/latlong.dart';

class LocationService {
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
