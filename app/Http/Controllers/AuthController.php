<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

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
            
            if (!$user->address || !$user->phone) {
                 return redirect()->route('profile.edit')->with('warning', 'Please complete your profile first.');
            }
            
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        Auth::login($user);

        Auth::login($user);

        return redirect()->route('profile.edit')->with('success', 'Account created! Please complete your profile.');
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
                    'password' => null // No password for social login
                ]);
            } else {
                // Update google_id if existing
                if (!$user->google_id) {
                    $user->update(['google_id' => $socialUser->getId()]);
                }
            }

            Auth::login($user);

            if (!$user->address || !$user->phone) {
                return redirect()->route('profile.edit')->with('warning', 'Please complete your profile first.');
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Google Login Failed: ' . $e->getMessage()]);
        }
    }
}
