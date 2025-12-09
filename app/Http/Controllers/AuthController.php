<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyOtpMail;

class AuthController extends Controller
{
    // Show Login Form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle Manual Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'captcha' => ['required', function ($attribute, $value, $fail) {
                if (session('captcha_code') !== strtoupper($value)) {
                    $fail('The Security Check code is incorrect.');
                }
            }],
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Redirect Admin immediately or after profile check?
            // User said: "sidebar langsung berubah menjadi sidebar admin malau saat pertama kali login perlu mengisi profile terlebih dahulu"
            // This suggests they MIGHT go to profile first. 
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (!$user->email_verified_at) {
                return redirect()->route('verification.notice')->with('warning', 'Please verify your email address first.');
            }

            // Redirect Admin immediately or after profile check?
            if (!$user->address || !$user->phone) {
                 return redirect()->route('profile.edit')->with('warning', 'Please complete your profile first.');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    // Show Register Form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle Registration
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'captcha' => ['required', function ($attribute, $value, $fail) {
                if (session('captcha_code') !== strtoupper($value)) {
                    $fail('The Security Check code is incorrect.');
                }
            }],
        ]);

        $otp = rand(100000, 999999);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'otp' => $otp,
            'otp_expires_at' => \Carbon\Carbon::now()->addMinutes(10)
        ]);

        Auth::login($user);

        // Send OTP Email
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifyOtpMail($otp));
        } catch (\Exception $e) {
            // Log error but allow proceed
        }

        return redirect()->route('verification.notice')->with('success', 'Account created! Please verify your email.');
    }

    // Logout
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    // Google Redirect
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google Callback
    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Register new user from Google
                $user = User::create([
                    'name' => $socialUser->getName(),
                    'email' => $socialUser->getEmail(),
                    'google_id' => $socialUser->getId(),
                    'role' => 'user',
                    // Generate random secure password instead of null to prevent issues
                    'password' => Hash::make(\Illuminate\Support\Str::random(16)), 
                    'email_verified_at' => Carbon::now(),
                ]);
            } else {
                // Update google_id if existing and ensure email is verified
                $updates = [];
                if (!$user->google_id) {
                    $updates['google_id'] = $socialUser->getId();
                }
                if (!$user->email_verified_at) {
                    $updates['email_verified_at'] = Carbon::now();
                }

                if (!empty($updates)) {
                    $user->update($updates);
                }
            }

            Auth::login($user);

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (!$user->address || !$user->phone) {
                return redirect()->route('profile.edit')->with('warning', 'Please complete your profile first.');
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect('/login')->withErrors(['email' => 'Google Login Failed. Please try again or user standard login. (' . substr($e->getMessage(), 0, 50) . '...)']);
        }
    }
}
