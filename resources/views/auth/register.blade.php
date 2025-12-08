@extends('layouts.app')

@section('content')
<div class="auth-container">
    <h2 class="auth-title">Register</h2>
    
    <form action="{{ route('register.post') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
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

        <button type="submit" class="btn-submit">Sign Up</button>
    </form>

    <p style="margin-top: 2rem;">
        Already have an account? <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Login</a>
    </p>
</div>
@endsection
