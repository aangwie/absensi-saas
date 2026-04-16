@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page-title', 'Pengaturan Sistem')
@section('page-subtitle', 'Kelola update, cache, dan migrasi database')

@section('content')
    <div class="space-y-6">

        {{-- Update dari GitHub --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 flex items-center justify-center shadow-lg shadow-slate-500/20">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Update dari GitHub</h3>
                        <p class="text-xs text-slate-500">Tarik perubahan terbaru dari repository GitHub</p>
                    </div>
                </div>
            </div>

            {{-- GitHub Token Form --}}
            <div class="px-6 py-5 bg-slate-50/50 border-b border-slate-100">
                <label class="block text-sm font-semibold text-slate-700 mb-2">Konfigurasi GitHub Token</label>
                <div class="flex flex-col gap-3 max-w-2xl">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <input type="password" id="github-token-input" value="{{ $githubToken }}"
                            class="w-full pl-10 pr-12 py-2.5 rounded-xl border border-slate-300 text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow"
                            placeholder="ghp_xxxxxxxxxxxxxxxxxxxx">
                        <button type="button" onclick="toggleTokenVisibility()"
                            class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100 transition-colors">
                            <svg id="eye-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-off-icon" class="w-4 h-4 hidden" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    <div class="flex justify-end">
                        <button onclick="saveToken()" id="btn-save-token"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Simpan Token</span>
                        </button>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2.5 flex items-start gap-1.5">
                    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Token disimpan aman di database. Dapatkan token di <a href="https://github.com/settings/tokens"
                            target="_blank"
                            class="text-indigo-600 hover:text-indigo-700 font-semibold hover:underline">GitHub → Settings →
                            Developer settings → Personal access tokens</a>.</span>
                </p>
            </div>

            {{-- Terminal Output --}}
            <div class="bg-slate-900 relative">
                <div class="flex items-center gap-2 px-4 py-2.5 bg-slate-800/80 border-b border-slate-700/50">
                    <div class="flex gap-1.5">
                        <div class="w-3 h-3 rounded-full bg-red-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500/80"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500/80"></div>
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono ml-2">terminal — git pull</span>
                    <div class="flex-1"></div>
                    <button onclick="clearTerminal('git-terminal')"
                        class="text-slate-500 hover:text-slate-300 transition-colors" title="Clear">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
                <div id="git-terminal" class="p-4 h-64 overflow-y-auto font-mono text-sm leading-relaxed scroll-smooth">
                    <div class="text-emerald-400">$ Siap menerima perintah...</div>
                    <div class="text-slate-500 text-xs mt-1">Klik tombol "Pull dari GitHub" untuk memulai update.</div>
                </div>
            </div>

            <div
                class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-xs font-medium text-slate-500 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Log terminal akan menampilkan status operasi secara real-time.
                </p>
                <button onclick="runGitPull()" id="btn-git-pull"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-lg shadow-indigo-500/20 transition-all duration-200 active:scale-[0.98]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Mulai Pull dari GitHub</span>
                </button>
            </div>
        </div>

        {{-- Maintenance Actions --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            {{-- Clear Cache --}}
            <div
                class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Clear Cache</h3>
                            <p class="text-xs text-slate-500">Bersihkan cache, view & route</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mb-4">Membersihkan semua cache aplikasi termasuk view cache dan route
                        cache. Berguna setelah melakukan update.</p>
                    <button onclick="runAction('clear-cache', this)" id="btn-clear-cache"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-cyan-600 text-white text-sm font-semibold rounded-xl hover:from-blue-600 hover:to-cyan-700 shadow-lg shadow-blue-500/20 hover:shadow-blue-500/30 transition-all duration-200 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Clear Cache</span>
                    </button>
                </div>
                <div id="result-clear-cache" class="hidden border-t border-slate-100 p-4 bg-slate-50/50">
                    <pre class="text-xs font-mono text-slate-600 whitespace-pre-wrap"></pre>
                </div>
            </div>

            {{-- Clear Config --}}
            <div
                class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Clear Config</h3>
                            <p class="text-xs text-slate-500">Bersihkan config cache</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mb-4">Membersihkan config cache yang tersimpan. Wajib dijalankan jika
                        ada perubahan pada file .env atau config.</p>
                    <button onclick="runAction('clear-config', this)" id="btn-clear-config"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-sm font-semibold rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-500/20 hover:shadow-amber-500/30 transition-all duration-200 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Clear Config</span>
                    </button>
                </div>
                <div id="result-clear-config" class="hidden border-t border-slate-100 p-4 bg-slate-50/50">
                    <pre class="text-xs font-mono text-slate-600 whitespace-pre-wrap"></pre>
                </div>
            </div>

            {{-- Migrate Database --}}
            <div
                class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-800">Migrate Database</h3>
                            <p class="text-xs text-slate-500">Jalankan migrasi database</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mb-4">Menjalankan migrasi database untuk menerapkan perubahan skema
                        terbaru. Pastikan backup data sebelum menjalankan.</p>
                    <button onclick="confirmMigrate()" id="btn-migrate"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-700 shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/30 transition-all duration-200 active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                        <span>Jalankan Migrate</span>
                    </button>
                </div>
                <div id="result-migrate" class="hidden border-t border-slate-100 p-4 bg-slate-50/50">
                    <pre class="text-xs font-mono text-slate-600 whitespace-pre-wrap"></pre>
                </div>
            </div>

        </div>

        {{-- System Info --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
            <h3 class="text-sm font-bold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Sistem
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Laravel</p>
                    <p class="text-sm font-bold text-slate-700 mt-1">{{ app()->version() }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">PHP</p>
                    <p class="text-sm font-bold text-slate-700 mt-1">{{ phpversion() }}</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Timezone</p>
                    <p class="text-sm font-bold text-slate-700 mt-1">{{ config('app.timezone') }} (GMT+7)</p>
                </div>
                <div class="bg-slate-50 rounded-xl p-3">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Server Time</p>
                    <p class="text-sm font-bold text-slate-700 mt-1">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Toggle token visibility
        function toggleTokenVisibility() {
            const input = document.getElementById('github-token-input');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeOffIcon = document.getElementById('eye-off-icon');
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }

        // Save GitHub token
        async function saveToken() {
            const btn = document.getElementById('btn-save-token');
            const token = document.getElementById('github-token-input').value;

            if (!token || token.length < 5) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Token tidak valid.', customClass: { popup: 'rounded-2xl' } });
                return;
            }

            setButtonLoading(btn, true);
            try {
                const response = await fetch('{{ route("admin.settings.update-token") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ github_token: token })
                });
                const data = await response.json();
                if (data.success) {
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                }
            } catch (error) {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message, customClass: { popup: 'rounded-2xl' } });
            }
            setButtonLoading(btn, false);
        }

        // Terminal helper
        function terminalAppend(terminalId, text, color = 'text-slate-300') {
            const terminal = document.getElementById(terminalId);
            const line = document.createElement('div');
            line.className = color + ' whitespace-pre-wrap break-all';
            line.textContent = text;
            terminal.appendChild(line);
            terminal.scrollTop = terminal.scrollHeight;
        }

        function clearTerminal(terminalId) {
            const terminal = document.getElementById(terminalId);
            terminal.innerHTML = '<div class="text-emerald-400">$ Terminal dibersihkan.</div>';
        }

        function setButtonLoading(btn, loading) {
            if (loading) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                const spinner = document.createElement('div');
                spinner.className = 'btn-spinner w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin';
                const icon = btn.querySelector('svg');
                if (icon) icon.classList.add('hidden');
                btn.insertBefore(spinner, btn.firstChild);
            } else {
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                const spinner = btn.querySelector('.btn-spinner');
                if (spinner) spinner.remove();
                const icon = btn.querySelector('svg');
                if (icon) icon.classList.remove('hidden');
            }
        }

        // Git Pull
        async function runGitPull() {
            const btn = document.getElementById('btn-git-pull');
            const terminal = document.getElementById('git-terminal');
            setButtonLoading(btn, true);
            terminal.innerHTML = '';
            terminalAppend('git-terminal', '$ git pull origin ...', 'text-emerald-400');
            terminalAppend('git-terminal', 'Menghubungkan ke GitHub...', 'text-yellow-400');
            terminalAppend('git-terminal', '', 'text-slate-500');

            try {
                const response = await fetch('{{ route("admin.settings.git-pull") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await response.json();
                const lines = data.output.split('\n');
                lines.forEach(line => {
                    let color = 'text-slate-300';
                    if (line.startsWith('$')) color = 'text-emerald-400';
                    else if (line.startsWith('📋')) color = 'text-cyan-400';
                    else if (line.startsWith('✅')) color = 'text-emerald-400';
                    else if (line.startsWith('Branch:')) color = 'text-indigo-400';
                    else if (line.startsWith('Error')) color = 'text-red-400';
                    else if (line.includes('Already up to date')) color = 'text-emerald-400';
                    else if (line.includes('CONFLICT')) color = 'text-red-400';
                    else if (line.includes('Updating') || line.includes('Fast-forward')) color = 'text-yellow-400';
                    terminalAppend('git-terminal', line, color);
                });
                terminalAppend('git-terminal', '', 'text-slate-500');
                terminalAppend('git-terminal', data.success ? '✅ Proses selesai.' : '❌ Proses gagal.', data.success ? 'text-emerald-400 font-semibold' : 'text-red-400 font-semibold');
            } catch (error) {
                terminalAppend('git-terminal', '❌ Error: ' + error.message, 'text-red-400');
            }
            setButtonLoading(btn, false);
        }

        // Run maintenance action
        async function runAction(action, btnElement) {
            const resultDiv = document.getElementById('result-' + action);
            const resultPre = resultDiv.querySelector('pre');
            setButtonLoading(btnElement, true);
            resultDiv.classList.remove('hidden');
            resultPre.textContent = 'Memproses...';

            try {
                const response = await fetch('{{ url("admin/settings") }}/' + action, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await response.json();
                resultPre.textContent = data.output || data.message;
                if (data.success) {
                    resultDiv.className = resultDiv.className.replace('bg-slate-50/50', '');
                    resultDiv.classList.add('bg-emerald-50/50');
                    resultPre.className = 'text-xs font-mono text-emerald-700 whitespace-pre-wrap';
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                } else {
                    resultDiv.classList.add('bg-red-50/50');
                    resultPre.className = 'text-xs font-mono text-red-700 whitespace-pre-wrap';
                }
            } catch (error) {
                resultPre.textContent = 'Error: ' + error.message;
                resultPre.className = 'text-xs font-mono text-red-700 whitespace-pre-wrap';
            }
            setButtonLoading(btnElement, false);
        }

        // Confirm migrate
        function confirmMigrate() {
            Swal.fire({
                title: 'Jalankan Migrasi?',
                html: 'Pastikan Anda sudah melakukan <strong>backup database</strong> sebelum menjalankan migrasi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#64748B',
                confirmButtonText: 'Ya, Jalankan!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6', cancelButton: 'rounded-xl px-6' }
            }).then((result) => { if (result.isConfirmed) runMigrate(); });
        }

        async function runMigrate() {
            const btn = document.getElementById('btn-migrate');
            const resultDiv = document.getElementById('result-migrate');
            const resultPre = resultDiv.querySelector('pre');
            setButtonLoading(btn, true);
            resultDiv.classList.remove('hidden');
            resultPre.textContent = 'Menjalankan migrasi database...';

            try {
                const response = await fetch('{{ route("admin.settings.migrate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await response.json();
                resultPre.textContent = data.output || data.message;
                if (data.success) {
                    resultDiv.className = resultDiv.className.replace('bg-slate-50/50', '');
                    resultDiv.classList.add('bg-emerald-50/50');
                    resultPre.className = 'text-xs font-mono text-emerald-700 whitespace-pre-wrap';
                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, timer: 2000, showConfirmButton: false, customClass: { popup: 'rounded-2xl' } });
                } else {
                    resultDiv.classList.add('bg-red-50/50');
                    resultPre.className = 'text-xs font-mono text-red-700 whitespace-pre-wrap';
                    Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Migrasi gagal. Periksa output untuk detail.', customClass: { popup: 'rounded-2xl' } });
                }
            } catch (error) {
                resultPre.textContent = 'Error: ' + error.message;
                resultPre.className = 'text-xs font-mono text-red-700 whitespace-pre-wrap';
            }
            setButtonLoading(btn, false);
        }
    </script>
@endpush