import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:dio/dio.dart';
import 'package:intl/intl.dart';
import '../../core/network/api_client.dart';
import '../controllers/auth_controller.dart';

class HistoryScreen extends StatefulWidget {
  const HistoryScreen({super.key});

  @override
  State<HistoryScreen> createState() => HistoryScreenState();
}

class HistoryScreenState extends State<HistoryScreen> {
  final ApiClient apiClient = ApiClient();
  final AuthController auth = Get.find<AuthController>();
  List<dynamic> attendances = [];
  bool isLoading = true;
  int currentPage = 1;
  int lastPage = 1;

  @override
  void initState() {
    super.initState();
    fetchHistory();
  }

  Future<void> fetchHistory({int page = 1}) async {
    setState(() => isLoading = true);
    try {
      var response = await apiClient.client.get('/attendance/history', queryParameters: {
        'page': page,
      });
      if (response.data['success'] == true) {
        final paginated = response.data['data'];
        setState(() {
          attendances = paginated['data'] ?? [];
          currentPage = paginated['current_page'] ?? 1;
          lastPage = paginated['last_page'] ?? 1;
        });
      }
    } catch (e) {
      String message = 'Gagal memuat riwayat.';
      if (e is DioException && e.response?.data is Map) {
        message = e.response?.data['message'] ?? message;
      }
      Get.snackbar('Error', message);
    } finally {
      setState(() => isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          _buildHeader(context),
          Expanded(
            child: isLoading
                ? const Center(child: CircularProgressIndicator())
                : attendances.isEmpty
                    ? _buildEmpty()
                    : _buildList(),
          ),
          if (!isLoading && lastPage > 1) _buildPagination(),
        ],
      ),
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
          colors: [Colors.indigo.shade700, Colors.indigo.shade500],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: const BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      child: Row(
        children: [
          const Icon(Icons.history, color: Colors.white, size: 28),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Riwayat Absensi',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                Text(
                  auth.userName.value,
                  style: TextStyle(
                    color: Colors.white.withOpacity(0.8),
                    fontSize: 13,
                  ),
                ),
              ],
            ),
          ),
          IconButton(
            onPressed: () => fetchHistory(),
            icon: const Icon(Icons.refresh, color: Colors.white),
          ),
        ],
      ),
    );
  }

  Widget _buildEmpty() {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.event_busy, size: 80, color: Colors.grey.shade300),
          const SizedBox(height: 16),
          Text(
            'Belum ada riwayat absensi',
            style: TextStyle(fontSize: 16, color: Colors.grey.shade500),
          ),
        ],
      ),
    );
  }

  Widget _buildList() {
    return RefreshIndicator(
      onRefresh: () => fetchHistory(page: currentPage),
      child: ListView.builder(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        itemCount: attendances.length,
        itemBuilder: (context, index) {
          final item = attendances[index];
          return _buildHistoryCard(item);
        },
      ),
    );
  }

  Widget _buildHistoryCard(Map<String, dynamic> item) {
    final bool isCheckIn = item['type'] == 'check_in';
    final String status = item['status'] ?? '';
    final double distance = (item['distance_meters'] as num?)?.toDouble() ?? 0;
    final String locationName = item['location']?['name'] ?? '-';
    final double radius = (item['location']?['radius_max'] as num?)?.toDouble() ?? 80;

    // Use server-formatted dates (already in WIB/GMT+7)
    final String formattedDate = item['checked_at_date'] ?? '-';
    final String formattedTime = item['checked_at_time'] ?? '-';
    final String timezone = item['timezone'] ?? 'WIB';

    Color statusColor;
    String statusLabel;
    IconData statusIcon;

    if (isCheckIn) {
      if (status == 'on_time') {
        statusColor = Colors.green;
        statusLabel = 'Tepat Waktu';
        statusIcon = Icons.check_circle;
      } else {
        statusColor = Colors.orange;
        statusLabel = 'Terlambat';
        statusIcon = Icons.warning;
      }
    } else {
      statusColor = Colors.blue;
      statusLabel = 'Pulang';
      statusIcon = Icons.logout;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 10,
            offset: const Offset(0, 2),
          ),
        ],
        border: Border.all(color: Colors.grey.shade100),
      ),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Row(
          children: [
            // Icon
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: statusColor.withOpacity(0.1),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(
                isCheckIn ? Icons.login : Icons.logout,
                color: statusColor,
                size: 24,
              ),
            ),
            const SizedBox(width: 14),
            // Content
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        isCheckIn ? 'Absen Masuk' : 'Absen Pulang',
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 15,
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: statusColor.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(statusIcon, size: 12, color: statusColor),
                            const SizedBox(width: 4),
                            Text(
                              statusLabel,
                              style: TextStyle(
                                color: statusColor,
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  // Location & distance
                  Row(
                    children: [
                      Icon(Icons.location_on, size: 14, color: Colors.grey.shade400),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          '$locationName — ${distance.toStringAsFixed(1)}m dari titik (radius ${radius.toInt()}m)',
                          style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            // Time
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(
                  formattedTime,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  '$formattedDate $timezone',
                  style: TextStyle(
                    fontSize: 11,
                    color: Colors.grey.shade500,
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildPagination() {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 10, horizontal: 20),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: Colors.grey.shade200)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          IconButton(
            onPressed: currentPage > 1 ? () => fetchHistory(page: currentPage - 1) : null,
            icon: const Icon(Icons.chevron_left),
          ),
          Text(
            'Hal $currentPage / $lastPage',
            style: const TextStyle(fontWeight: FontWeight.w500),
          ),
          IconButton(
            onPressed: currentPage < lastPage ? () => fetchHistory(page: currentPage + 1) : null,
            icon: const Icon(Icons.chevron_right),
          ),
        ],
      ),
    );
  }
}
