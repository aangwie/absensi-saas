import 'dart:io';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class DeviceInfoService {
  final DeviceInfoPlugin deviceInfo = DeviceInfoPlugin();
  final FlutterSecureStorage secureStorage = const FlutterSecureStorage();

  /// Mendapatkan Device Info Lengkap
  Future<Map<String, String>> getDeviceInfo() async {
    String deviceId = 'unknown_device';
    String deviceName = 'Unknown';
    String deviceVersion = 'Unknown';

    try {
      if (Platform.isAndroid) {
        AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
        deviceId = androidInfo.id;
        deviceName = '${androidInfo.brand} ${androidInfo.model}';
        deviceVersion = 'Android ${androidInfo.version.release}';
      } else if (Platform.isIOS) {
        IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
        deviceId = iosInfo.identifierForVendor ?? 'unknown_ios_id';
        deviceName = iosInfo.name;
        deviceVersion = '${iosInfo.systemName} ${iosInfo.systemVersion}';
      }
    } catch (e) {
      // Fallback if failed
    }
    
    return {
      'device_id': deviceId,
      'device_name': deviceName.trim(),
      'device_version': deviceVersion,
    };
  }
}
