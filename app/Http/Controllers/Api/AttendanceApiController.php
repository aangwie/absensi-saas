<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceSchedule;
use App\Models\Location;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\HaversineService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceApiController extends Controller
{
    protected HaversineService $haversineService;

    public function __construct(HaversineService $haversineService)
    {
        $this->haversineService = $haversineService;
    }

    /**
     * Check-in attendance
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'device_id' => 'nullable|string',
            'accuracy' => 'nullable|numeric',
            'is_mock_suspected' => 'nullable|boolean',
            'mock_reasons' => 'nullable|string|max:1000',
        ]);

        // Mock location check — reject if fake GPS detected
        if ($request->boolean('is_mock_suspected')) {
            return response()->json([
                'success' => false,
                'message' => 'Fake GPS terdeteksi! Anda tidak diizinkan melakukan absensi dengan lokasi palsu.',
                'data' => [
                    'mock_reasons' => $request->mock_reasons,
                ],
            ], 403);
        }

        // Reject suspiciously perfect accuracy (real GPS never gives 0)
        if ($request->has('accuracy') && $request->accuracy !== null && $request->accuracy < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Akurasi GPS tidak valid. Pastikan Anda tidak menggunakan lokasi palsu.',
            ], 403);
        }

        $user = $request->user();
        $school = $user->school;

        // Check attendance schedule time window
        $schedule = AttendanceSchedule::getTodaySchedule($school->id);
        if ($schedule) {
            if (!$schedule->isWithinCheckInWindow()) {
                $window = $schedule->check_in_window;
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu absen masuk telah habis. Jadwal absen masuk hari ini: ' . $window,
                    'data' => ['schedule' => $window],
                ], 403);
            }
        }

        // Check if already checked in today
        $existingCheckIn = Attendance::where('attendee_type', get_class($user))
            ->where('attendee_id', $user->id)
            ->where('type', 'check_in')
            ->whereDate('checked_at', today())
            ->first();

        if ($existingCheckIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-in hari ini pada pukul ' . Carbon::parse($existingCheckIn->checked_at)->format('H:i'),
            ], 422);
        }

        // Get active locations for this school
        $locations = Location::where('school_id', $school->id)->active()->get();

        if ($locations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada lokasi yang terdaftar untuk sekolah ini.',
            ], 422);
        }

        // Check distance using Haversine
        $result = $this->haversineService->checkWithinRadius(
            (float) $request->latitude,
            (float) $request->longitude,
            $locations
        );

        if (!$result['within_radius']) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius sekolah. Jarak: ' . round($result['distance'], 1) . ' meter (max: ' . $result['max_radius'] . ' meter)',
                'data' => [
                    'distance' => round($result['distance'], 1),
                    'max_radius' => $result['max_radius'],
                    'nearest_location' => $result['location']?->name,
                ],
            ], 403);
        }

        // Determine status based on late_threshold
        $now = Carbon::now();
        $lateThreshold = Carbon::parse($school->late_threshold);
        $status = $now->format('H:i:s') <= $lateThreshold->format('H:i:s') ? 'on_time' : 'late';

        // Save attendance with mock detection data
        $attendance = Attendance::create([
            'school_id' => $school->id,
            'attendee_type' => get_class($user),
            'attendee_id' => $user->id,
            'location_id' => $result['location']->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_meters' => $result['distance'],
            'accuracy' => $request->accuracy,
            'device_id' => $request->device_id ?? $user->device_id,
            'is_mock_suspected' => false,
            'mock_reasons' => $request->mock_reasons,
            'user_agent' => $request->userAgent(),
            'type' => 'check_in',
            'status' => $status,
            'checked_at' => $now,
        ]);

        $userType = $user instanceof Student ? 'Siswa' : 'Guru';

        return response()->json([
            'success' => true,
            'message' => 'Check-in berhasil! Status: ' . ($status === 'on_time' ? 'Tepat Waktu' : 'Terlambat'),
            'data' => [
                'attendance_id' => $attendance->id,
                'name' => $user->name,
                'type' => $userType,
                'school_name' => $school->name,
                'location_name' => $result['location']->name,
                'distance' => round($result['distance'], 2),
                'status' => $status,
                'checked_at' => $now->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Check-out attendance
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'device_id' => 'nullable|string',
            'accuracy' => 'nullable|numeric',
            'is_mock_suspected' => 'nullable|boolean',
            'mock_reasons' => 'nullable|string|max:1000',
        ]);

        // Mock location check — reject if fake GPS detected
        if ($request->boolean('is_mock_suspected')) {
            return response()->json([
                'success' => false,
                'message' => 'Fake GPS terdeteksi! Anda tidak diizinkan melakukan absensi dengan lokasi palsu.',
            ], 403);
        }

        // Reject suspiciously perfect accuracy
        if ($request->has('accuracy') && $request->accuracy !== null && $request->accuracy < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Akurasi GPS tidak valid. Pastikan Anda tidak menggunakan lokasi palsu.',
            ], 403);
        }

        $user = $request->user();
        $school = $user->school;

        // Check attendance schedule time window
        $schedule = AttendanceSchedule::getTodaySchedule($school->id);
        if ($schedule) {
            if (!$schedule->isWithinCheckOutWindow()) {
                $window = $schedule->check_out_window;
                return response()->json([
                    'success' => false,
                    'message' => 'Waktu absen pulang telah habis. Jadwal absen pulang hari ini: ' . $window,
                    'data' => ['schedule' => $window],
                ], 403);
            }
        }

        // Check if already checked out today
        $existingCheckOut = Attendance::where('attendee_type', get_class($user))
            ->where('attendee_id', $user->id)
            ->where('type', 'check_out')
            ->whereDate('checked_at', today())
            ->first();

        if ($existingCheckOut) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan check-out hari ini.',
            ], 422);
        }

        // Must check-in first
        $checkIn = Attendance::where('attendee_type', get_class($user))
            ->where('attendee_id', $user->id)
            ->where('type', 'check_in')
            ->whereDate('checked_at', today())
            ->first();

        if (!$checkIn) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check-in hari ini.',
            ], 422);
        }

        // Get active locations for this school
        $locations = Location::where('school_id', $school->id)->active()->get();

        // Check distance
        $result = $this->haversineService->checkWithinRadius(
            (float) $request->latitude,
            (float) $request->longitude,
            $locations
        );

        if (!$result['within_radius']) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar radius sekolah. Jarak: ' . round($result['distance'], 1) . ' meter (max: ' . $result['max_radius'] . ' meter)',
                'data' => [
                    'distance' => round($result['distance'], 1),
                    'max_radius' => $result['max_radius'],
                    'nearest_location' => $result['location']?->name,
                ],
            ], 403);
        }

        $now = Carbon::now();

        $attendance = Attendance::create([
            'school_id' => $school->id,
            'attendee_type' => get_class($user),
            'attendee_id' => $user->id,
            'location_id' => $result['location']->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_meters' => $result['distance'],
            'accuracy' => $request->accuracy,
            'device_id' => $request->device_id ?? $user->device_id,
            'is_mock_suspected' => false,
            'mock_reasons' => $request->mock_reasons,
            'user_agent' => $request->userAgent(),
            'type' => 'check_out',
            'status' => 'on_time',
            'checked_at' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-out berhasil!',
            'data' => [
                'attendance_id' => $attendance->id,
                'location_name' => $result['location']->name,
                'distance' => round($result['distance'], 2),
                'checked_at' => $now->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Get attendance history
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $attendances = Attendance::where('attendee_type', get_class($user))
            ->where('attendee_id', $user->id)
            ->with('location')
            ->orderBy('checked_at', 'desc')
            ->paginate(20);

        // Manually map to ensure formatted dates appear in JSON
        $attendances->getCollection()->transform(function ($item) {
            $checkedAt = Carbon::parse($item->checked_at)->timezone('Asia/Jakarta');
            return [
                'id' => $item->id,
                'type' => $item->type,
                'status' => $item->status,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'distance_meters' => $item->distance_meters,
                'accuracy' => $item->accuracy,
                'checked_at' => $checkedAt->format('Y-m-d H:i:s'),
                'checked_at_formatted' => $checkedAt->format('d/m/Y H:i:s'),
                'checked_at_date' => $checkedAt->format('d/m/Y'),
                'checked_at_time' => $checkedAt->format('H:i'),
                'timezone' => 'WIB',
                'location' => $item->location ? [
                    'id' => $item->location->id,
                    'name' => $item->location->name,
                    'radius_max' => $item->location->radius_max,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    /**
     * Get today's attendance status
     */
    public function todayStatus(Request $request)
    {
        $user = $request->user();

        $checkIn = Attendance::where('attendee_type', get_class($user))
            ->where('attendee_id', $user->id)
            ->where('type', 'check_in')
            ->whereDate('checked_at', today())
            ->first();

        $checkOut = Attendance::where('attendee_type', get_class($user))
            ->where('attendee_id', $user->id)
            ->where('type', 'check_out')
            ->whereDate('checked_at', today())
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'has_checked_in' => (bool) $checkIn,
                'has_checked_out' => (bool) $checkOut,
                'check_in' => $checkIn ? [
                    'time' => Carbon::parse($checkIn->checked_at)->format('H:i:s'),
                    'status' => $checkIn->status,
                    'location' => $checkIn->location?->name,
                    'distance' => $checkIn->distance_meters,
                ] : null,
                'check_out' => $checkOut ? [
                    'time' => Carbon::parse($checkOut->checked_at)->format('H:i:s'),
                    'location' => $checkOut->location?->name,
                ] : null,
            ],
        ]);
    }

    /**
     * Get school locations for the authenticated user
     */
    public function schoolLocations(Request $request)
    {
        $user = $request->user();
        $school = $user->school;

        $locations = Location::where('school_id', $school->id)
            ->active()
            ->get(['id', 'name', 'latitude', 'longitude', 'radius_max']);

        return response()->json([
            'success' => true,
            'data' => [
                'school_name' => $school->name,
                'locations' => $locations->map(function ($loc) {
                    return [
                        'id' => $loc->id,
                        'name' => $loc->name,
                        'latitude' => (float) $loc->latitude,
                        'longitude' => (float) $loc->longitude,
                        'radius' => $loc->radius_max,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Get today's attendance schedule for the authenticated user's school
     */
    public function todaySchedule(Request $request)
    {
        $user = $request->user();
        $school = $user->school;
        $schedule = AttendanceSchedule::getTodaySchedule($school->id);
        $dayNames = AttendanceSchedule::$dayNames;
        $todayDay = now()->dayOfWeek;

        if (!$schedule) {
            return response()->json([
                'success' => true,
                'data' => [
                    'day' => $dayNames[$todayDay] ?? '-',
                    'has_schedule' => false,
                    'message' => 'Tidak ada jadwal absensi untuk hari ini.',
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'day' => $dayNames[$todayDay] ?? '-',
                'has_schedule' => true,
                'is_active' => $schedule->is_active,
                'check_in' => [
                    'start' => $schedule->check_in_start ? substr($schedule->check_in_start, 0, 5) : null,
                    'end' => $schedule->check_in_end ? substr($schedule->check_in_end, 0, 5) : null,
                    'is_open' => $schedule->isWithinCheckInWindow(),
                ],
                'check_out' => [
                    'start' => $schedule->check_out_start ? substr($schedule->check_out_start, 0, 5) : null,
                    'end' => $schedule->check_out_end ? substr($schedule->check_out_end, 0, 5) : null,
                    'is_open' => $schedule->isWithinCheckOutWindow(),
                ],
                'server_time' => now()->format('H:i'),
            ],
        ]);
    }
}
