<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    protected $cyberPanel;

    public function __construct(\App\Services\CyberPanelService $cyberPanel)
    {
        $this->cyberPanel = $cyberPanel;
    }

    public function index()
    {
        // 1. Total Users (Strict 'user' role)
        $totalUsers = User::where('role', 'user')->count();
        
        // Growth: New Users Today vs Yesterday
        $newUsersToday = User::where('role', 'user')->whereDate('created_at', Carbon::today())->count();
        $newUsersYesterday = User::where('role', 'user')->whereDate('created_at', Carbon::yesterday())->count();
        
        $userGrowth = 0;
        if ($newUsersYesterday > 0) {
            $userGrowth = (($newUsersToday - $newUsersYesterday) / $newUsersYesterday) * 100;
        } elseif ($newUsersToday > 0) {
            $userGrowth = 100; // Infinite growth
        }
        
        // 2. Active Subscriptions
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        
        // Growth: New Active Subs Today vs Yesterday
        $newSubsToday = Subscription::where('status', 'active')->whereDate('updated_at', Carbon::today())->count();
        $newSubsYesterday = Subscription::where('status', 'active')->whereDate('updated_at', Carbon::yesterday())->count();
        
        $subGrowth = 0;
        if ($newSubsYesterday > 0) {
            $subGrowth = (($newSubsToday - $newSubsYesterday) / $newSubsYesterday) * 100;
        } elseif ($newSubsToday > 0) {
            $subGrowth = 100;
        }

        $totalStorage = User::where('role', 'user')->sum('storage_usage');
        $pendingRequests = Subscription::where('status', 'pending')->count();
        $revenue = Subscription::where('status', 'active')->sum('price');

        // 3. Chart Data
        
        // Plan Distribution
        $planStarter = Subscription::whereIn('plan_type', ['starter', '1_month', 'Starter Plan', 'starter-plan'])->count();
        $planPro = Subscription::whereIn('plan_type', ['pro', '2_months', 'Pro Plan', 'pro-plan'])->count();
        $planStats = [$planStarter, $planPro];

        // Revenue Last 6 Months
        $revenueLabels = [];
        $revenueData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('M');
            $revenueLabels[] = $monthName;
            
            // Sum price of subscriptions created in that month
            $monthlySum = Subscription::whereMonth('created_at', $date->month)
                                      ->whereYear('created_at', $date->year)
                                      ->sum('price');
            $revenueData[] = $monthlySum;
        }

        return view('admin.dashboard', compact(
            'totalUsers', 'userGrowth', 'newUsersToday',
            'activeSubscriptions', 'subGrowth', 'newSubsToday',
            'totalStorage', 'pendingRequests', 'revenue',
            'planStats', 'revenueLabels', 'revenueData'
        ));
    }

    public function subscriptions()
    {
        $pendingSubscriptions = Subscription::with('user')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
        $activeSubscriptions = Subscription::with('user')->where('status', 'active')->orderBy('created_at', 'desc')->get();
        // Also get others (rejected/expired) if needed, or just include them in active for history? 
        // User asked specifically for "list user yang mengajukan" vs "yang sudah subscription".
        
        return view('admin.subscriptions.index', compact('pendingSubscriptions', 'activeSubscriptions'));
    }

    public function approveSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Determine Duration and Storage Limit
        // Determine Duration and Storage Limit
        $durationInMonths = 1;
        $storageLimit = 10 * 1024 * 1024 * 1024; // 10 GB for all plans as requested

        if (stripos($subscription->plan_type, 'pro') !== false || $subscription->plan_type == '2_months') {
            $durationInMonths = 2;
        } elseif (stripos($subscription->plan_type, 'starter') !== false || $subscription->plan_type == '1_month') {
             $durationInMonths = 1;
        }

        $subscription->update([
            'status' => 'active',
            'starts_at' => Carbon::now(),
            'ends_at' => Carbon::now()->addMonths($durationInMonths),
        ]);
        
        // Update User Storage Limit
        $user = $subscription->user;
        $user->storage_limit = $storageLimit;
        $user->save();

        // --- Create CyberPanel User ---
        // Generate a username from email (take part before @, sanitize)
        $username = strtolower(explode('@', $user->email)[0]);
        $username = preg_replace('/[^a-z0-9]/', '', $username);
        // Ensure username is not too short or taken? For now simple logic.
        if(strlen($username) < 3) $username .= rand(100, 999);
        
        // Generate random password
        $password = \Illuminate\Support\Str::random(12);

        try {
            // Call CyberPanel Service
            $response = $this->cyberPanel->createCyberPanelUser(
                $user->name,
                $username,
                $user->email,
                $password
            );
            
            // Check for API error structure usually {"error": 1, "errorMessage": "..."}
            // Or {"status": 0, "error_message": "..."}
            if ((isset($response['error']) && $response['error'] == 1) || isset($response['error_message'])) {
                $msg = $response['errorMessage'] ?? $response['error_message'] ?? 'Unknown error';
                \Illuminate\Support\Facades\Log::error("CyberPanel API Error: " . $msg);
                 return redirect()->back()->with('error', 'Subscription approved in DB, but CyberPanel User creation failed: ' . $msg);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("CyberPanel User Creation Failed for {$user->email}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Subscription approved in DB, but CyberPanel connection failed. Please check logs/configuration.');
        }

        // Send Approval Email with Credentials
        $credentials = [
            'username' => $username,
            'password' => $password
        ];

        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SubscriptionApproved($subscription, $credentials));
        } catch (\Exception $e) {
            // Log error or ignore if mail fails to avoid blocking the request
            \Illuminate\Support\Facades\Log::error('Failed to send subscription email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Subscription approved & Email sent successfully.');
    }

    public function rejectSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete(); // Or set status to 'rejected' if soft delete preferred

        return redirect()->back()->with('success', 'Subscription rejected/deleted.');
    }

    public function deleteSubscription($id)
    {
        $subscription = Subscription::findOrFail($id);
        
        // Optional: Add logic here to delete from CyberPanel if needed (e.g., delete website/user)
        // For now, primarily for DB cleanup/retry.
        
        $subscription->delete();

        return redirect()->back()->with('success', 'Subscription deleted successfully. User can now request a new plan.');
    }
}
