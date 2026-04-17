<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $students = Student::with('school')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nisn', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.students.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'nisn' => 'required|string|max:20|unique:students',
            'name' => 'required|string|max:255',
            'class' => 'nullable|string|max:20',
        ]);

        if (!$user->isSuperAdmin() && $validated['school_id'] != $user->school_id) {
            abort(403);
        }

        // Default password is NPSN of the school
        $school = School::findOrFail($validated['school_id']);
        $validated['password'] = Hash::make($school->npsn);

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $validated['verification_status'] = 'verified';
            $validated['verified_by'] = $user->id;
            $validated['verified_at'] = now();
        }

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan! Password default: ' . $school->npsn);
    }

    public function edit(Request $request, Student $student)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $student->school_id != $user->school_id) {
            abort(403);
        }

        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.students.edit', compact('student', 'schools'));
    }

    public function update(Request $request, Student $student)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $student->school_id != $user->school_id) {
            abort(403);
        }

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'name' => 'required|string|max:255',
            'class' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'reset_password' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->boolean('reset_password')) {
            $school = School::findOrFail($validated['school_id']);
            $validated['password'] = Hash::make($school->npsn);
        }

        unset($validated['reset_password']);
        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    public function destroy(Request $request, Student $student)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $student->school_id != $user->school_id) {
            abort(403);
        }

        $student->delete();
        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus!');
    }

    public function verify(Request $request, Student $student)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Anda tidak berhak memverifikasi siswa ini.');
        }

        $student->update([
            'verification_status' => 'verified',
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Siswa berhasil diverifikasi.');
    }

    public function reject(Request $request, Student $student)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Anda tidak berhak menolak siswa ini.');
        }

        $student->update([
            'verification_status' => 'rejected',
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran siswa ditolak.');
    }

    /**
     * Clear device binding for a student (allow new device)
     */
    public function clearDevice(Request $request, Student $student)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $student->school_id != $user->school_id) {
            abort(403, 'Anda tidak berhak mereset perangkat siswa ini.');
        }

        $student->update([
            'device_id' => null,
            'device_name' => null,
            'device_version' => null,
        ]);
        $student->tokens()->delete(); // Also revoke tokens

        return back()->with('success', "Perangkat siswa {$student->name} berhasil direset. Siswa dapat login dari perangkat baru.");
    }
}
