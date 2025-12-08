<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function selectPlan(Request $request)
    {
        // Ensure user is authenticated
        // Note: Middleware 'auth' should handle the redirection to login, 
        // passing 'url.intended' so they come back here.

        $user = Auth::user();
        $planType = $request->query('plan_type');
        $price = $request->query('price');

        // Validate plan
        if (!$planType || !$price) {
            return redirect('/');
        }

        // Format Plan Name
        if ($planType == 'starter' || $planType == '1_month') {
            $planName = '1 Month Plan (Starter)';
        } else {
            $planName = '2 Months Plan (Pro)';
        }
        $priceFormatted = 'Rp ' . number_format($price, 0, ',', '.');

        // Create Pending Subscription
        \App\Models\Subscription::create([
            'user_id' => $user->id,
            'plan_type' => $planType,
            'price' => $price,
            'status' => 'pending',
            // starts_at and ends_at will be set by admin upon activation
        ]);
        
        // WhatsApp Redirect
        // Format: Hello Admin, I am interested in the [Plan] - Price: [Price]. My email is [Email].
        $adminNumber = '6287871607761'; // Updated Admin Number
        
        $message = "Hello Admin, I am interested in the *{$planName}* - Price: *{$priceFormatted}*.\n";
        $message .= "My email is: {$user->email}";

        $whatsappUrl = "https://wa.me/{$adminNumber}?text=" . urlencode($message);

        return redirect()->away($whatsappUrl);
    }
}
