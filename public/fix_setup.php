<?php
/**
 * Fix duplicate migrations and re-run setup
 */
set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$basePath = realpath(__DIR__ . '/..');
$migrationsPath = $basePath . '/database/migrations';

echo "<html><head><title>Fix & Setup</title><style>
body { font-family: 'Segoe UI', sans-serif; background: #0f172a; color: #e2e8f0; padding: 40px; max-width: 900px; margin: 0 auto; }
h1 { color: #818cf8; }
.step { background: #1e293b; border-radius: 12px; padding: 16px; margin: 12px 0; border-left: 4px solid #818cf8; }
.step.success { border-left-color: #10b981; }
.step.error { border-left-color: #ef4444; }
.step h3 { margin: 0 0 8px 0; font-size: 14px; }
pre { background: #0f172a; padding: 12px; border-radius: 8px; overflow-x: auto; font-size: 12px; white-space: pre-wrap; }
.badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: bold; }
.badge.ok { background: #10b981; color: white; }
.badge.fail { background: #ef4444; color: white; }
</style></head><body>";

echo "<h1>🔧 Fix Duplicates & Re-Setup</h1>";

function runCmd($cmd, $cwd) {
    $desc = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
    $proc = proc_open($cmd, $desc, $pipes, $cwd);
    if (!is_resource($proc)) return ['output' => 'Failed', 'error' => true];
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]); fclose($pipes[2]);
    $exitCode = proc_close($proc);
    return ['output' => $stdout . ($stderr ? "\n" . $stderr : ''), 'error' => $exitCode !== 0];
}

// Step 1: Delete duplicate personal_access_tokens migrations
echo "<div class='step'><h3>Step 1: Hapus duplikat migration personal_access_tokens</h3>";
$deleted = [];
$duplicates = [
    '2026_04_14_055455_create_personal_access_tokens_table.php',
    '2026_04_14_060055_create_personal_access_tokens_table.php',
];
foreach ($duplicates as $file) {
    $path = $migrationsPath . '/' . $file;
    if (file_exists($path)) {
        unlink($path);
        $deleted[] = $file;
    }
}
echo "<span class='badge ok'>OK</span>";
echo "<pre>Deleted: " . implode("\n", $deleted) . "\nKept: 2026_04_14_054903_create_personal_access_tokens_table.php</pre>";
echo "</div>";

// Step 2: Re-run migrate fresh --seed
echo "<div class='step'><h3>Step 2: php artisan migrate:fresh --seed</h3>";
$result = runCmd('php artisan migrate:fresh --seed --no-interaction 2>&1', $basePath);
$class = $result['error'] ? 'fail' : 'ok';
echo "<span class='badge {$class}'>" . ($result['error'] ? 'ERROR' : 'OK') . "</span>";
echo "<pre>" . htmlspecialchars($result['output']) . "</pre></div>";

if (!$result['error']) {
    echo "<hr style='border-color:#334155; margin:24px 0;'>";
    echo "<h2 style='color:#10b981;'>✅ Setup Berhasil!</h2>";
    echo "<p><a href='/absensi/public/login' style='color:#818cf8; text-decoration:underline; font-size:18px;'>→ Buka Halaman Login</a></p>";
    echo "<p>Email: <code>superadmin@absensi.com</code> | Password: <code>password</code></p>";
}

echo "<p style='color:#ef4444; margin-top:24px;'>⚠️ Hapus file fix_setup.php dan setup.php setelah selesai!</p>";
echo "</body></html>";
