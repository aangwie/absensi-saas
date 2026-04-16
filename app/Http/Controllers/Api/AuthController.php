<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login for students using NISN
     */
    public function studentLogin(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'password' => 'required|string',
            'device_id' => 'required|string',
        ]);

        $student = Student::where('nisn', $request->nisn)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'nisn' => ['NISN atau password salah.'],
            ]);
        }

        if (!$student->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun siswa tidak aktif. Hubungi admin sekolah.',
            ], 403);
        }

        if ($student->verification_status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Akun siswa belum diverifikasi oleh Admin Sekolah.',
            ], 403);
        }

        // Device binding check
        if ($student->device_id && $student->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini sudah terdaftar di perangkat lain. Hubungi admin untuk mereset perangkat.',
            ], 403);
        }

        // Bind device if first login
        if (!$student->device_id) {
            // Cek apakah device_id sudah digunakan oleh akun lain (student atau teacher)
            $deviceUsedByStudent = Student::where('device_id', $request->device_id)
                ->where('id', '!=', $student->id)
                ->exists();
            $deviceUsedByTeacher = Teacher::where('device_id', $request->device_id)->exists();

            if ($deviceUsedByStudent || $deviceUsedByTeacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perangkat ini sudah terdaftar pada akun lain. Satu perangkat hanya bisa digunakan untuk satu akun. Hubungi SuperAdmin untuk reset.',
                ], 403);
            }

            $student->update(['device_id' => $request->device_id]);
        }

        // Revoke previous tokens
        $student->tokens()->delete();

        $token = $student->createToken('student-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'type' => 'student',
                'user' => [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class' => $student->class,
                    'school' => $student->school->name,
                    'school_id' => $student->school_id,
                ],
            ],
        ]);
    }

    /**
     * Login for teachers using NIP
     */
    public function teacherLogin(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
            'device_id' => 'required|string',
        ]);

        $teacher = Teacher::where('nip', $request->nip)->first();

        if (!$teacher || !Hash::check($request->password, $teacher->password)) {
            throw ValidationException::withMessages([
                'nip' => ['NIP atau password salah.'],
            ]);
        }

        if (!$teacher->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun guru tidak aktif. Hubungi admin sekolah.',
            ], 403);
        }

        if ($teacher->verification_status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Akun guru belum diverifikasi oleh Super Admin.',
            ], 403);
        }

        // Device binding check
        if ($teacher->device_id && $teacher->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini sudah terdaftar di perangkat lain. Hubungi admin untuk mereset perangkat.',
            ], 403);
        }

        // Bind device if first login
        if (!$teacher->device_id) {
            // Cek apakah device_id sudah digunakan oleh akun lain (student atau teacher)
            $deviceUsedByStudent = Student::where('device_id', $request->device_id)->exists();
            $deviceUsedByTeacher = Teacher::where('device_id', $request->device_id)
                ->where('id', '!=', $teacher->id)
                ->exists();

            if ($deviceUsedByStudent || $deviceUsedByTeacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perangkat ini sudah terdaftar pada akun lain. Satu perangkat hanya bisa digunakan untuk satu akun. Hubungi SuperAdmin untuk reset.',
                ], 403);
            }

            $teacher->update(['device_id' => $request->device_id]);
        }

        // Revoke previous tokens
        $teacher->tokens()->delete();

        $token = $teacher->createToken('teacher-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'type' => 'teacher',
                'user' => [
                    'id' => $teacher->id,
                    'nip' => $teacher->nip,
                    'name' => $teacher->name,
                    'subject' => $teacher->subject,
                    'school' => $teacher->school->name,
                    'school_id' => $teacher->school_id,
                ],
            ],
        ]);
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Get current user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $type = $user instanceof Student ? 'student' : 'teacher';

        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'type' => $type,
            'school' => $user->school->name,
            'school_id' => $user->school_id,
            'device_id' => $user->device_id,
        ];

        if ($type === 'student') {
            $data['nisn'] = $user->nisn;
            $data['class'] = $user->class;
        } else {
            $data['nip'] = $user->nip;
            $data['subject'] = $user->subject;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
