@extends('layouts.admin')
@section('title', 'Tambah Guru')
@section('page-title', 'Tambah Guru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
        <div class="mb-5 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs">
            <strong>Info:</strong> Password default guru adalah <strong>NPSN sekolah</strong>. Guru login melalui API menggunakan NIP + Password.
        </div>
        <form method="POST" action="{{ route('admin.teachers.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Sekolah <span class="text-red-500">*</span></label>
                    <select name="school_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }} ({{ $school->npsn }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIP <span class="text-red-500">*</span></label>
                    <input type="text" name="nip" value="{{ old('nip') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono" placeholder="198501012010011001">
                    @error('nip')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Budi Santoso, S.Pd">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Mata Pelajaran</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Matematika">
                    @error('subject')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="08123456789">
                    @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">Simpan</button>
                <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
