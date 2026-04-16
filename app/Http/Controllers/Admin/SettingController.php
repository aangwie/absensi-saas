<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Process;

class SettingController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $githubToken = Setting::getValue('github_token', '');
        return view('admin.settings.index', compact('githubToken'));
    }

    /**
     * Update GitHub token.
     */
    public function updateToken(Request $request)
    {
        $request->validate([
            'github_token' => 'required|string|min:5',
        ]);

        Setting::setValue('github_token', $request->github_token);

        return response()->json([
            'success' => true,
            'message' => 'GitHub token berhasil diperbarui!'
        ]);
    }

    /**
     * Pull latest changes from GitHub using token.
     */
    public function gitPull(Request $request)
    {
        $token = Setting::getValue('github_token');

        if (empty($token) || $token === 'ghp_xxxxxxxxxx') {
            return response()->json([
                'success' => false,
                'output' => 'GitHub token belum dikonfigurasi. Silakan masukkan token yang valid di form di atas.'
            ]);
        }

        try {
            $basePath = base_path();

            // Get current remote URL
            $getRemote = Process::fromShellCommandline('git remote get-url origin', $basePath);
            $getRemote->run();
            $remoteUrl = trim($getRemote->getOutput());

            if (empty($remoteUrl)) {
                return response()->json([
                    'success' => false,
                    'output' => 'Tidak dapat menemukan remote origin URL.'
                ]);
            }

            // Build authenticated URL
            $authenticatedUrl = preg_replace(
                '#https://(.*?)@?github\.com/#',
                'https://' . $token . '@github.com/',
                $remoteUrl
            );

            if (strpos($authenticatedUrl, $token) === false) {
                $authenticatedUrl = str_replace('https://github.com/', 'https://' . $token . '@github.com/', $remoteUrl);
            }

            $output = '';

            // Set remote URL with token
            $setRemote = Process::fromShellCommandline(
                'git remote set-url origin ' . escapeshellarg($authenticatedUrl),
                $basePath
            );
            $setRemote->run();

            // Fetch latest
            $fetch = Process::fromShellCommandline('git fetch origin 2>&1', $basePath);
            $fetch->setTimeout(120);
            $fetch->run();
            $output .= "$ git fetch origin\n" . $fetch->getOutput() . $fetch->getErrorOutput() . "\n";

            // Get current branch
            $branch = Process::fromShellCommandline('git rev-parse --abbrev-ref HEAD', $basePath);
            $branch->run();
            $currentBranch = trim($branch->getOutput());
            $output .= "Branch: " . $currentBranch . "\n\n";

            // Show incoming changes log
            $log = Process::fromShellCommandline(
                'git log HEAD..origin/' . $currentBranch . ' --oneline --no-decorate 2>&1',
                $basePath
            );
            $log->run();
            $logOutput = trim($log->getOutput());

            if (!empty($logOutput)) {
                $output .= "📋 Perubahan yang akan diterapkan:\n" . $logOutput . "\n\n";
            } else {
                $output .= "✅ Tidak ada perubahan baru dari remote.\n\n";
            }

            // Pull latest changes
            $pull = Process::fromShellCommandline(
                'git pull origin ' . $currentBranch . ' 2>&1',
                $basePath
            );
            $pull->setTimeout(120);
            $pull->run();
            $output .= "$ git pull origin " . $currentBranch . "\n" . $pull->getOutput() . $pull->getErrorOutput();

            // Restore remote URL without token for safety
            $restoreRemote = Process::fromShellCommandline(
                'git remote set-url origin ' . escapeshellarg($remoteUrl),
                $basePath
            );
            $restoreRemote->run();

            return response()->json([
                'success' => $pull->isSuccessful(),
                'output' => $output
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            $output = Artisan::output();

            Artisan::call('view:clear');
            $output .= Artisan::output();

            Artisan::call('route:clear');
            $output .= Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Cache berhasil dibersihkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear config cache.
     */
    public function clearConfig()
    {
        try {
            Artisan::call('config:clear');
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Config cache berhasil dibersihkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Run database migrations.
     */
    public function migrate()
    {
        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'output' => $output,
                'message' => 'Migrasi database berhasil dijalankan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'output' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
