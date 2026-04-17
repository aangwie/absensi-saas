@extends('layouts.admin')

@section('title', 'Pengaturan Jadwal Absensi')
@section('page-title', 'Jadwal Absensi')
@section('page-subtitle', 'Atur waktu absen masuk dan pulang per hari untuk setiap sekolah')

@section('content')
    <div class="space-y-6">

        {{-- School Selector --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Pengaturan Waktu Absensi</h3>
                        <p class="text-sm text-slate-500">Tentukan jendela waktu absen masuk & pulang per hari</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.attendance-schedule.index') }}" class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Sekolah</label>
                        <select name="school_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-5 py-2.5 bg-slate-700 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 transition">
                        Tampilkan
                    </button>
                </form>
            </div>
        </div>

        {{-- Success Alert --}}
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Schedule Table --}}
        @if($selectedSchoolId && count($schedules) > 0)
            <form method="POST" action="{{ route('admin.attendance-schedule.store') }}">
                @csrf
                <input type="hidden" name="school_id" value="{{ $selectedSchoolId }}">

                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gradient-to-r from-slate-50 to-slate-100 border-b border-slate-200">
                                    <th class="text-left px-5 py-4 font-bold text-slate-700 w-28">Hari</th>
                                    <th class="text-center px-3 py-4 font-bold text-slate-700" colspan="2">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="inline-block w-3 h-3 bg-green-400 rounded-full"></span>
                                            Absen Masuk
                                        </div>
                                    </th>
                                    <th class="text-center px-3 py-4 font-bold text-slate-700" colspan="2">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="inline-block w-3 h-3 bg-orange-400 rounded-full"></span>
                                            Absen Pulang
                                        </div>
                                    </th>
                                    <th class="text-center px-3 py-4 font-bold text-slate-700 w-20">Aktif</th>
                                </tr>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-5 py-2"></th>
                                    <th class="px-3 py-2 text-xs font-medium text-slate-500">Mulai</th>
                                    <th class="px-3 py-2 text-xs font-medium text-slate-500">Selesai</th>
                                    <th class="px-3 py-2 text-xs font-medium text-slate-500">Mulai</th>
                                    <th class="px-3 py-2 text-xs font-medium text-slate-500">Selesai</th>
                                    <th class="px-3 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @php $dayNames = \App\Models\AttendanceSchedule::$dayNames; @endphp
                                @foreach($schedules as $dayIndex => $schedule)
                                    <tr class="hover:bg-blue-50/30 transition {{ $dayIndex == 0 || $dayIndex == 6 ? 'bg-red-50/20' : '' }}">
                                        <td class="px-5 py-3">
                                            <input type="hidden" name="schedules[{{ $dayIndex }}][day_of_week]" value="{{ $dayIndex }}">
                                            <span class="font-semibold text-slate-800 {{ $dayIndex == 0 || $dayIndex == 6 ? 'text-red-600' : '' }}">
                                                {{ $dayNames[$dayIndex] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="time"
                                                name="schedules[{{ $dayIndex }}][check_in_start]"
                                                value="{{ $schedule->check_in_start ? substr($schedule->check_in_start, 0, 5) : '' }}"
                                                class="w-full px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-green-400 focus:border-green-400 transition">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="time"
                                                name="schedules[{{ $dayIndex }}][check_in_end]"
                                                value="{{ $schedule->check_in_end ? substr($schedule->check_in_end, 0, 5) : '' }}"
                                                class="w-full px-3 py-2 bg-green-50 border border-green-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-green-400 focus:border-green-400 transition">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="time"
                                                name="schedules[{{ $dayIndex }}][check_out_start]"
                                                value="{{ $schedule->check_out_start ? substr($schedule->check_out_start, 0, 5) : '' }}"
                                                class="w-full px-3 py-2 bg-orange-50 border border-orange-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="time"
                                                name="schedules[{{ $dayIndex }}][check_out_end]"
                                                value="{{ $schedule->check_out_end ? substr($schedule->check_out_end, 0, 5) : '' }}"
                                                class="w-full px-3 py-2 bg-orange-50 border border-orange-200 rounded-lg text-sm text-center focus:ring-2 focus:ring-orange-400 focus:border-orange-400 transition">
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="hidden" name="schedules[{{ $dayIndex }}][is_active]" value="0">
                                                <input type="checkbox"
                                                    name="schedules[{{ $dayIndex }}][is_active]"
                                                    value="1"
                                                    {{ $schedule->is_active ? 'checked' : '' }}
                                                    class="sr-only peer">
                                                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-500"></div>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <p class="text-xs text-slate-500">
                            <svg class="w-4 h-4 inline text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Jika waktu tidak diisi, absensi tidak akan dibatasi waktu untuk hari tersebut.
                        </p>
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            💾 Simpan Jadwal
                        </button>
                    </div>
                </div>
            </form>
        @endif

    </div>
@endsection
