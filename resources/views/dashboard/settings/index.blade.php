@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem;">
        <h2 style="font-weight: 700; color: #333;">Account Settings</h2>
        <p style="color: #666;">Manage your password and account security.</p>
    </div>

    <!-- Layout using standard charts-grid (2fr 1fr) for consistency -->
    <div class="charts-grid">
        
        <!-- Change Password Card -->
        <div class="card" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #eee;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #e3f2fd; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                    <i class="fas fa-lock" style="color: #1976d2; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700;">Security</h3>
                    <span style="font-size: 0.9rem; color: #666;">Update your password</span>
                </div>
            </div>
            
            <form action="{{ route('settings.password.update') }}" method="POST">
                @csrf
                @method('put')

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Current Password</label>
                    <input type="password" name="current_password" class="form-control" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;" required>
                    @error('current_password')
                        <span style="color: #d32f2f; font-size: 0.85rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">New Password</label>
                    <input type="password" name="password" class="form-control" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;" required>
                    @error('password')
                        <span style="color: #d32f2f; font-size: 0.85rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-control" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;" required>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 12px 30px; border: none; background: var(--primary); color: white; border-radius: 10px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 12px rgba(223, 105, 81, 0.3);">Update Password</button>
            </form>
        </div>

        <!-- Danger Zone Card -->
        <div class="card" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #ffebee; height: fit-content;">
            <div style="display: flex; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #ffebee;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: #ffebee; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #d32f2f; font-size: 1.2rem;"></i>
                </div>
                <div>
                    <h3 style="margin: 0; font-size: 1.25rem; font-weight: 700; color: #d32f2f;">Danger Zone</h3>
                    <span style="font-size: 0.9rem; color: #d32f2f;">Irreversible actions</span>
                </div>
            </div>
            
            <p style="color: #666; margin-bottom: 1.5rem; line-height: 1.6; font-size: 0.95rem;">
                Once you delete your account, there is no going back. Please be certain.
            </p>

            <form id="delete-account-form" action="{{ route('settings.destroy') }}" method="POST" onsubmit="confirmAccountDelete(event)">
                @csrf
                @method('delete')
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Confirm Password</label>
                    <input type="password" name="password" class="form-control" 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;" placeholder="Enter password to confirm" required>
                    @error('password', 'userDeletion')
                        <span style="color: #d32f2f; font-size: 0.85rem; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-danger" style="width: 100%; padding: 12px; border-radius: 10px; background: #d32f2f; color: white; border: none; font-weight: 600; box-shadow: 0 4px 12px rgba(211, 47, 47, 0.2);">Delete Account</button>
            </form>
        </div>
    </div>
</div>
@endsection

<script>
    function confirmAccountDelete(e) {
        e.preventDefault();
        Hostoo.alert({
            title: 'Delete Account?',
            text: 'This action is IRREVERSIBLE. Are you sure you want to delete your account?',
            type: 'error',
            showCancel: true,
            confirmText: 'Yes, Delete'
        }).then(confirmed => {
            if(confirmed) {
                document.getElementById('delete-account-form').submit();
            }
        });
    }
</script>
