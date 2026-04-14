<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $schools = School::withCount(['students', 'teachers', 'locations'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('npsn', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10);

        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:schools',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'late_threshold' => 'required|date_format:H:i',
            'checkout_time' => 'required|date_format:H:i',
            'logo' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        School::create($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil ditambahkan!');
    }

    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'required|string|max:20|unique:schools,npsn,' . $school->id,
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'late_threshold' => 'required|date_format:H:i',
            'checkout_time' => 'required|date_format:H:i',
            'is_active' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('schools/logos', 'public');
        }

        $school->update($validated);

        return redirect()->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil diperbarui!');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil dihapus!');
    }
}
