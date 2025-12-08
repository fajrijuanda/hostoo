<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\HostingPlan;

class SubscriptionPageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->where('status', 'active')->latest()->first();
        
        // If no active, maybe pending?
        if (!$subscription) {
            $subscription = $user->subscriptions()->latest()->first();
        }

        $plans = HostingPlan::where('price', '>', 0)->get();

        return view('dashboard.subscription', compact('user', 'subscription', 'plans'));
    }
}
