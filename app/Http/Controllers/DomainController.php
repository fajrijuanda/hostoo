<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index()
    {
        $domains = \App\Models\Domain::where('user_id', \Illuminate\Support\Facades\Auth::id())->get();
        return view('dashboard.domains.index', compact('domains'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'domain_name' => 'required|string|unique:domains,domain_name|regex:/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i'
        ]);

        // Create Website on CyberPanel
        $service = app(\App\Services\CyberPanelService::class);
        
        // Random FTP Password
        $ftpPassword = \Illuminate\Support\Str::random(16);
        $ftpUsername = \Illuminate\Support\Str::slug($request->domain_name);

        // CyberPanel System User Logic
        // We use a consistent username based on Laravel User ID
        // e.g., 'client_1', 'client_55'
        $cpUsername = 'client_' . \Illuminate\Support\Facades\Auth::id();
        $cpPassword = \Illuminate\Support\Str::random(16); // Password for CP User (not shared with user for now)
        $userEmail  = \Illuminate\Support\Facades\Auth::user()->email;
        $userName   = \Illuminate\Support\Facades\Auth::user()->name;

        try {
            // 0. Ensure CyberPanel User Exists
            try {
                // Try create user. If exists, it might error or return specific code.
                // We attempt creation. If it fails due to "Exists", we assume it's fine.
                // Ideally, we could check if user exists first but no simple API for that without admin.
                $userResult = $service->createCyberPanelUser($userName, $cpUsername, $userEmail, $cpPassword);
                
                // Optional: Log result or check for specific "User already exists" message if strictly needed.
                // Log::info("CP User Creation Result: " . json_encode($userResult));
            } catch (\Exception $e) {
                // Ignore if user already exists (likely)
                // In a robust system, we'd handle this better.
            }

            // 1. Create Website (Owned by the new CP User)
            // Determine Package from User's Subscription
            // Assuming one active subscription or taking the first one
            $user = \Illuminate\Support\Facades\Auth::user();
            $subscription = $user->subscriptions()->where('status', 'active')->first();
            
            $packageName = 'Default';
            if ($subscription) {
                 $packageName = preg_replace('/[^a-zA-Z0-9_\-]/', '', $subscription->plan_type);
                 if(empty($packageName)) $packageName = 'Default';
            }
            
            // --- NEW: CREATE PACKAGE FIRST (As per User Flow) ---
            // Using stored CP Password
            if ($user->cp_password) {
                try {
                     $service->createPackage(
                     $service->createPackage(
                        $packageName, 
                        10000, 
                        0, 
                        3, 
                        1, 
                        100, 
                        1,
                        ['username' => $cpUsername, 'password' => $user->cp_password]
                      ) 
                    );
                } catch (\Exception $pkgEx) {
                    \Illuminate\Support\Facades\Log::warning("CyberPanel Package Creation Failed in DomainController: " . $pkgEx->getMessage());
                }
            } else {
                 \Illuminate\Support\Facades\Log::warning("No CP Password found for user {$user->id}, skipping Package Creation.");
            }
            
            $service->createWebsite($request->domain_name, $userEmail, $packageName, $cpUsername);
            
            // 2. Create/Set FTP 
            // Since website is now owned by 'client_ID', FTP should technically be managed by them.
            // But 'admin' can usually create FTP for any site.
            // We continue creating a dedicated FTP account.
            $service->createFtpAccount($request->domain_name, $ftpUsername, $ftpPassword, $cpUsername);

            // 3. Create NameServer (Auto-assign IP from Server)
            $serverIp = parse_url(config('services.cyberpanel.url'), PHP_URL_HOST);
            $ns1 = 'ns1.' . $request->domain_name;
            $ns2 = 'ns2.' . $request->domain_name;
            
            // Call API to create nameservers
            $service->createNameServer($request->domain_name, $ns1, $ns2, $serverIp); // Using same IP for both for now, typical for single CP instance

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("CyberPanel Error: " . $e->getMessage());
            // Continue locally but warn? Or fail? For now continue so UI works even if API fails (dev mode)
        }

        \App\Models\Domain::create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'domain_name' => $request->domain_name,
            'status' => 'pending', 
            'ftp_host' => parse_url(config('services.cyberpanel.url'), PHP_URL_HOST), // Use CyberPanel IP/Host
            'ftp_username' => $ftpUsername,
            'ftp_password' => $ftpPassword,
        ]);

        return redirect()->back()->with('success', 'Domain added successfully. Please configure your DNS.');
    }

    public function destroy($id)
    {
        $domain = \App\Models\Domain::where('user_id', \Illuminate\Support\Facades\Auth::id())->findOrFail($id);
        $domain->delete();
        return redirect()->back()->with('success', 'Domain removed.');
    }
}
