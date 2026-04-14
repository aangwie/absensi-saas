@extends('layouts.admin')
@section('title', 'Kelola Lokasi')
@section('page-title', 'Kelola Lokasi')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm">
    <div class="p-5 border-b border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Lokasi GPS</h3>
            <p class="text-xs text-slate-500 mt-0.5">Lokasi check-in/check-out absensi</p>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Lokasi
        </a>
    </div>
    <div class="p-5">
        <form method="GET" class="mb-4">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari lokasi..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Lokasi</th>
                        @if(auth()->user()->isSuperAdmin())
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Sekolah</th>
                        @endif
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Latitude</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Longitude</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Radius (m)</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($locations as $i => $location)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-3 text-slate-500">{{ $locations->firstItem() + $i }}</td>
                        <td class="py-3 px-3 font-semibold text-slate-800">{{ $location->name }}</td>
                        @if(auth()->user()->isSuperAdmin())
                        <td class="py-3 px-3 text-slate-600">{{ $location->school->name }}</td>
                        @endif
                        <td class="py-3 px-3 text-center font-mono text-xs text-slate-600">{{ $location->latitude }}</td>
                        <td class="py-3 px-3 text-center font-mono text-xs text-slate-600">{{ $location->longitude }}</td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold">{{ $location->radius_max }}m</span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold {{ $location->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $location->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('admin.locations.edit', $location) }}" class="p-2 rounded-lg text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form id="delete-location-{{ $location->id }}" method="POST" action="{{ route('admin.locations.destroy', $location) }}">@csrf @method('DELETE')</form>
                                <button onclick="confirmDelete('delete-location-{{ $location->id }}', '{{ $location->name }}')" class="p-2 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-12 text-center text-slate-400">Belum ada data lokasi</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $locations->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
