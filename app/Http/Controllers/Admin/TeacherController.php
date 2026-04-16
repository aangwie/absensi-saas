<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $teachers = Teacher::with('school')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('nip', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.teachers.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'nip' => 'required|string|max:30|unique:teachers',
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if (!$user->isSuperAdmin() && $validated['school_id'] != $user->school_id) {
            abort(403);
        }

        // Default password is NPSN of the school
        $school = School::findOrFail($validated['school_id']);
        $validated['password'] = Hash::make($school->npsn);

        if ($user->isSuperAdmin()) {
            $validated['verification_status'] = 'verified';
            $validated['verified_by'] = $user->id;
            $validated['verified_at'] = now();
        }

        Teacher::create($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil ditambahkan! Password default: ' . $school->npsn);
    }

    public function edit(Request $request, Teacher $teacher)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $teacher->school_id != $user->school_id) {
            abort(403);
        }

        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.teachers.edit', compact('teacher', 'schools'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $teacher->school_id != $user->school_id) {
            abort(403);
        }

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'nip' => 'required|string|max:30|unique:teachers,nip,' . $teacher->id,
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'reset_password' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->boolean('reset_password')) {
            $school = School::findOrFail($validated['school_id']);
            $validated['password'] = Hash::make($school->npsn);
        }

        unset($validated['reset_password']);
        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy(Request $request, Teacher $teacher)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $teacher->school_id != $user->school_id) {
            abort(403);
        }

        $teacher->delete();
        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus!');
    }

    public function verify(Request $request, Teacher $teacher)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya SuperAdmin yang dapat memverifikasi guru.');
        }

        $teacher->update([
            'verification_status' => 'verified',
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Guru berhasil diverifikasi.');
    }

    public function reject(Request $request, Teacher $teacher)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Hanya SuperAdmin yang dapat menolak guru.');
        }

        $teacher->update([
            'verification_status' => 'rejected',
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran guru ditolak.');
    }
}
