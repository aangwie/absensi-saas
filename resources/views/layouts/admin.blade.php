<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Absensi Multi-SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full bg-slate-50 font-[Inter]" x-data="{ sidebarOpen: true, mobileSidebar: false }">

    <div class="flex h-full">
        {{-- Sidebar --}}
        @include('components.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

            {{-- Top Navbar --}}
            @include('components.navbar')

            {{-- Page Content --}}
            <main class="flex-1 p-4 lg:p-6">
                {{-- Flash Messages --}}
                @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:leave="transition ease-in duration-300"
                     class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- Mobile sidebar overlay --}}
    <div x-show="mobileSidebar" x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-300"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden" @click="mobileSidebar = false">
    </div>

    {{-- SweetAlert2 Delete Confirmation --}}
    <script>
    function confirmDelete(formId, itemName) {
        Swal.fire({
            title: 'Hapus Data?',
            html: `Anda yakin ingin menghapus <strong>${itemName}</strong>?<br>Data yang dihapus tidak dapat dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#64748B',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl px-6',
                cancelButton: 'rounded-xl px-6',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
    </script>

    @stack('scripts')
</body>
</html>
