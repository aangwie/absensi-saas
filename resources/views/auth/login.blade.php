<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Absensi Multi-SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-[Inter]">

<div class="min-h-full flex" x-data="{ showPassword: false }">
    {{-- Left: Branding --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-700 to-slate-900">
        {{-- Decorative elements --}}
        <div class="absolute inset-0">
            <div class="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-96 h-96 bg-indigo-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-purple-400/10 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
        </div>

        {{-- Grid pattern --}}
        <div class="absolute inset-0 opacity-5">
            <div class="h-full w-full" style="background-image: radial-gradient(circle, white 1px, transparent 1px); background-size: 30px 30px;"></div>
        </div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col justify-center px-16">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-8 shadow-2xl">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <h1 class="text-5xl font-extrabold text-white leading-tight mb-4">
                Absensi<br>
                <span class="text-indigo-300">Multi-SaaS</span>
            </h1>
            <p class="text-lg text-indigo-200/80 max-w-md leading-relaxed">
                Sistem absensi digital berbasis GPS untuk multiple sekolah. Kelola kehadiran siswa dan guru dengan mudah dan akurat.
            </p>

            {{-- Features list --}}
            <div class="mt-10 space-y-3">
                <div class="flex items-center gap-3 text-indigo-200/70">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm">Validasi lokasi GPS dengan Haversine Formula</span>
                </div>
                <div class="flex items-center gap-3 text-indigo-200/70">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm">Multi-tenant untuk banyak sekolah</span>
                </div>
                <div class="flex items-center gap-3 text-indigo-200/70">
                    <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <span class="text-sm">Dashboard real-time & laporan lengkap</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Login Form --}}
    <div class="flex-1 flex items-center justify-center p-6 bg-slate-50">
        <div class="w-full max-w-md">
            {{-- Mobile Logo --}}
            <div class="lg:hidden flex items-center gap-3 mb-8">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Absensi Multi-SaaS</h1>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200/60 p-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-800">Selamat Datang 👋</h2>
                    <p class="text-sm text-slate-500 mt-1">Masuk ke panel administrasi</p>
                </div>

                @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
                    @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                                   placeholder="admin@sekolah.sch.id">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                   class="w-full pl-11 pr-12 py-3 rounded-xl border border-slate-300 text-slate-800 placeholder-slate-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm"
                                   placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold text-sm shadow-lg shadow-indigo-500/30 hover:shadow-xl hover:shadow-indigo-500/40 hover:from-indigo-700 hover:to-purple-700 transform hover:-translate-y-0.5 transition-all duration-200">
                        Masuk
                    </button>
                </form>
            </div>

            <p class="text-center text-xs text-slate-400 mt-6">
                &copy; {{ date('Y') }} Absensi Multi-SaaS. All rights reserved.
            </p>
        </div>
    </div>
</div>

</body>
</html>
