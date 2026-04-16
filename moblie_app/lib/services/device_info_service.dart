import 'dart:io';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';

class DeviceInfoService {
  final DeviceInfoPlugin deviceInfo = DeviceInfoPlugin();
  final FlutterSecureStorage secureStorage = const FlutterSecureStorage();

  /// Mendapatkan Unique Device ID
  Future<String> getDeviceId() async {
    String deviceId = '';
    try {
      if (Platform.isAndroid) {
        AndroidDeviceInfo androidInfo = await deviceInfo.androidInfo;
        deviceId = androidInfo.id; // Unique ID Android
      } else if (Platform.isIOS) {
        IosDeviceInfo iosInfo = await deviceInfo.iosInfo;
        deviceId = iosInfo.identifierForVendor ?? 'unknown_ios_id';
      }
    } catch (e) {
      deviceId = 'unknown_device';
    }
    return deviceId;
  }
}
