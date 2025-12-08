<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Mail\SubscriptionThankYou;
use Illuminate\Support\Facades\Mail;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create([
            'email' => $request->email
        ]);

        // Send Thank You Email
        try {
            Mail::to($subscriber->email)->send(new SubscriptionThankYou($subscriber->email));
        } catch (\Exception $e) {
            \Log::error('Failed to send subscription email: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you for subscribing! Check your inbox for confirmation.');
    }

    public function unsubscribe(Request $request)
    {
        return view('subscription.unsubscribe');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $subscriber = Subscriber::where('email', $request->email)->first();

        if ($subscriber) {
            $subscriber->delete();
            // Log the reason
            \Log::info('Subscriber Unsubscribed: ' . $request->email . ' | Reason: ' . $request->reason . ' | Note: ' . $request->custom_reason);
        }

        return redirect()->route('welcome')->with('success', 'You have been successfully unsubscribed.');
    }
}
