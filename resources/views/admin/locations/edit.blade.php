@extends('layouts.admin')
@section('title', 'Edit Lokasi')
@section('page-title', 'Edit Lokasi')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.locations.update', $location) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Sekolah <span class="text-red-500">*</span></label>
                    <select name="school_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $location->school_id == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lokasi <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Latitude <span class="text-red-500">*</span></label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $location->latitude) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Longitude <span class="text-red-500">*</span></label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $location->longitude) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Radius Maksimum (meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius_max" value="{{ old('radius_max', $location->radius_max) }}" required min="10" max="1000" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ $location->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700">Lokasi Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">Perbarui</button>
                <a href="{{ route('admin.locations.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
