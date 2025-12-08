@extends('layouts.app')

@section('content')
<div class="auth-container" style="max-width: 500px; margin: 4rem auto; padding: 2rem; background: white; border-radius: 20px; box-shadow: var(--shadow);">
    <h2 class="auth-title" style="text-align: center; margin-bottom: 1rem;">Forgot Password?</h2>
    <p style="text-align: center; color: var(--text-color); margin-bottom: 2rem;">
        Enter your email address and we will send you a link to reset your password.
    </p>

    @if (session('status'))
        <div class="alert alert-success" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label>Security Check</label>
            <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                <img src="{{ route('captcha.image') }}" onclick="this.src='{{ route('captcha.image') }}?'+Math.random()" alt="Captcha" style="border-radius: 5px; cursor: pointer; height: 40px;" title="Click to refresh">
                <input type="text" name="captcha" class="form-control" placeholder="Enter code" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; flex: 1;">
            </div>
            @error('captcha')
                <div style="color: red; font-size: 0.8rem; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit" style="width: 100%; background: var(--secondary); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 20px;">
            Send Reset Link
        </button>
    </form>
    
    <div style="text-align: center; margin-top: 1.5rem;">
        <a href="{{ route('login') }}" style="color: var(--text-color); font-size: 0.9rem;">Back to Login</a>
    </div>
</div>
@endsection
