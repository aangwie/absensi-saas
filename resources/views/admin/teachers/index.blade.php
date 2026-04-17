@extends('layouts.admin')
@section('title', 'Kelola Guru')
@section('page-title', 'Kelola Guru')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm">
    <div class="p-5 border-b border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Guru</h3>
            <p class="text-xs text-slate-500 mt-0.5">Password default guru: NPSN sekolah | Login via NIP</p>
        </div>
        <a href="{{ route('admin.teachers.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Guru
        </a>
    </div>
    <div class="p-5">
        <form method="GET" class="mb-4" id="search-form">
            <div class="relative max-w-sm">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" 
                       x-data
                       @input.debounce.500ms="$el.form.submit()"
                       autofocus
                       onfocus="var temp_value=this.value; this.value=''; this.value=temp_value"
                       placeholder="Cari nama atau NIP..." 
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
        </form>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">#</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Guru</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">NIP</th>
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Mata Pelajaran</th>
                        @if(auth()->user()->isSuperAdmin())
                        <th class="text-left py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Sekolah</th>
                        @endif
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Verifikasi</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Status</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Perangkat</th>
                        <th class="text-center py-3 px-3 text-xs font-semibold text-slate-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $i => $teacher)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3 px-3 text-slate-500">{{ $teachers->firstItem() + $i }}</td>
                        <td class="py-3 px-3">
                            <p class="font-semibold text-slate-800">{{ $teacher->name }}</p>
                            <p class="text-xs text-slate-400">{{ $teacher->phone ?? '-' }}</p>
                        </td>
                        <td class="py-3 px-3 font-mono text-slate-600">{{ $teacher->nip }}</td>
                        <td class="py-3 px-3 text-slate-600">{{ $teacher->subject ?? '-' }}</td>
                        @if(auth()->user()->isSuperAdmin())
                        <td class="py-3 px-3 text-slate-600">{{ $teacher->school->name }}</td>
                        @endif
                        <td class="py-3 px-3 text-center">
                            @if($teacher->verification_status === 'verified')
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700" title="Diverifikasi oleh: {{ $teacher->verifiedByUser?->name }}">Verified</span>
                            @elseif($teacher->verification_status === 'pending')
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Pending</span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-red-100 text-red-700">Rejected</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold {{ $teacher->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $teacher->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="py-3 px-3 text-center">
                            @if($teacher->device_id)
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700" title="{{ $teacher->device_id }}">Terikat</span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">Belum</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex items-center justify-center gap-1">
                                @if(auth()->user()->isSuperAdmin() && $teacher->verification_status !== 'verified')
                                    <form id="verify-teacher-{{ $teacher->id }}" method="POST" action="{{ route('admin.teachers.verify', $teacher) }}">@csrf</form>
                                    <button onclick="document.getElementById('verify-teacher-{{ $teacher->id }}').submit()" class="p-2 rounded-lg text-emerald-500 hover:bg-emerald-50 hover:text-emerald-600 transition-colors" title="Verifikasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                @endif
                                
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="p-2 rounded-lg text-slate-500 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form id="delete-teacher-{{ $teacher->id }}" method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}">@csrf @method('DELETE')</form>
                                <button onclick="confirmDelete('delete-teacher-{{ $teacher->id }}', '{{ $teacher->name }}')" class="p-2 rounded-lg text-slate-500 hover:bg-red-50 hover:text-red-600 transition-colors" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @if($teacher->device_id)
                                    <button onclick="showDeviceDetail('{{ addslashes($teacher->device_name ?? 'Tidak diketahui') }}', '{{ addslashes($teacher->device_version ?? 'Tidak diketahui') }}', '{{ addslashes($teacher->device_id) }}')" class="p-2 rounded-lg text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition-colors" title="Detail Perangkat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </button>
                                    <form id="clear-device-teacher-{{ $teacher->id }}" method="POST" action="{{ route('admin.teachers.clearDevice', $teacher) }}">@csrf</form>
                                    <button onclick="if(confirm('Reset perangkat {{ $teacher->name }}? Guru akan bisa login dari perangkat baru.')) document.getElementById('clear-device-teacher-{{ $teacher->id }}').submit()" class="p-2 rounded-lg text-slate-500 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Reset Perangkat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="py-12 text-center text-slate-400">Belum ada data guru</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $teachers->withQueryString()->links() }}</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showDeviceDetail(name, version, id) {
        Swal.fire({
            title: 'Detail Perangkat',
            html: `
                <div class="text-left mt-4 text-sm text-slate-600">
                    <p class="mb-2"><strong class="text-slate-800">Nama Perangkat:</strong><br/> ${name}</p>
                    <p class="mb-2"><strong class="text-slate-800">Versi OS:</strong><br/> ${version}</p>
                    <p class="mb-2"><strong class="text-slate-800">Device ID:</strong><br/> <span class="font-mono text-xs text-indigo-600">${id}</span></p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#4f46e5',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6',
            }
        });
    }
</script>
@endpush
