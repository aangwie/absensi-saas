{{-- Sidebar Component --}}
<aside class="fixed top-0 left-0 z-50 h-full transition-all duration-300 ease-in-out"
       :class="[
           sidebarOpen ? 'w-64' : 'w-20',
           mobileSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
       ]">
    <div class="h-full flex flex-col bg-gradient-to-b from-slate-900 via-slate-800 to-indigo-900 text-white shadow-2xl">

        {{-- Logo Header --}}
        <div class="flex items-center gap-3 px-4 h-16 border-b border-white/10">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shrink-0 shadow-lg shadow-indigo-500/30">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div x-show="sidebarOpen" x-transition class="overflow-hidden">
                <h1 class="font-bold text-sm leading-tight">Absensi</h1>
                <p class="text-[10px] text-indigo-300">Multi-SaaS Platform</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin">
            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.dashboard') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Dashboard</span>
            </a>

            {{-- Divider --}}
            <div class="pt-3 pb-1 px-3" x-show="sidebarOpen">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Master Data</p>
            </div>

            @if(auth()->user()->isSuperAdmin())
            {{-- Schools --}}
            <a href="{{ route('admin.schools.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.schools.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Sekolah</span>
            </a>
            @endif

            {{-- Locations --}}
            <a href="{{ route('admin.locations.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.locations.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Lokasi</span>
            </a>

            {{-- Students --}}
            <a href="{{ route('admin.students.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.students.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Siswa</span>
            </a>

            {{-- Teachers --}}
            <a href="{{ route('admin.teachers.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.teachers.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Guru</span>
            </a>

            {{-- Divider --}}
            <div class="pt-3 pb-1 px-3" x-show="sidebarOpen">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Absensi</p>
            </div>

            {{-- Attendances --}}
            <a href="{{ route('admin.attendances.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.attendances.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Data Absensi</span>
            </a>

            @if(auth()->user()->isAdmin())
            {{-- Divider --}}
            <div class="pt-3 pb-1 px-3" x-show="sidebarOpen">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Pengaturan</p>
            </div>

            {{-- Users --}}
            <a href="{{ route('admin.users.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.users.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Manajemen User</span>
            </a>
            @endif

            @if(auth()->user()->isSuperAdmin())
            {{-- Divider --}}
            <div class="pt-3 pb-1 px-3" x-show="sidebarOpen">
                <p class="text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Sistem</p>
            </div>

            {{-- Settings --}}
            <a href="{{ route('admin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200
                      {{ request()->routeIs('admin.settings.*') ? 'bg-white/15 text-white shadow-lg' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                <span x-show="sidebarOpen" x-transition class="text-sm font-medium">Pengaturan Sistem</span>
            </a>
            @endif
        </nav>

        {{-- Sidebar Toggle --}}
        <div class="p-3 border-t border-white/10">
            <button @click="sidebarOpen = !sidebarOpen"
                    class="hidden lg:flex w-full items-center justify-center gap-2 px-3 py-2 rounded-xl text-slate-400 hover:bg-white/10 hover:text-white transition-all duration-200">
                <svg x-show="sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                <svg x-show="!sidebarOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                <span x-show="sidebarOpen" class="text-xs font-medium">Tutup Sidebar</span>
            </button>
        </div>
    </div>
</aside>
