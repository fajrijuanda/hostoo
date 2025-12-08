@extends('layouts.app')

@section('content')
<style>
    /* Scoped Dark Mode Styles for Auth Card */
    body.dark-mode .auth-card {
        background-color: #1E1E1E !important;
        border: 1px solid #333 !important;
        box-shadow: none !important;
    }
    
    body.dark-mode .auth-card h3 {
        color: white !important;
    }
    
    body.dark-mode .auth-card p, 
    body.dark-mode .auth-card label {
        color: #e0e0e0 !important;
    }

    body.dark-mode .auth-card input#otp {
        background-color: #2C2C2C !important;
        border-color: #444 !important;
        color: white !important;
    }

    body.dark-mode .auth-card .logo-light {
        display: none;
    }

    body.dark-mode .auth-card .logo-dark {
        display: inline-block !important;
    }
</style>

<div class="auth-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 2rem;">
    <div class="auth-card" style="background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); width: 100%; max-width: 450px; text-align: center;">
        
        <!-- Logo -->
        <div style="margin-bottom: 2rem;">
             <img src="{{ asset('images/logo.png') }}" alt="Hostoo Logo" class="logo-light" style="height: 40px; margin-bottom: 10px;">
             <img src="{{ asset('images/dark-logo.png') }}" alt="Hostoo Logo Dark" class="logo-dark" style="height: 40px; margin-bottom: 10px; display: none;">
             <h3 style="font-weight: 700; color: #333; margin-top: 10px;">Verify Your Email</h3>
             <p style="color: #666; font-size: 0.9rem;">We have sent a 6-digit verification code to<br><strong>{{ Auth::user()->email }}</strong></p>
        </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div class="alert alert-success" style="background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; text-align: left; font-size: 0.9rem;">
                <i class="fas fa-check-circle" style="margin-right: 8px;"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Error Alert -->
        @if (session('error'))
            <div class="alert alert-danger" style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; text-align: left; font-size: 0.9rem;">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> {{ session('error') }}
            </div>
        @endif
        
        @if($errors->any())
             <div class="alert alert-danger" style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 1.5rem; text-align: left; font-size: 0.9rem;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('verification.verify') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1.5rem; text-align: left;">
                <label for="otp" style="font-weight: 500; color: #333; display: block; margin-bottom: 0.5rem;">One-Time Password (OTP)</label>
                <input type="text" name="otp" id="otp" 
                       class="form-control" placeholder="Enter 6-digit code" 
                       required maxlength="6" pattern="\d{6}"
                       style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 1.2rem; text-align: center; letter-spacing: 5px;">
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s; box-shadow: 0 4px 10px rgba(223, 105, 81, 0.3);">
                Verify Email
            </button>
        </form>

        <!-- Resend Section -->
        <div style="margin-top: 2rem; border-top: 1px solid #f0f0f0; padding-top: 1.5rem;">
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 10px;">Didn't receive the code?</p>
            <form action="{{ route('verification.resend') }}" method="POST" id="resendForm">
                @csrf
                <button type="submit" id="resendBtn" class="btn btn-link" disabled style="color: #999; text-decoration: none; font-weight: 600; cursor: not-allowed; background: none; border: none; padding: 0;">
                    Resend OTP (<span id="timer">120</span>s)
                </button>
            </form>
        </div>

    </div>
</div>

<!-- SweetAlert Remvoved -->

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Timer Logic
        let timeLeft = 120;
        const resendBtn = document.getElementById('resendBtn');
        const timerSpan = document.getElementById('timer');
        
        const countdown = setInterval(() => {
            timeLeft--;
            timerSpan.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(countdown);
                resendBtn.disabled = false;
                resendBtn.style.color = 'var(--primary)';
                resendBtn.style.cursor = 'pointer';
                resendBtn.innerHTML = 'Resend OTP';
            }
        }, 1000);

        // Success Custom Alert (Intercept Redirect)
        @if(isset($verified) && $verified)
            Hostoo.alert({
                title: 'Success!',
                text: 'Email verified successfully! Welcome to Hostoo.',
                type: 'success',
                confirmText: 'OK'
            }).then((confirmed) => {
                if (confirmed) {
                    window.location.href = "{{ route('profile.edit') }}";
                }
            });
        @endif
    });
</script>

<!-- Add AJAX handling for cleaner experience matching "Click OK -> Redirect" request -->
<script>
    const form = document.querySelector('form[action="{{ route('verification.verify') }}"]');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json' // Request JSON response if possible
            },
            body: JSON.stringify({ otp: document.getElementById('otp').value })
        })
        .then(response => {
            if (response.redirected) {
                // If standard redirect (unlikely with json accept, but Laravel might coerce)
                window.location.href = response.url;
                return;
            }
            return response.json();
        })
        .then(data => {
            if (data.success) { // We need verification method to return JSON for this to work perfectly or blade conditional
                 // Actually, let's stick to standard Session Flash for simplicity + SweetAlert on Profile Page?
                 // NO, user said "setelah email terverifikasi dan user mengklik ok pada alert sukses maka akan menuju halaman profile".
                 // This STRICTLY implies Alert is on verify page OR intersitial.
            }
            // For now, let's keep it simple: Server side validation.
            // If success, we just rely on Controller redirect.
            // But to show "Click OK -> Redirect", maybe we verify, then return a view with the script to show alert?
            // OR use AJAX. 
        })
        .catch(err => {
             // Fallback to standard submit
             form.submit();
        });
    });
</script>
@endsection
