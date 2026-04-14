@extends('layouts.mobile')
@section('title', 'Login — Absensi Mobile')

@section('styles')
<style>
    .login-wrapper {
        min-height: 100vh;
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 24px 20px;
    }

    .login-header {
        text-align: center;
        margin-bottom: 36px;
    }

    .login-logo {
        width: 72px;
        height: 72px;
        border-radius: 22px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6, #a78bfa);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 8px 32px rgba(99, 102, 241, 0.3);
    }

    .login-logo svg {
        width: 36px;
        height: 36px;
        color: white;
    }

    .login-header h1 {
        font-size: 26px;
        font-weight: 800;
        background: linear-gradient(135deg, #f1f5f9, #94a3b8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .login-header p {
        font-size: 14px;
        color: var(--text-muted);
        margin-top: 6px;
    }

    .login-card {
        padding: 28px 24px;
    }

    /* Tab switch */
    .tab-switch {
        display: flex;
        background: var(--bg-input);
        border-radius: 12px;
        padding: 4px;
        margin-bottom: 28px;
        border: 1px solid var(--border);
    }

    .tab-switch button {
        flex: 1;
        padding: 12px 16px;
        border: none;
        border-radius: 10px;
        font-family: inherit;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        background: transparent;
        color: var(--text-muted);
    }

    .tab-switch button.active {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 4px 16px var(--primary-glow);
    }

    .tab-switch button:not(.active):hover {
        color: var(--text-secondary);
    }

    /* Password toggle */
    .password-wrapper {
        position: relative;
    }
    .password-wrapper input { padding-right: 48px; }
    .password-toggle {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--text-muted);
        cursor: pointer;
        padding: 4px;
    }
    .password-toggle:hover { color: var(--text-secondary); }

    .login-footer {
        text-align: center;
        margin-top: 24px;
    }
    .login-footer p {
        font-size: 12px;
        color: var(--text-muted);
    }

    .school-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        background: var(--glass);
        border: 1px solid var(--border);
        border-radius: 100px;
        font-size: 12px;
        color: var(--text-secondary);
        margin-top: 12px;
    }
</style>
@endsection

@section('content')
<div class="login-wrapper">
    <div class="container">
        {{-- Header --}}
        <div class="login-header animate-in">
            <div class="login-logo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h1>Absensi Digital</h1>
            <p>Login untuk melakukan absensi</p>
        </div>

        {{-- Login Card --}}
        <div class="glass-card login-card animate-in delay-1">
            {{-- Error Messages --}}
            @if($errors->any())
                <div style="padding: 12px 16px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); border-radius: 12px; margin-bottom: 20px; font-size: 13px; color: #fca5a5;">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Tab Switch --}}
            <div class="tab-switch" id="tabSwitch">
                <button type="button" class="active" data-tab="student" onclick="switchTab('student')">
                    👨‍🎓 Siswa
                </button>
                <button type="button" data-tab="teacher" onclick="switchTab('teacher')">
                    👨‍🏫 Guru
                </button>
            </div>

            {{-- Login Form --}}
            <form method="POST" action="{{ route('mobile.login.submit') }}" id="loginForm">
                @csrf
                <input type="hidden" name="login_type" id="loginType" value="{{ old('login_type', 'student') }}">

                <div class="input-group">
                    <label id="identifierLabel">NISN</label>
                    <input type="text" name="identifier" id="identifierInput"
                           value="{{ old('identifier') }}"
                           placeholder="Masukkan NISN..."
                           autocomplete="off" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="passwordInput"
                               placeholder="Masukkan password..."
                               autocomplete="current-password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="eyeIcon">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg mt-3" id="loginBtn">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Masuk
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="login-footer animate-in delay-2">
            <p>Absensi Multi-SaaS Platform</p>
            <div class="school-badge">
                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Koneksi Aman
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Tab switching
    function switchTab(type) {
        document.getElementById('loginType').value = type;
        document.querySelectorAll('.tab-switch button').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === type);
        });

        const label = document.getElementById('identifierLabel');
        const input = document.getElementById('identifierInput');

        if (type === 'student') {
            label.textContent = 'NISN';
            input.placeholder = 'Masukkan NISN...';
        } else {
            label.textContent = 'NIP';
            input.placeholder = 'Masukkan NIP...';
        }
    }

    // Password toggle
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');

        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }

    // Restore tab on validation error
    @if(old('login_type') === 'teacher')
        switchTab('teacher');
    @endif

    // Form loading state
    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner"></div> Memproses...';
    });
</script>
@endsection
