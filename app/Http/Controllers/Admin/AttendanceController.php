<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\School;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $attendances = Attendance::with(['location', 'school'])
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->when($request->date, fn($q) => $q->whereDate('checked_at', $request->date))
            ->when(!$request->date, fn($q) => $q->whereDate('checked_at', today()))
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->attendee_type, function ($q) use ($request) {
                if ($request->attendee_type === 'student') {
                    $q->where('attendee_type', 'App\\Models\\Student');
                } elseif ($request->attendee_type === 'teacher') {
                    $q->where('attendee_type', 'App\\Models\\Teacher');
                }
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->whereHasMorph('attendee', ['App\\Models\\Student', 'App\\Models\\Teacher'], function ($mq) use ($request) {
                        $mq->where('name', 'like', "%{$request->search}%");
                    });
                });
            })
            ->when($request->school_id && $user->isSuperAdmin(), fn($q) => $q->where('school_id', $request->school_id))
            ->orderBy('checked_at', 'desc')
            ->paginate(15);

        // Load attendee relationship for each attendance
        $attendances->getCollection()->transform(function ($attendance) {
            $attendance->load('attendee');
            return $attendance;
        });

        $schools = $user->isSuperAdmin() ? School::orderBy('name')->get() : collect();

        return view('admin.attendances.index', compact('attendances', 'schools'));
    }

    public function destroy(Request $request, Attendance $attendance)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $attendance->school_id != $user->school_id) {
            abort(403);
        }

        $attendance->delete();
        return redirect()->route('admin.attendances.index')
            ->with('success', 'Data absensi berhasil dihapus!');
    }
}
