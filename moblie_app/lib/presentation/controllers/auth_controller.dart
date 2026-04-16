import 'package:get/get.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../../core/network/api_client.dart';
import '../../services/device_info_service.dart';

class AuthController extends GetxController {
  final ApiClient apiClient = ApiClient();
  final DeviceInfoService deviceService = DeviceInfoService();
  final FlutterSecureStorage secureStorage = const FlutterSecureStorage();

  var isLoading = false.obs;
  var isLoggedIn = false.obs;

  @override
  void onInit() {
    super.onInit();
    checkLoginStatus();
  }

  void checkLoginStatus() async {
    String? token = await secureStorage.read(key: 'auth_token');
    if (token != null) {
      isLoggedIn.value = true;
    }
  }

  Future<bool> login(String username, String password, String email) async {
    isLoading.value = true;
    try {
      String deviceId = await deviceService.getDeviceId();

      var response = await apiClient.client.post('/login', data: {
        'username_or_email': email,
        'password': password,
        'device_id': deviceId,
      });

      if (response.statusCode == 200) {
        // Asumsi API mengembalikan token dan tenant_id
        String token = response.data['token'];
        String tenantId = response.data['tenant_id'].toString();

        await secureStorage.write(key: 'auth_token', value: token);
        await secureStorage.write(key: 'tenant_id', value: tenantId);

        isLoggedIn.value = true;
        return true;
      }
      return false;
    } catch (e) {
      Get.snackbar('Login Gagal', e.toString());
      return false;
    } finally {
      isLoading.value = false;
    }
  }

  void logout() async {
    await secureStorage.deleteAll();
    isLoggedIn.value = false;
  }
}
