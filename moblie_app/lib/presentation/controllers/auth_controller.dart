import 'package:get/get.dart';
import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/network/api_client.dart';
import '../../services/device_info_service.dart';

class AuthController extends GetxController {
  final ApiClient apiClient = ApiClient();
  final DeviceInfoService deviceService = DeviceInfoService();
  final FlutterSecureStorage secureStorage = const FlutterSecureStorage();

  var isLoading = false.obs;
  var isLoggedIn = false.obs;
  var userType = ''.obs; // 'student' or 'teacher'
  var userName = ''.obs;
  var userSchool = ''.obs;
  var userId = ''.obs;

  @override
  void onInit() {
    super.onInit();
    checkLoginStatus();
  }

  void checkLoginStatus() async {
    String? token = await secureStorage.read(key: 'auth_token');
    if (token != null) {
      isLoggedIn.value = true;
      userType.value = await secureStorage.read(key: 'user_type') ?? '';
      userName.value = await secureStorage.read(key: 'user_name') ?? '';
      userSchool.value = await secureStorage.read(key: 'user_school') ?? '';
      userId.value = await secureStorage.read(key: 'user_id') ?? '';
    }
  }

  /// Login sebagai Siswa menggunakan NISN
  /// Returns Map: {success: bool, message: String, isDeviceMismatch: bool}
  Future<Map<String, dynamic>> loginAsStudent(String nisn, String password) async {
    isLoading.value = true;
    try {
      String deviceId = await deviceService.getDeviceId();

      var response = await apiClient.client.post('/auth/student/login', data: {
        'nisn': nisn,
        'password': password,
        'device_id': deviceId,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        String token = data['token'];
        String type = data['type'];
        final user = data['user'];

        await secureStorage.write(key: 'auth_token', value: token);
        await secureStorage.write(key: 'user_type', value: type);
        await secureStorage.write(key: 'tenant_id', value: user['school_id'].toString());
        await secureStorage.write(key: 'user_name', value: user['name']);
        await secureStorage.write(key: 'user_school', value: user['school']);
        await secureStorage.write(key: 'user_id', value: user['id'].toString());

        isLoggedIn.value = true;
        userType.value = type;
        userName.value = user['name'];
        userSchool.value = user['school'];
        userId.value = user['id'].toString();
        return {'success': true, 'message': 'Login berhasil', 'isDeviceMismatch': false};
      }
      return {'success': false, 'message': 'Login gagal', 'isDeviceMismatch': false};
    } catch (e) {
      return _handleLoginError(e);
    } finally {
      isLoading.value = false;
    }
  }

  /// Login sebagai Guru menggunakan NIP
  /// Returns Map: {success: bool, message: String, isDeviceMismatch: bool}
  Future<Map<String, dynamic>> loginAsTeacher(String nip, String password) async {
    isLoading.value = true;
    try {
      String deviceId = await deviceService.getDeviceId();

      var response = await apiClient.client.post('/auth/teacher/login', data: {
        'nip': nip,
        'password': password,
        'device_id': deviceId,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        String token = data['token'];
        String type = data['type'];
        final user = data['user'];

        await secureStorage.write(key: 'auth_token', value: token);
        await secureStorage.write(key: 'user_type', value: type);
        await secureStorage.write(key: 'tenant_id', value: user['school_id'].toString());
        await secureStorage.write(key: 'user_name', value: user['name']);
        await secureStorage.write(key: 'user_school', value: user['school']);
        await secureStorage.write(key: 'user_id', value: user['id'].toString());

        isLoggedIn.value = true;
        userType.value = type;
        userName.value = user['name'];
        userSchool.value = user['school'];
        userId.value = user['id'].toString();
        return {'success': true, 'message': 'Login berhasil', 'isDeviceMismatch': false};
      }
      return {'success': false, 'message': 'Login gagal', 'isDeviceMismatch': false};
    } catch (e) {
      return _handleLoginError(e);
    } finally {
      isLoading.value = false;
    }
  }

  /// Logout
  Future<void> logout() async {
    try {
      await apiClient.client.post('/auth/logout');
    } catch (_) {
      // Ignore logout errors
    }
    await secureStorage.deleteAll();
    isLoggedIn.value = false;
    userType.value = '';
    userName.value = '';
    userSchool.value = '';
    userId.value = '';
  }

  /// Handle login errors dan deteksi device mismatch
  Map<String, dynamic> _handleLoginError(dynamic e) {
    String message = 'Terjadi kesalahan.';
    bool isDeviceMismatch = false;

    if (e is DioException && e.response != null) {
      final data = e.response?.data;
      final statusCode = e.response?.statusCode;

      if (data is Map && data.containsKey('message')) {
        message = data['message'];
      } else if (data is Map && data.containsKey('errors')) {
        final errors = data['errors'] as Map;
        message = errors.values.first is List
            ? (errors.values.first as List).first.toString()
            : errors.values.first.toString();
      }

      // Deteksi device binding mismatch dari response 403
      if (statusCode == 403 && message.toLowerCase().contains('perangkat lain')) {
        isDeviceMismatch = true;
      }
    } else {
      message = e.toString();
    }

    return {
      'success': false,
      'message': message,
      'isDeviceMismatch': isDeviceMismatch,
    };
  }
}
