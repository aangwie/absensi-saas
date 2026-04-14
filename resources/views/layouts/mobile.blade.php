<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e1b4b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="description" content="Absensi Digital — Check-in dan Check-out via browser mobile">
    <title>@yield('title', 'Absensi Mobile')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --primary-glow: rgba(99, 102, 241, 0.3);
            --success: #10b981;
            --success-dark: #059669;
            --success-glow: rgba(16, 185, 129, 0.3);
            --warning: #f59e0b;
            --warning-dark: #d97706;
            --danger: #ef4444;
            --danger-dark: #dc2626;
            --danger-glow: rgba(239, 68, 68, 0.3);
            --bg-dark: #0f0e1a;
            --bg-card: #1a1a2e;
            --bg-card-alt: #16213e;
            --bg-input: #1e1e36;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: rgba(255, 255, 255, 0.08);
            --border-light: rgba(255, 255, 255, 0.12);
            --glass: rgba(255, 255, 255, 0.05);
            --glass-strong: rgba(255, 255, 255, 0.1);
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            background-image:
                radial-gradient(ellipse at 20% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 100%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            color: var(--text-primary);
            min-height: 100vh;
            min-height: 100dvh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            padding-top: var(--safe-top);
            padding-bottom: var(--safe-bottom);
        }

        .container {
            max-width: 480px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Glass card */
        .glass-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        /* Toast notification */
        .toast {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            z-index: 9999;
            padding: 14px 24px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            max-width: 90vw;
            text-align: center;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
        }
        .toast.show { transform: translateX(-50%) translateY(0); }
        .toast.success { background: var(--success); color: white; }
        .toast.error { background: var(--danger); color: white; }
        .toast.warning { background: var(--warning); color: #1a1a2e; }

        /* Button base */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border: none;
            border-radius: 14px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            text-decoration: none;
        }
        .btn:active { transform: scale(0.96); }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 20px var(--primary-glow);
        }
        .btn-primary:hover:not(:disabled) {
            box-shadow: 0 8px 30px var(--primary-glow);
            transform: translateY(-1px);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), var(--success-dark));
            color: white;
            box-shadow: 0 4px 20px var(--success-glow);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), var(--danger-dark));
            color: white;
            box-shadow: 0 4px 20px var(--danger-glow);
        }

        .btn-ghost {
            background: var(--glass);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { background: var(--glass-strong); color: var(--text-primary); }

        .btn-lg {
            width: 100%;
            padding: 18px 28px;
            font-size: 16px;
            border-radius: 16px;
        }

        /* Input */
        .input-group { margin-bottom: 16px; }
        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--bg-input);
            color: var(--text-primary);
            font-family: inherit;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow);
        }
        .input-group input::placeholder { color: var(--text-muted); }
        .input-group .error-text {
            font-size: 12px;
            color: var(--danger);
            margin-top: 6px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px var(--primary-glow); }
            50% { box-shadow: 0 0 40px var(--primary-glow), 0 0 60px rgba(99, 102, 241, 0.15); }
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-in {
            animation: fadeInUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        .delay-5 { animation-delay: 0.5s; }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* Utility */
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 12px; }
        .mt-4 { margin-top: 16px; }
        .mb-3 { margin-bottom: 12px; }
        .mb-4 { margin-bottom: 16px; }
    </style>
    @yield('styles')
</head>
<body>
    @yield('content')

    <script>
        // Toast notification system
        function showToast(message, type = 'success', duration = 3000) {
            const existing = document.querySelector('.toast');
            if (existing) existing.remove();

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.textContent = message;
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.add('show');
            });

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, duration);
        }

        // CSRF token for fetch
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    </script>
    @yield('scripts')
</body>
</html>
