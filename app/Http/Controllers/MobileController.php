<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileController extends Controller
{
    /**
     * Show mobile login page
     */
    public function loginPage()
    {
        // If already logged in, redirect to dashboard
        if (session('mobile_token')) {
            return redirect()->route('mobile.dashboard');
        }

        return view('mobile.login');
    }

    /**
     * Handle mobile login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login_type' => 'required|in:student,teacher',
            'identifier' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginType = $request->login_type;

        if ($loginType === 'student') {
            $user = Student::where('nisn', $request->identifier)->first();
            $identifierLabel = 'NISN';

            if (!$user || !Hash::check($request->password, $user->password)) {
                return back()->withErrors(['identifier' => 'NISN atau password salah.'])->withInput();
            }

            if (!$user->is_active) {
                return back()->withErrors(['identifier' => 'Akun siswa tidak aktif. Hubungi admin sekolah.'])->withInput();
            }

            if ($user->verification_status !== 'verified') {
                return back()->withErrors(['identifier' => 'Akun siswa belum diverifikasi oleh Admin Sekolah.'])->withInput();
            }

            // Revoke previous tokens and create new one
            $user->tokens()->delete();
            $token = $user->createToken('mobile-student-token')->plainTextToken;

            // Store in session
            session([
                'mobile_token' => $token,
                'mobile_user_type' => 'student',
                'mobile_user_id' => $user->id,
                'mobile_user_name' => $user->name,
                'mobile_user_identifier' => $user->nisn,
                'mobile_user_class' => $user->class,
                'mobile_school_name' => $user->school->name,
                'mobile_school_id' => $user->school_id,
            ]);

        } else {
            $user = Teacher::where('nip', $request->identifier)->first();
            $identifierLabel = 'NIP';

            if (!$user || !Hash::check($request->password, $user->password)) {
                return back()->withErrors(['identifier' => 'NIP atau password salah.'])->withInput();
            }

            if (!$user->is_active) {
                return back()->withErrors(['identifier' => 'Akun guru tidak aktif. Hubungi admin sekolah.'])->withInput();
            }

            if ($user->verification_status !== 'verified') {
                return back()->withErrors(['identifier' => 'Akun guru belum diverifikasi oleh Super Admin.'])->withInput();
            }

            // Revoke previous tokens and create new one
            $user->tokens()->delete();
            $token = $user->createToken('mobile-teacher-token')->plainTextToken;

            // Store in session
            session([
                'mobile_token' => $token,
                'mobile_user_type' => 'teacher',
                'mobile_user_id' => $user->id,
                'mobile_user_name' => $user->name,
                'mobile_user_identifier' => $user->nip,
                'mobile_user_subject' => $user->subject,
                'mobile_school_name' => $user->school->name,
                'mobile_school_id' => $user->school_id,
            ]);
        }

        return redirect()->route('mobile.dashboard');
    }

    /**
     * Show mobile dashboard
     */
    public function dashboard()
    {
        if (!session('mobile_token')) {
            return redirect()->route('mobile.login');
        }

        $userType = session('mobile_user_type');
        $userId = session('mobile_user_id');

        // Get today's attendance status
        $attendeeType = $userType === 'student' ? 'App\\Models\\Student' : 'App\\Models\\Teacher';

        $checkIn = Attendance::where('attendee_type', $attendeeType)
            ->where('attendee_id', $userId)
            ->where('type', 'check_in')
            ->whereDate('checked_at', today())
            ->first();

        $checkOut = Attendance::where('attendee_type', $attendeeType)
            ->where('attendee_id', $userId)
            ->where('type', 'check_out')
            ->whereDate('checked_at', today())
            ->first();

        // Get recent attendance history (last 7 days)
        $history = Attendance::where('attendee_type', $attendeeType)
            ->where('attendee_id', $userId)
            ->with('location')
            ->orderBy('checked_at', 'desc')
            ->limit(14) // 7 days × 2 (check-in + check-out)
            ->get();

        return view('mobile.dashboard', compact('checkIn', 'checkOut', 'history'));
    }

    /**
     * Logout mobile session
     */
    public function logout(Request $request)
    {
        // Clear mobile session data
        $request->session()->forget([
            'mobile_token',
            'mobile_user_type',
            'mobile_user_id',
            'mobile_user_name',
            'mobile_user_identifier',
            'mobile_user_class',
            'mobile_user_subject',
            'mobile_school_name',
            'mobile_school_id',
        ]);

        return redirect()->route('mobile.login')->with('success', 'Berhasil logout.');
    }
}
