@extends('layouts.admin')
@section('title', 'Edit Sekolah')
@section('page-title', 'Edit Sekolah')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.schools.update', $school) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $school->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NPSN <span class="text-red-500">*</span></label>
                    <input type="text" name="npsn" value="{{ old('npsn', $school->npsn) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                    @error('npsn')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $school->email) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $school->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Batas Keterlambatan <span class="text-red-500">*</span></label>
                    <input type="time" name="late_threshold" value="{{ old('late_threshold', \Carbon\Carbon::parse($school->late_threshold)->format('H:i')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('late_threshold')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Jam Pulang <span class="text-red-500">*</span></label>
                    <input type="time" name="checkout_time" value="{{ old('checkout_time', \Carbon\Carbon::parse($school->checkout_time)->format('H:i')) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('checkout_time')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('address', $school->address) }}</textarea>
                    @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Logo</label>
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    @error('logo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $school->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700">Sekolah Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">Perbarui</button>
                <a href="{{ route('admin.schools.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
