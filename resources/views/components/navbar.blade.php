{{-- Top Navbar --}}
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-slate-200/60 shadow-sm">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        {{-- Left side --}}
        <div class="flex items-center gap-3">
            {{-- Mobile menu toggle --}}
            <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden p-2 rounded-xl text-slate-500 hover:bg-slate-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <div>
                <h2 class="text-lg font-bold text-slate-800">@yield('page-title', 'Dashboard')</h2>
                <p class="text-xs text-slate-500">@yield('page-subtitle', auth()->user()->school?->name ?? 'Super Administrator')</p>
            </div>
        </div>

        {{-- Right side --}}
        <div class="flex items-center gap-3">
            {{-- Date --}}
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-xs font-medium">{{ now()->translatedFormat('l, d M Y') }}</span>
            </div>

            {{-- User dropdown --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center gap-2 p-1.5 pr-3 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-slate-700 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500">{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 rounded-xl bg-white shadow-xl border border-slate-200 py-1 z-50">
                    <div class="px-3 py-2 border-b border-slate-100">
                        <p class="text-xs text-slate-500">Login sebagai</p>
                        <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
