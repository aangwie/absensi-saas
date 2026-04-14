@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', auth()->user()->school?->name ?? 'Super Administrator')

@section('content')
<div class="space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Schools --}}
        @if(auth()->user()->isSuperAdmin())
        <div class="relative overflow-hidden bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Sekolah</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $totalSchools }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-violet-50 rounded-full"></div>
        </div>
        @endif

        {{-- Total Students --}}
        <div class="relative overflow-hidden bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Siswa</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $totalStudents }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-blue-50 rounded-full"></div>
        </div>

        {{-- Total Teachers --}}
        <div class="relative overflow-hidden bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Total Guru</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $totalTeachers }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-amber-50 rounded-full"></div>
        </div>

        {{-- Today Check-in --}}
        <div class="relative overflow-hidden bg-white rounded-2xl border border-slate-200/60 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Check-in Hari Ini</p>
                    <p class="text-3xl font-extrabold text-slate-800 mt-1">{{ $todayCheckIns }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">{{ $todayOnTime }} tepat</span>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-600 bg-red-50 px-1.5 py-0.5 rounded-md">{{ $todayLate }} telat</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-emerald-50 rounded-full"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Weekly Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Statistik Absensi 7 Hari Terakhir</h3>
            <div class="space-y-3">
                @foreach($weeklyData as $day)
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 w-12 shrink-0">{{ $day['date'] }}</span>
                    <div class="flex-1 flex items-center gap-1 h-7">
                        @if($day['on_time'] > 0)
                        <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-lg flex items-center justify-center text-[10px] font-bold text-white px-2 transition-all duration-500"
                             style="width: {{ max(($day['on_time'] / max($day['on_time'] + $day['late'], 1)) * 100, 15) }}%">
                            {{ $day['on_time'] }}
                        </div>
                        @endif
                        @if($day['late'] > 0)
                        <div class="h-full bg-gradient-to-r from-red-400 to-red-500 rounded-lg flex items-center justify-center text-[10px] font-bold text-white px-2 transition-all duration-500"
                             style="width: {{ max(($day['late'] / max($day['on_time'] + $day['late'], 1)) * 100, 15) }}%">
                            {{ $day['late'] }}
                        </div>
                        @endif
                        @if($day['on_time'] == 0 && $day['late'] == 0)
                        <div class="h-full bg-slate-100 rounded-lg flex items-center justify-center text-[10px] text-slate-400 px-2 w-full">
                            Belum ada data
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-slate-100">
                <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-emerald-500"></div><span class="text-[10px] text-slate-500">Tepat Waktu</span></div>
                <div class="flex items-center gap-1.5"><div class="w-3 h-3 rounded bg-red-500"></div><span class="text-[10px] text-slate-500">Terlambat</span></div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-slate-800 mb-4">Aktivitas Terbaru</h3>
            <div class="space-y-3">
                @forelse($recentAttendances as $att)
                <div class="flex items-start gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors">
                    <div class="w-8 h-8 rounded-lg {{ $att->status === 'on_time' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600' }} flex items-center justify-center shrink-0 mt-0.5">
                        @if($att->type === 'check_in')
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $att->attendee_name }}</p>
                        <p class="text-[10px] text-slate-400">
                            {{ $att->type === 'check_in' ? 'Check-in' : 'Check-out' }} •
                            {{ \Carbon\Carbon::parse($att->checked_at)->format('H:i') }} •
                            {{ round($att->distance_meters) }}m
                        </p>
                    </div>
                    <span class="inline-flex text-[10px] font-semibold px-2 py-0.5 rounded-full {{ $att->status === 'on_time' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                        {{ $att->status === 'on_time' ? 'Tepat' : 'Telat' }}
                    </span>
                </div>
                @empty
                <div class="text-center py-8">
                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-xs text-slate-400">Belum ada aktivitas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
