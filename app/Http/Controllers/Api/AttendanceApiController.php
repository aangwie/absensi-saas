<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
        ]);

        $user = $request->user();
        $school = $user->school;

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

        // Save attendance
        $attendance = Attendance::create([
            'school_id' => $school->id,
            'attendee_type' => get_class($user),
            'attendee_id' => $user->id,
            'location_id' => $result['location']->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'distance_meters' => $result['distance'],
            'device_id' => $request->device_id ?? $user->device_id,
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
        ]);

        $user = $request->user();
        $school = $user->school;

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
            'device_id' => $request->device_id ?? $user->device_id,
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
}
