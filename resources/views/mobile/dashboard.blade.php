@extends('layouts.mobile')
@section('title', 'Dashboard — Absensi Mobile')

@section('styles')
<style>
    .dashboard-wrapper {
        padding: 20px 0;
        padding-bottom: 100px;
    }

    /* Top bar */
    .top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-avatar {
        width: 46px; height: 46px;
        border-radius: 14px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; font-weight: 800; color: white;
        box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
    }
    .user-name { font-size: 16px; font-weight: 700; color: var(--text-primary); }
    .user-role { font-size: 11px; color: var(--text-muted); margin-top: 2px; }
    .logout-btn {
        width: 40px; height: 40px;
        border-radius: 12px;
        background: var(--glass);
        border: 1px solid var(--border);
        color: var(--text-muted);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .logout-btn:hover { background: rgba(239,68,68,0.15); color: var(--danger); border-color: rgba(239,68,68,0.3); }

    /* Status cards */
    .status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        padding: 0 20px;
        margin-bottom: 20px;
    }
    .status-card {
        padding: 16px;
        border-radius: 16px;
        background: var(--bg-card);
        border: 1px solid var(--border);
    }
    .status-card .label {
        font-size: 11px; color: var(--text-muted);
        text-transform: uppercase; letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    .status-card .value {
        font-size: 22px; font-weight: 800;
    }
    .status-card .sub {
        font-size: 11px; color: var(--text-muted); margin-top: 4px;
    }
    .status-card.checked-in .value { color: var(--success); }
    .status-card.not-checked .value { color: var(--text-muted); }

    /* GPS Status Widget */
    .gps-status {
        margin: 0 20px 20px;
        padding: 16px 20px;
        border-radius: 16px;
        background: var(--bg-card);
        border: 1px solid var(--border);
    }
    .gps-status-header {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 10px;
    }
    .gps-status-header .title {
        font-size: 13px; font-weight: 600; color: var(--text-secondary);
        display: flex; align-items: center; gap: 8px;
    }
    .gps-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: var(--text-muted);
    }
    .gps-dot.active { background: var(--success); box-shadow: 0 0 8px var(--success-glow); }
    .gps-dot.error { background: var(--danger); box-shadow: 0 0 8px var(--danger-glow); }
    .gps-dot.checking {
        background: var(--warning);
        animation: pulse-glow 1.5s infinite;
    }
    .gps-info { font-size: 12px; color: var(--text-muted); line-height: 1.5; }
    .gps-info span { color: var(--text-secondary); font-weight: 500; }

    /* Mock Alert */
    .mock-alert {
        margin: 0 20px 20px;
        padding: 16px 20px;
        border-radius: 16px;
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        display: none;
    }
    .mock-alert.show { display: block; }
    .mock-alert .alert-title {
        font-size: 14px; font-weight: 700; color: #fca5a5;
        display: flex; align-items: center; gap: 8px;
        margin-bottom: 8px;
    }
    .mock-alert .alert-body {
        font-size: 12px; color: #fca5a5; line-height: 1.6; opacity: 0.9;
    }
    .mock-alert .alert-reasons {
        margin-top: 8px; padding-top: 8px;
        border-top: 1px solid rgba(239,68,68,0.2);
    }
    .mock-alert .alert-reasons li {
        font-size: 11px; color: #fca5a5; opacity: 0.8;
        margin-bottom: 4px; list-style: none;
        padding-left: 16px; position: relative;
    }
    .mock-alert .alert-reasons li::before {
        content: '⚠'; position: absolute; left: 0;
    }

    /* Action buttons */
    .action-area {
        padding: 0 20px;
        margin-bottom: 24px;
    }
    .action-btn {
        width: 100%;
        padding: 22px 24px;
        border: none;
        border-radius: 20px;
        font-family: inherit;
        font-size: 17px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        position: relative;
        overflow: hidden;
    }
    .action-btn:active { transform: scale(0.97); }
    .action-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    .action-btn.check-in {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 8px 32px rgba(16, 185, 129, 0.3);
    }
    .action-btn.check-in:hover:not(:disabled) {
        box-shadow: 0 12px 40px rgba(16, 185, 129, 0.4);
        transform: translateY(-2px);
    }

    .action-btn.check-out {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        box-shadow: 0 8px 32px rgba(245, 158, 11, 0.3);
    }
    .action-btn.check-out:hover:not(:disabled) {
        box-shadow: 0 12px 40px rgba(245, 158, 11, 0.4);
        transform: translateY(-2px);
    }

    .action-btn.completed {
        background: var(--bg-card);
        color: var(--text-muted);
        border: 1px solid var(--border);
        box-shadow: none;
        cursor: default;
    }

    .action-btn .shimmer {
        position: absolute;
        top: 0; left: -100%; width: 100%; height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        to { left: 100%; }
    }

    .action-spacer { height: 12px; }

    /* Detection status text */
    .detection-status {
        text-align: center;
        font-size: 12px;
        color: var(--text-muted);
        margin-top: 12px;
        min-height: 18px;
    }

    /* History section */
    .section-title {
        font-size: 15px; font-weight: 700; color: var(--text-primary);
        padding: 0 20px;
        margin-bottom: 12px;
    }
    .history-list { padding: 0 20px; }
    .history-item {
        display: flex; align-items: center; gap: 14px;
        padding: 14px 16px;
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 14px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .history-icon {
        width: 40px; height: 40px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    }
    .history-icon.in { background: rgba(16, 185, 129, 0.15); }
    .history-icon.out { background: rgba(245, 158, 11, 0.15); }
    .history-details { flex: 1; min-width: 0; }
    .history-type { font-size: 13px; font-weight: 600; color: var(--text-primary); }
    .history-location { font-size: 11px; color: var(--text-muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .history-meta { text-align: right; flex-shrink: 0; }
    .history-time { font-size: 13px; font-weight: 700; color: var(--text-secondary); font-variant-numeric: tabular-nums; }
    .history-date { font-size: 10px; color: var(--text-muted); margin-top: 2px; }
    .history-badge {
        display: inline-block; padding: 2px 8px;
        border-radius: 6px; font-size: 10px; font-weight: 700;
        margin-top: 4px;
    }
    .badge-on-time { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .badge-late { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .badge-mock { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }

    .empty-state {
        text-align: center; padding: 40px 20px;
        color: var(--text-muted); font-size: 14px;
    }
    .empty-state svg { margin-bottom: 12px; opacity: 0.3; }

    /* Loading overlay */
    .loading-overlay {
        position: fixed; inset: 0;
        background: rgba(15, 14, 26, 0.85);
        backdrop-filter: blur(8px);
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        z-index: 9999;
        opacity: 0; pointer-events: none;
        transition: opacity 0.3s ease;
    }
    .loading-overlay.show { opacity: 1; pointer-events: all; }
    .loading-spinner {
        width: 48px; height: 48px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-bottom: 16px;
    }
    .loading-text {
        font-size: 14px; color: var(--text-secondary); font-weight: 500;
    }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper">
    {{-- Top Bar --}}
    <div class="top-bar animate-in">
        <div class="user-info">
            <div class="user-avatar">{{ substr(session('mobile_user_name', 'U'), 0, 1) }}</div>
            <div>
                <div class="user-name">{{ session('mobile_user_name', 'User') }}</div>
                <div class="user-role">
                    {{ session('mobile_user_type') === 'student' ? 'Siswa' : 'Guru' }}
                    • {{ session('mobile_school_name') }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('mobile.logout') }}" id="logoutForm">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- Status Cards --}}
    <div class="status-grid animate-in delay-1">
        <div class="status-card {{ $checkIn ? 'checked-in' : 'not-checked' }}">
            <div class="label">Check-In</div>
            @if($checkIn)
                <div class="value">{{ \Carbon\Carbon::parse($checkIn->checked_at)->format('H:i') }}</div>
                <div class="sub">{{ $checkIn->status === 'on_time' ? '✅ Tepat Waktu' : '⚠️ Terlambat' }}</div>
            @else
                <div class="value">--:--</div>
                <div class="sub">Belum check-in</div>
            @endif
        </div>
        <div class="status-card {{ $checkOut ? 'checked-in' : 'not-checked' }}">
            <div class="label">Check-Out</div>
            @if($checkOut)
                <div class="value">{{ \Carbon\Carbon::parse($checkOut->checked_at)->format('H:i') }}</div>
                <div class="sub">✅ Selesai</div>
            @else
                <div class="value">--:--</div>
                <div class="sub">Belum check-out</div>
            @endif
        </div>
    </div>

    {{-- GPS Status --}}
    <div class="gps-status animate-in delay-2" id="gpsStatus">
        <div class="gps-status-header">
            <div class="title">
                <div class="gps-dot" id="gpsDot"></div>
                <span id="gpsTitle">Status GPS</span>
            </div>
            <button class="btn btn-ghost" style="padding:6px 12px;font-size:11px;border-radius:8px;" onclick="refreshGPS()">
                Refresh
            </button>
        </div>
        <div class="gps-info" id="gpsInfo">
            Tekan tombol di bawah untuk memulai pengecekan lokasi.
        </div>
    </div>

    {{-- Mock Location Alert --}}
    <div class="mock-alert" id="mockAlert">
        <div class="alert-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            Fake GPS Terdeteksi!
        </div>
        <div class="alert-body">
            Sistem mendeteksi bahwa Anda menggunakan lokasi palsu (mock location). Absensi tidak dapat dilakukan.
        </div>
        <ul class="alert-reasons" id="mockReasons"></ul>
    </div>

    {{-- Action Buttons --}}
    <div class="action-area animate-in delay-3">
        @if(!$checkIn)
            {{-- Check-In Button --}}
            <button class="action-btn check-in" id="checkInBtn" onclick="doAttendance('check-in')">
                <div class="shimmer"></div>
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Check-In Sekarang
            </button>
        @elseif(!$checkOut)
            {{-- Check-Out Button --}}
            <button class="action-btn check-out" id="checkOutBtn" onclick="doAttendance('check-out')">
                <div class="shimmer"></div>
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Check-Out Sekarang
            </button>
        @else
            {{-- Both done --}}
            <button class="action-btn completed" disabled>
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Absensi Hari Ini Selesai ✓
            </button>
        @endif
        <div class="detection-status" id="detectionStatus"></div>
    </div>

    {{-- History --}}
    <div class="animate-in delay-4">
        <div class="section-title">Riwayat Terakhir</div>
        <div class="history-list">
            @forelse($history as $item)
                <div class="history-item">
                    <div class="history-icon {{ $item->type === 'check_in' ? 'in' : 'out' }}">
                        {{ $item->type === 'check_in' ? '📥' : '📤' }}
                    </div>
                    <div class="history-details">
                        <div class="history-type">{{ $item->type === 'check_in' ? 'Check-In' : 'Check-Out' }}</div>
                        <div class="history-location">📍 {{ $item->location?->name ?? '-' }}</div>
                    </div>
                    <div class="history-meta">
                        <div class="history-time">{{ \Carbon\Carbon::parse($item->checked_at)->format('H:i') }}</div>
                        <div class="history-date">{{ \Carbon\Carbon::parse($item->checked_at)->format('d M') }}</div>
                        @if($item->type === 'check_in')
                            <div class="history-badge {{ $item->status === 'on_time' ? 'badge-on-time' : 'badge-late' }}">
                                {{ $item->status === 'on_time' ? 'Tepat' : 'Telat' }}
                            </div>
                        @endif
                        @if($item->is_mock_suspected)
                            <div class="history-badge badge-mock">⚠ Mock</div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p>Belum ada riwayat absensi</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Loading Overlay --}}
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
    <div class="loading-text" id="loadingText">Memproses absensi...</div>
</div>
@endsection

@section('scripts')
<script src="/js/mock-location-detector.js"></script>
<script>
    const API_BASE = '/api';
    const AUTH_TOKEN = @json(session('mobile_token'));
    let currentDetection = null;
    let isProcessing = false;

    // Initialize GPS status on load
    document.addEventListener('DOMContentLoaded', () => {
        checkGPSStatus();
    });

    // Check GPS availability
    async function checkGPSStatus() {
        const dot = document.getElementById('gpsDot');
        const title = document.getElementById('gpsTitle');
        const info = document.getElementById('gpsInfo');

        dot.className = 'gps-dot checking';
        title.textContent = 'Memeriksa GPS...';
        info.innerHTML = 'Sedang memeriksa ketersediaan dan keaslian GPS...';

        try {
            // Check permission first
            const detector = new MockLocationDetector({ sampleCount: 2, sampleInterval: 800 });

            detector.onStatusUpdate = (msg) => {
                info.innerHTML = msg;
            };

            const result = await detector.detect();
            currentDetection = result;

            if (result.isMockSuspected) {
                dot.className = 'gps-dot error';
                title.textContent = 'GPS PALSU TERDETEKSI';
                info.innerHTML = `<span style="color:#fca5a5;">⚠ Lokasi palsu (mock) terdeteksi!</span><br>Kepercayaan: ${(result.confidence * 100).toFixed(0)}%<br>Akurasi: ${result.location.accuracy?.toFixed(1) ?? 'N/A'}m`;

                // Show mock alert
                showMockAlert(result.reasons);

                // Disable action buttons
                disableActionButtons();
            } else {
                dot.className = 'gps-dot active';
                title.textContent = 'GPS Aktif & Valid';
                info.innerHTML = `Lokasi: <span>${result.location.latitude.toFixed(6)}, ${result.location.longitude.toFixed(6)}</span><br>Akurasi: <span>${result.location.accuracy?.toFixed(1) ?? 'N/A'}m</span> • ${result.samplesCollected} sampel terverifikasi`;

                hideMockAlert();
                enableActionButtons();
            }
        } catch (error) {
            dot.className = 'gps-dot error';
            title.textContent = 'GPS Error';
            info.innerHTML = `<span style="color:#fca5a5;">${error.message}</span>`;
            disableActionButtons();
        }
    }

    function refreshGPS() {
        currentDetection = null;
        checkGPSStatus();
    }

    function showMockAlert(reasons) {
        const alert = document.getElementById('mockAlert');
        const list = document.getElementById('mockReasons');
        list.innerHTML = '';

        reasons.forEach(r => {
            const li = document.createElement('li');
            li.textContent = r;
            list.appendChild(li);
        });

        alert.classList.add('show');
    }

    function hideMockAlert() {
        document.getElementById('mockAlert').classList.remove('show');
    }

    function disableActionButtons() {
        document.querySelectorAll('.action-btn:not(.completed)').forEach(btn => {
            btn.disabled = true;
        });
        document.getElementById('detectionStatus').innerHTML =
            '<span style="color: var(--danger);">❌ Absensi diblokir — fake GPS terdeteksi</span>';
    }

    function enableActionButtons() {
        document.querySelectorAll('.action-btn:not(.completed)').forEach(btn => {
            btn.disabled = false;
        });
        document.getElementById('detectionStatus').innerHTML =
            '<span style="color: var(--success);">✅ GPS terverifikasi — siap untuk absensi</span>';
    }

    // Perform attendance (check-in or check-out)
    async function doAttendance(type) {
        if (isProcessing) return;

        const actionBtn = document.querySelector('.action-btn:not(.completed)');
        if (!actionBtn || actionBtn.disabled) return;

        isProcessing = true;
        showLoading(`Memproses ${type === 'check-in' ? 'Check-In' : 'Check-Out'}...`);

        try {
            // Step 1: Get fresh location with mock detection
            updateLoading('Memverifikasi lokasi GPS...');
            const detector = new MockLocationDetector({ sampleCount: 3, sampleInterval: 800 });
            detector.onStatusUpdate = (msg) => updateLoading(msg);
            const detection = await detector.detect();

            // Step 2: Check for mock location
            if (detection.isMockSuspected) {
                hideLoading();
                showMockAlert(detection.reasons);
                disableActionButtons();
                showToast('Fake GPS terdeteksi! Absensi ditolak.', 'error', 5000);
                isProcessing = false;
                return;
            }

            // Step 3: Send to API
            updateLoading('Mengirim data absensi...');

            const response = await fetch(`${API_BASE}/attendance/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${AUTH_TOKEN}`,
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    latitude: detection.location.latitude,
                    longitude: detection.location.longitude,
                    accuracy: detection.location.accuracy,
                    is_mock_suspected: detection.isMockSuspected,
                    mock_reasons: detection.reasons.length > 0 ? detection.reasons.join('; ') : null,
                    device_id: getDeviceId(),
                }),
            });

            const data = await response.json();

            hideLoading();

            if (data.success) {
                showToast(data.message, 'success', 4000);
                // Reload page after a short delay
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showToast(data.message || 'Gagal melakukan absensi.', 'error', 5000);
            }
        } catch (error) {
            hideLoading();
            showToast(error.message || 'Terjadi kesalahan. Coba lagi.', 'error', 5000);
        }

        isProcessing = false;
    }

    // Loading overlay
    function showLoading(text) {
        document.getElementById('loadingText').textContent = text;
        document.getElementById('loadingOverlay').classList.add('show');
    }
    function updateLoading(text) {
        document.getElementById('loadingText').textContent = text;
    }
    function hideLoading() {
        document.getElementById('loadingOverlay').classList.remove('show');
    }

    // Simple device fingerprint
    function getDeviceId() {
        let deviceId = localStorage.getItem('absensi_device_id');
        if (!deviceId) {
            deviceId = 'web_' + Date.now().toString(36) + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('absensi_device_id', deviceId);
        }
        return deviceId;
    }
</script>
@endsection
