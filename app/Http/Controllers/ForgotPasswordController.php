<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'captcha' => ['required', function ($attribute, $value, $fail) {
                if (session('captcha_code') !== strtoupper($value)) {
                    $fail('The Security Check code is incorrect.');
                }
            }],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        // Generate Token
        $token = Str::random(64);

        // Store Token in password_resets table (standard Laravel table)
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Send Email
        try {
            Mail::to($request->email)->send(new ResetPasswordMail($token, $user));
        } catch (\Exception $e) {
            Log::error('Mail Error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send email. Check SMTP configuration.']);
        }

        return back()->with('status', 'We have e-mailed your password reset link!');
    }
}
