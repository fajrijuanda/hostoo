@extends('layouts.app')

@section('content')
<div class="auth-container" style="max-width: 500px; margin: 4rem auto; padding: 2rem; background: white; border-radius: 20px; box-shadow: var(--shadow);">
    <h2 class="auth-title" style="text-align: center; margin-bottom: 2rem;">Reset Password</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        
        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" class="form-control" value="{{ request()->email }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label>New Password</label>
            <input type="password" name="password" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <div class="form-group" style="margin-top: 1rem;">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
        </div>

        <button type="submit" class="btn-submit" style="width: 100%; background: var(--secondary); color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-top: 1.5rem;">
            Reset Password
        </button>
    </form>
</div>
@endsection
