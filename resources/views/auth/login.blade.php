@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h2 class="auth-title">Login</h2>
    
    <!-- Manual Login Form -->
    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div style="text-align: right; margin-bottom: 1rem;">
            <a href="{{ route('password.request') }}" style="color: var(--primary); font-size: 0.9rem;">Forgot Password?</a>
        </div>

        <div class="form-group">
            <label>Security Check</label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <img src="{{ route('captcha.image') }}" onclick="this.src='{{ route('captcha.image') }}?'+Math.random()" alt="Captcha" style="border-radius: 5px; cursor: pointer; height: 40px;" title="Click to refresh">
                <input type="text" name="captcha" class="form-control" placeholder="Enter code" required style="flex: 1;">
            </div>
            @error('captcha')
                <div style="color: red; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Log In</button>
    </form>

    <div style="margin: 1.5rem 0; color: #aaa;">OR</div>

    <!-- Google Login -->
    <a href="{{ route('auth.google') }}" class="btn-google">
        <i class="fab fa-google" style="color: #DB4437; font-size: 1.2rem;"></i>
        Login with Google
    </a>
    
    <p style="margin-top: 2rem;">
        Don't have an account? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Register</a>
    </p>
</div>
@endsection
