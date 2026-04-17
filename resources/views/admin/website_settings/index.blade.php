@extends('layouts.admin')

@section('title', 'Pengaturan Website')
@section('page-title', 'Pengaturan Website')
@section('page-subtitle', 'Ubah identitas dan logo website')

@section('content')
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden text-slate-800">
    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
        <h3 class="text-sm font-bold text-slate-800">Informasi Website</h3>
        <p class="text-xs text-slate-500">Sesuaikan nama platform dan logo.</p>
    </div>

    @if (session('success'))
    <div class="m-6 p-4 mb-0 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
        <span class="font-medium">Berhasil!</span> {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.website-settings.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
        @csrf

        {{-- Website Name --}}
        <div>
            <label for="website_name" class="block text-sm font-semibold text-slate-700 mb-2">Nama Website</label>
            <input type="text" id="website_name" name="website_name" value="{{ old('website_name', $websiteName) }}"
                class="w-full px-4 py-2.5 rounded-xl border {{ $errors->has('website_name') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-500' }} text-sm transition-shadow shadow-sm"
                placeholder="Misal: Absensi Multi-SaaS" required>
            @error('website_name')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        {{-- Website Logo --}}
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">Logo Website</label>
            
            <div class="flex items-center gap-6">
                <!-- Preview -->
                <div class="w-16 h-16 shrink-0 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden shadow-sm">
                    @if($websiteLogo)
                        <img src="{{ $websiteLogo }}" alt="Logo Perview" id="logo-preview" class="w-full h-full object-contain">
                    @else
                        <svg id="logo-placeholder" class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <img src="" alt="Logo Perview" id="logo-preview" class="w-full h-full object-contain hidden">
                    @endif
                </div>

                <div class="flex-1">
                    <input type="file" id="website_logo" name="website_logo" accept="image/*" class="hidden" onchange="previewImage(event)">
                    <label for="website_logo" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 cursor-pointer shadow-sm transition-colors">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Pilih Gambar
                    </label>
                    <p class="mt-2 text-xs text-slate-500">Format yang didukung: JPG, PNG, GIF. Maksimal ukuran file: 500Kb.</p>
                </div>
            </div>
            @error('website_logo')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg shadow-indigo-500/30 transition-all duration-200 active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Check size (500kb = 512000 bytes)
            if (file.size > 512000) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ukuran Terlalu Besar',
                    text: 'Ukuran logo tidak boleh lebih dari 500Kb.',
                    customClass: { popup: 'rounded-2xl' }
                });
                input.value = ''; // Reset input
                return;
            }

            const reader = new FileReader();
            
            reader.onload = function(e) {
                const preview = document.getElementById('logo-preview');
                const placeholder = document.getElementById('logo-placeholder');
                
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            }
            
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
