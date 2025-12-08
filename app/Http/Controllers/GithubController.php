<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GithubSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GithubController extends Controller
{
    public function index()
    {
        $setting = Auth::user()->github;
        return view('dashboard.github.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'repo_url' => 'required|string',
            'branch' => 'required|string',
        ]);

        $setting = GithubSetting::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'repo_url' => $request->repo_url,
                'branch' => $request->branch,
            ]
        );

        if (!$setting->public_key) {
            // Generate SSH Keys
            $rsaKey = openssl_pkey_new([
                'private_key_bits' => 2048,
                'private_key_type' => OPENSSL_KEYTYPE_RSA,
            ]);

            openssl_pkey_export($rsaKey, $privateKey);
            $publicKey = $this->getPublicKey($rsaKey);

            $setting->update([
                'private_key' => $privateKey,
                'public_key' => $publicKey,
                'webhook_secret' => Str::random(32),
            ]);
        }

        return back()->with('success', 'GitHub settings saved successfully.');
    }

    private function getPublicKey($resource)
    {
        $keyData = openssl_pkey_get_details($resource);
        // Convert to OpenSSH format
        $buffer = pack('N', 7) . 'ssh-rsa' .
                  $this->sshEncodeBuffer($keyData['rsa']['e']) .
                  $this->sshEncodeBuffer($keyData['rsa']['n']);
        
        return 'ssh-rsa ' . base64_encode($buffer) . ' generated-key';
    }

    private function sshEncodeBuffer($buffer)
    {
        $len = strlen($buffer);
        if (ord($buffer[0]) & 0x80) {
            $len++;
            return pack('N', $len) . chr(0) . $buffer;
        }
        return pack('N', $len) . $buffer;
    }

    public function webhook(Request $request, $secret)
    {
        $setting = GithubSetting::where('webhook_secret', $secret)->firstOrFail();
        
        // Target Directory: uploads/{user_id}/github_code (isolated)
        $targetDir = storage_path('app/public/uploads/' . $setting->user_id . '/github_code');
        
        // Ensure directory exists or create it? 
        // Logic: Checks if .git exists.
        
        // Prepare SSH Key File
        $keyFile = storage_path('app/private_keys/id_rsa_' . $setting->id);
        if (!file_exists(dirname($keyFile))) mkdir(dirname($keyFile), 0700, true);
        
        file_put_contents($keyFile, $setting->private_key);
        chmod($keyFile, 0600); // Important for SSH

        // Validate payload? (Optional but good)
        // $signature = $request->header('X-Hub-Signature-256');
        
        $repoUrl = $setting->repo_url;
        $branch = $setting->branch;
        
        // SSH Command wrapper to use the key and ignore known_hosts
        $gitSshCmd = "ssh -i \"$keyFile\" -o StrictHostKeyChecking=no";
        
        try {
            if (!is_dir($targetDir . '/.git')) {
                // Clone
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                
                // Using Process to execute git
                // Note: Windows git path might vary, assuming 'git' is in PATH.
                $cmd = ['git', 'clone', '-b', $branch, $repoUrl, '.'];
            } else {
                // Pull
                $cmd = ['git', 'pull', 'origin', $branch];
            }

            $result = Process::path($targetDir)
                ->env(['GIT_SSH_COMMAND' => $gitSshCmd])
                ->run($cmd);

            // Cleanup key file
            unlink($keyFile);

            if ($result->successful()) {
                Log::info("Git Deployment Success for User {$setting->user_id}: " . $result->output());
                return response()->json(['success' => true, 'message' => 'Deployed successfully.']);
            } else {
                Log::error("Git Deployment Failed: " . $result->errorOutput());
                return response()->json(['success' => false, 'error' => $result->errorOutput()], 500);
            }

        } catch (\Exception $e) {
            if (file_exists($keyFile)) unlink($keyFile);
            Log::error("Git Deployment Exception: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
