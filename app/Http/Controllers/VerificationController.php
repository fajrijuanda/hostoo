<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOtpMail;
use Carbon\Carbon;

class VerificationController extends Controller
{
    public function show()
    {
        if (Auth::user()->email_verified_at) {
            return redirect()->route('dashboard');
        }
        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $user = Auth::user();

        if ($user->otp !== $request->otp) {
            return back()->with('error', 'Invalid OTP code.');
        }

        if (Carbon::now()->greaterThan($user->otp_expires_at)) {
            return back()->with('error', 'OTP has expired. Please request a new one.');
        }

        // OTP Valid
        $user->update([
            'email_verified_at' => Carbon::now(),
            'otp' => null,
            'otp_expires_at' => null
        ]);

        // Return view with verified flag to trigger JS Alert -> Redirect
        return view('auth.verify-otp', ['verified' => true]);
    }

    public function resend(Request $request)
    {
        $user = Auth::user();
        
        if ($user->email_verified_at) {
            return redirect()->route('dashboard');
        }

        $otp = rand(100000, 999999);
        $user->update([
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10)
        ]);

        try {
            Mail::to($user->email)->send(new VerifyOtpMail($otp));
            return back()->with('success', 'A new OTP has been sent to your email.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send OTP. Please try again later.');
        }
    }
}
