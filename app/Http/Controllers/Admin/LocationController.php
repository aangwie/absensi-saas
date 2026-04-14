<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\School;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $locations = Location::with('school')
            ->when(!$user->isSuperAdmin(), fn($q) => $q->where('school_id', $user->school_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->orderBy('name')
            ->paginate(10);

        return view('admin.locations.index', compact('locations'));
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.locations.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_max' => 'required|integer|min:10|max:1000',
        ]);

        // Ensure tenant access
        if (!$user->isSuperAdmin() && $validated['school_id'] != $user->school_id) {
            abort(403);
        }

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil ditambahkan!');
    }

    public function edit(Request $request, Location $location)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $location->school_id != $user->school_id) {
            abort(403);
        }

        $schools = $user->isSuperAdmin()
            ? School::active()->orderBy('name')->get()
            : School::where('id', $user->school_id)->get();

        return view('admin.locations.edit', compact('location', 'schools'));
    }

    public function update(Request $request, Location $location)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $location->school_id != $user->school_id) {
            abort(403);
        }

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_max' => 'required|integer|min:10|max:1000',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil diperbarui!');
    }

    public function destroy(Request $request, Location $location)
    {
        $user = $request->user();
        if (!$user->isSuperAdmin() && $location->school_id != $user->school_id) {
            abort(403);
        }

        $location->delete();
        return redirect()->route('admin.locations.index')
            ->with('success', 'Lokasi berhasil dihapus!');
    }
}
