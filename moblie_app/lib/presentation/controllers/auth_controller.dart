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
  Future<bool> loginAsStudent(String nisn, String password) async {
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
        return true;
      }
      return false;
    } catch (e) {
      _handleError(e);
      return false;
    } finally {
      isLoading.value = false;
    }
  }

  /// Login sebagai Guru menggunakan NIP
  Future<bool> loginAsTeacher(String nip, String password) async {
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
        return true;
      }
      return false;
    } catch (e) {
      _handleError(e);
      return false;
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

  void _handleError(dynamic e) {
    String message = 'Terjadi kesalahan.';
    if (e is DioException && e.response != null) {
      final data = e.response?.data;
      if (data is Map && data.containsKey('message')) {
        message = data['message'];
      } else if (data is Map && data.containsKey('errors')) {
        final errors = data['errors'] as Map;
        message = errors.values.first is List
            ? (errors.values.first as List).first.toString()
            : errors.values.first.toString();
      }
    } else {
      message = e.toString();
    }
    Get.snackbar('Login Gagal', message);
  }
}
