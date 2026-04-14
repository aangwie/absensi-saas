<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $schoolId = $user->isSuperAdmin() ? null : $user->school_id;

        // Total counts
        $totalSchools = $user->isSuperAdmin() ? School::count() : 1;
        $totalStudents = Student::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();
        $totalTeachers = Teacher::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->count();

        // Today's attendance
        $today = Carbon::today();
        $todayCheckIns = Attendance::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('type', 'check_in')
            ->whereDate('checked_at', $today)
            ->count();

        $todayOnTime = Attendance::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('type', 'check_in')
            ->where('status', 'on_time')
            ->whereDate('checked_at', $today)
            ->count();

        $todayLate = Attendance::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('type', 'check_in')
            ->where('status', 'late')
            ->whereDate('checked_at', $today)
            ->count();

        // Recent attendances
        $recentAttendances = Attendance::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->with(['location'])
            ->orderBy('checked_at', 'desc')
            ->limit(10)
            ->get();

        // Weekly chart data (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $weeklyData[] = [
                'date' => $date->format('d/m'),
                'on_time' => Attendance::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                    ->where('type', 'check_in')
                    ->where('status', 'on_time')
                    ->whereDate('checked_at', $date)
                    ->count(),
                'late' => Attendance::when($schoolId, fn($q) => $q->where('school_id', $schoolId))
                    ->where('type', 'check_in')
                    ->where('status', 'late')
                    ->whereDate('checked_at', $date)
                    ->count(),
            ];
        }

        return view('admin.dashboard', compact(
            'totalSchools',
            'totalStudents',
            'totalTeachers',
            'todayCheckIns',
            'todayOnTime',
            'todayLate',
            'recentAttendances',
            'weeklyData'
        ));
    }
}
