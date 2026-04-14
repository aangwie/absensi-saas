@extends('layouts.admin')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm">
    <div class="p-5 border-b border-slate-200/60">
        <h3 class="text-base font-bold text-slate-800">Riwayat Absensi</h3>
        <p class="text-xs text-slate-500 mt-0.5">Filter dan lihat data absensi siswa & guru</p>
    </div>
    <div class="p-5">
        {{-- Filters --}}
        <form method="GET" class="mb-5 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-[10px] font-medium text-slate-500 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date', today()->format('Y-m-d')) }}" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-[10px] font-medium text-slate-500 mb-1">Tipe</label>
                <select name="type" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="check_in" {{ request('type') == 'check_in' ? 'selected' : '' }}>Check-in</option>
                    <option value="check_out" {{ request('type') == 'check_out' ? 'selected' : '' }}>Check-out</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-medium text-slate-500 mb-1">Status</label>
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="on_time" {{ request('status') == 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-medium text-slate-500 mb-1">Kategori</label>
                <select name="attendee_type" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="student" {{ request('attendee_type') == 'student' ? 'selected' : '' }}>Siswa</option>
                    <option value="teacher" {{ request('attendee_type') == 'teacher' ? 'selected' : '' }}>Guru</option>
                </select>
            </div>
            @if(auth()->user()->isSuperAdmin() && $schools->count() > 0)
            <div>
                <label class="block text-[10px] font-medium text-slate-500 mb-1">Sekolah</label>
                <select name="school_id" class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label class="block text-[10px] font-medium text-slate-500 mb-1">Cari Nama</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="px-3 py-2 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 w-40">
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">Filter</button>
            <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-600 text-sm font-semibold hover:bg-slate-200 transition-colors">Reset</a>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Nama</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Kategori</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Tipe</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Waktu</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Lokasi</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Jarak</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Akurasi</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Device</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($attendances as $i => $att)
                    <tr class="hover:bg-slate-50/50 transition-colors {{ $att->is_mock_suspected ? 'bg-red-50/50' : '' }}">
                        <td class="py-3 px-3 text-slate-500">{{ $attendances->firstItem() + $i }}</td>
                        <td class="py-3 px-3">
                            <p class="font-semibold text-slate-800">{{ $att->attendee_name }}</p>
                            <p class="text-[10px] text-slate-400 font-mono">{{ $att->attendee_identifier }}</p>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold {{ $att->attendee_type === 'App\\Models\\Student' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">
                                {{ $att->attendee_type === 'App\\Models\\Student' ? 'Siswa' : 'Guru' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $att->type === 'check_in' ? 'bg-indigo-100 text-indigo-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $att->type === 'check_in' ? 'Masuk' : 'Pulang' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center text-slate-600 font-mono text-xs">{{ \Carbon\Carbon::parse($att->checked_at)->format('H:i:s') }}</td>
                        <td class="py-3 px-3 text-slate-600 text-xs">{{ $att->location?->name ?? '-' }}</td>
                        <td class="py-3 px-3 text-center text-xs text-slate-600">{{ round($att->distance_meters) }}m</td>
                        <td class="py-3 px-3 text-center text-xs text-slate-600">{{ $att->accuracy ? round($att->accuracy, 1) . 'm' : '-' }}</td>
                        <td class="py-3 px-3 text-center">
                            @if($att->is_mock_suspected)
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700" title="{{ $att->mock_reasons }}">
                                    ⚠ Mock
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold {{ $att->status === 'on_time' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $att->status === 'on_time' ? 'Tepat' : 'Telat' }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($att->user_agent)
                                <span class="inline-flex px-2 py-0.5 rounded-lg text-[10px] font-medium {{ str_contains($att->user_agent ?? '', 'Mobile') ? 'bg-sky-50 text-sky-600' : 'bg-slate-100 text-slate-500' }}" title="{{ $att->user_agent }}">
                                    {{ str_contains($att->user_agent ?? '', 'Mobile') ? '📱 Mobile' : '💻 Desktop' }}
                                </span>
                            @else
                                <span class="text-[10px] text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            <form id="delete-att-{{ $att->id }}" method="POST" action="{{ route('admin.attendances.destroy', $att) }}">@csrf @method('DELETE')</form>
                            <button onclick="confirmDelete('delete-att-{{ $att->id }}', 'absensi {{ $att->attendee_name }}')" class="p-2 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="py-12 text-center text-slate-400">Belum ada data absensi untuk tanggal ini</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $attendances->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
