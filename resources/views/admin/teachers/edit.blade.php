@extends('layouts.admin')
@section('title', 'Edit Guru')
@section('page-title', 'Edit Guru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.teachers.update', $teacher) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Sekolah <span class="text-red-500">*</span></label>
                    <select name="school_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $teacher->school_id == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">NIP <span class="text-red-500">*</span></label>
                    <input type="text" name="nip" value="{{ old('nip', $teacher->nip) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-mono">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Mata Pelajaran</label>
                    <input type="text" name="subject" value="{{ old('subject', $teacher->subject) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $teacher->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-slate-700">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="reset_password" value="1" class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <span class="text-sm text-slate-700">Reset Password</span>
                    </label>
                </div>
                <div class="sm:col-span-2 pt-2">
                    <p class="text-sm text-slate-600">Status Verifikasi: 
                        <span class="font-bold {{ $teacher->verification_status === 'verified' ? 'text-blue-600' : ($teacher->verification_status === 'pending' ? 'text-amber-600' : 'text-red-600') }}">
                            {{ ucfirst($teacher->verification_status) }}
                        </span>
                        @if($teacher->verifiedByUser)
                         oleh {{ $teacher->verifiedByUser->name }} pada {{ \Carbon\Carbon::parse($teacher->verified_at)->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl transition-all">Perbarui</button>
                <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition-colors">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
