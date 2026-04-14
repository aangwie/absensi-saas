<?php
/**
 * Temporary setup script - DELETE AFTER SETUP
 * Run this via browser: http://localhost/absensi/public/setup.php
 */

set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = realpath(__DIR__ . '/..');

echo "<html><head><title>Setup Absensi</title><style>
body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; padding: 40px; max-width: 900px; margin: 0 auto; }
h1 { color: #818cf8; }
.step { background: #1e293b; border-radius: 12px; padding: 16px; margin: 12px 0; border-left: 4px solid #818cf8; }
.step.success { border-left-color: #10b981; }
.step.error { border-left-color: #ef4444; }
.step h3 { margin: 0 0 8px 0; font-size: 14px; }
pre { background: #0f172a; padding: 12px; border-radius: 8px; overflow-x: auto; font-size: 12px; white-space: pre-wrap; word-wrap: break-word; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; }
.badge.ok { background: #10b981; color: white; }
.badge.fail { background: #ef4444; color: white; }
.badge.skip { background: #f59e0b; color: white; }
</style></head><body>";

echo "<h1>🚀 Setup Aplikasi Absensi Multi-SaaS</h1>";
echo "<p>Base path: <code>{$basePath}</code></p>";

$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;

function runCmd($cmd, $cwd) {
    $desc = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];
    $env = null;
    $proc = proc_open($cmd, $desc, $pipes, $cwd, $env);
    if (!is_resource($proc)) {
        return ['output' => 'Failed to execute command', 'error' => true];
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($proc);
    return [
        'output' => $stdout . ($stderr ? "\nSTDERR: " . $stderr : ''),
        'error' => $exitCode !== 0,
        'exitCode' => $exitCode,
    ];
}

// Step 1: Composer require sanctum
echo "<div class='step'>";
echo "<h3>Step 1: composer require laravel/sanctum</h3>";
if ($step === 0 || $step === 1) {
    $result = runCmd('composer require laravel/sanctum --no-interaction 2>&1', $basePath);
    echo "<span class='badge " . ($result['error'] ? 'fail' : 'ok') . "'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
    echo "<pre>" . htmlspecialchars($result['output']) . "</pre>";
} else {
    echo "<span class='badge skip'>SKIP</span>";
}
echo "</div>";

// Step 2: Publish Sanctum config + migration
echo "<div class='step'>";
echo "<h3>Step 2: php artisan vendor:publish (Sanctum)</h3>";
if ($step === 0 || $step === 2) {
    $result = runCmd('php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --no-interaction 2>&1', $basePath);
    echo "<span class='badge " . ($result['error'] ? 'fail' : 'ok') . "'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
    echo "<pre>" . htmlspecialchars($result['output']) . "</pre>";
} else {
    echo "<span class='badge skip'>SKIP</span>";
}
echo "</div>";

// Step 3: NPM install
echo "<div class='step'>";
echo "<h3>Step 3: npm install alpinejs sweetalert2</h3>";
if ($step === 0 || $step === 3) {
    $result = runCmd('npm install alpinejs sweetalert2 2>&1', $basePath);
    echo "<span class='badge " . ($result['error'] ? 'fail' : 'ok') . "'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
    echo "<pre>" . htmlspecialchars($result['output']) . "</pre>";
} else {
    echo "<span class='badge skip'>SKIP</span>";
}
echo "</div>";

// Step 4: Migrate fresh + seed
echo "<div class='step'>";
echo "<h3>Step 4: php artisan migrate:fresh --seed</h3>";
if ($step === 0 || $step === 4) {
    $result = runCmd('php artisan migrate:fresh --seed --no-interaction 2>&1', $basePath);
    echo "<span class='badge " . ($result['error'] ? 'fail' : 'ok') . "'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
    echo "<pre>" . htmlspecialchars($result['output']) . "</pre>";
} else {
    echo "<span class='badge skip'>SKIP</span>";
}
echo "</div>";

// Step 5: Storage link
echo "<div class='step'>";
echo "<h3>Step 5: php artisan storage:link</h3>";
if ($step === 0 || $step === 5) {
    $result = runCmd('php artisan storage:link --no-interaction 2>&1', $basePath);
    echo "<span class='badge " . ($result['error'] ? 'fail' : 'ok') . "'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
    echo "<pre>" . htmlspecialchars($result['output']) . "</pre>";
} else {
    echo "<span class='badge skip'>SKIP</span>";
}
echo "</div>";

// Step 6: NPM build (instead of dev since we're in browser)
echo "<div class='step'>";
echo "<h3>Step 6: npm run build</h3>";
if ($step === 0 || $step === 6) {
    $result = runCmd('npm run build 2>&1', $basePath);
    echo "<span class='badge " . ($result['error'] ? 'fail' : 'ok') . "'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
    echo "<pre>" . htmlspecialchars($result['output']) . "</pre>";
} else {
    echo "<span class='badge skip'>SKIP</span>";
}
echo "</div>";

echo "<hr style='border-color: #334155; margin: 24px 0;'>";
echo "<h2 style='color: #10b981;'>✅ Setup Selesai!</h2>";
echo "<p><strong>Login credentials:</strong></p>";
echo "<table style='border-collapse:collapse; width:100%;'>";
echo "<tr style='border-bottom:1px solid #334155;'><th style='text-align:left; padding:8px;'>Akun</th><th style='text-align:left; padding:8px;'>Email</th><th style='text-align:left; padding:8px;'>Password</th></tr>";
echo "<tr style='border-bottom:1px solid #334155;'><td style='padding:8px;'>Super Admin</td><td style='padding:8px;'><code>superadmin@absensi.com</code></td><td style='padding:8px;'><code>password</code></td></tr>";
echo "<tr style='border-bottom:1px solid #334155;'><td style='padding:8px;'>Admin SMPN 6</td><td style='padding:8px;'><code>admin@smpn6sudimoro.sch.id</code></td><td style='padding:8px;'><code>password</code></td></tr>";
echo "</table>";
echo "<p style='margin-top: 16px;'><a href='/absensi/public/login' style='color: #818cf8; text-decoration: underline;'>→ Buka Halaman Login</a></p>";

echo "<hr style='border-color: #334155; margin: 24px 0;'>";
echo "<p style='color: #ef4444; font-weight: bold;'>⚠️ PENTING: Hapus file setup.php ini setelah selesai!</p>";
echo "</body></html>";
