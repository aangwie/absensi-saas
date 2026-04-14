@extends('layouts.admin')
@section('title', 'Kelola Sekolah')
@section('page-title', 'Kelola Sekolah')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm">
    <div class="p-5 border-b border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Sekolah</h3>
            <p class="text-xs text-slate-500 mt-0.5">Kelola data sekolah dalam sistem</p>
        </div>
        <a href="{{ route('admin.schools.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Sekolah
        </a>
    </div>

    <div class="p-5">
        <form method="GET" class="mb-4">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari sekolah..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Sekolah</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">NPSN</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Batas Telat</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Siswa</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Guru</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($schools as $i => $school)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-3 text-slate-500">{{ $schools->firstItem() + $i }}</td>
                        <td class="py-3 px-3">
                            <p class="font-semibold text-slate-800">{{ $school->name }}</p>
                            <p class="text-xs text-slate-400">{{ $school->email }}</p>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-600">{{ $school->npsn }}</td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 text-xs font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($school->late_threshold)->format('H:i') }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center font-semibold text-slate-700">{{ $school->students_count }}</td>
                        <td class="py-3 px-3 text-center font-semibold text-slate-700">{{ $school->teachers_count }}</td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold {{ $school->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $school->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.schools.edit', $school) }}" class="p-2 rounded-lg text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form id="delete-school-{{ $school->id }}" method="POST" action="{{ route('admin.schools.destroy', $school) }}">
                                    @csrf @method('DELETE')
                                </form>
                                <button onclick="confirmDelete('delete-school-{{ $school->id }}', '{{ $school->name }}')" class="p-2 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <svg class="w-12 h-12 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <p>Belum ada data sekolah</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $schools->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
