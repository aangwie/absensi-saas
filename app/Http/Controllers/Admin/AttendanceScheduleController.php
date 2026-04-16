<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSchedule;
use App\Models\School;
use Illuminate\Http\Request;

class AttendanceScheduleController extends Controller
{
    /**
     * Show attendance schedule settings page
     */
    public function index()
    {
        $schools = School::active()->orderBy('name')->get();
        $selectedSchoolId = request('school_id', $schools->first()?->id);

        $schedules = [];
        if ($selectedSchoolId) {
            $existing = AttendanceSchedule::where('school_id', $selectedSchoolId)
                ->orderBy('day_of_week')
                ->get()
                ->keyBy('day_of_week');

            // Build full week schedule (0-6)
            for ($i = 0; $i <= 6; $i++) {
                $schedules[$i] = $existing->get($i, new AttendanceSchedule([
                    'day_of_week' => $i,
                    'school_id' => $selectedSchoolId,
                    'is_active' => ($i >= 1 && $i <= 5), // Default: Senin-Jumat active
                ]));
            }
        }

        return view('admin.settings.attendance-schedule', compact('schools', 'selectedSchoolId', 'schedules'));
    }

    /**
     * Save attendance schedule for a school
     */
    public function store(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'schedules' => 'required|array',
            'schedules.*.day_of_week' => 'required|integer|between:0,6',
            'schedules.*.check_in_start' => 'nullable|date_format:H:i',
            'schedules.*.check_in_end' => 'nullable|date_format:H:i',
            'schedules.*.check_out_start' => 'nullable|date_format:H:i',
            'schedules.*.check_out_end' => 'nullable|date_format:H:i',
            'schedules.*.is_active' => 'nullable|boolean',
        ]);

        $schoolId = $request->school_id;

        foreach ($request->schedules as $data) {
            AttendanceSchedule::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'day_of_week' => $data['day_of_week'],
                ],
                [
                    'check_in_start' => $data['check_in_start'] ?? null,
                    'check_in_end' => $data['check_in_end'] ?? null,
                    'check_out_start' => $data['check_out_start'] ?? null,
                    'check_out_end' => $data['check_out_end'] ?? null,
                    'is_active' => isset($data['is_active']) ? true : false,
                ]
            );
        }

        return redirect()->route('admin.attendance-schedule.index', ['school_id' => $schoolId])
            ->with('success', 'Jadwal absensi berhasil disimpan.');
    }
}
