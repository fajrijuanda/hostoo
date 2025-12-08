@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-weight: 700; color: #333;">My Profile</h2>
        <p style="color: #666;">Manage your personal information and hosting identity.</p>
    </div>

    <!-- Responsive Grid Layout -->
    <div class="profile-grid">
        
        <!-- Sidebar context / Profile Summary -->
        <div class="card" style="background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); text-align: center; height: fit-content;">
            <div class="avatar-preview" style="width: 120px; height: 120px; margin: 0 auto 1.5rem;">
                 @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid #f8f9fa;">
                @else
                    <div style="width: 100%; height: 100%; background: #f8f9fa; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 4px solid #fff; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <i class="fas fa-user fa-4x" style="color: #dee2e6;"></i>
                    </div>
                @endif
            </div>
            <h4 style="margin-bottom: 0.5rem; font-weight: 700; color: #333;">{{ $user->name }}</h4>
            <p style="font-size: 0.95rem; color: #777; margin-bottom: 2rem;">{{ $user->email }}</p>
            
            <div style="text-align: left; background: #f8f9fa; padding: 1.5rem; border-radius: 12px; border: 1px solid #e9ecef;">
                 <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #e9ecef;">
                    <span style="color: #666;">Member Since</span>
                    <strong style="color: #333;">{{ $user->created_at->format('M Y') }}</strong>
                 </div>
                 <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                    <span style="color: #666;">Status</span>
                    @if($user->hasActiveSubscription() || $user->isAdmin())
                        <span class="badge" style="background: #e8f5e9; color: #2e7d32; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">Active</span>
                    @else
                        <span class="badge" style="background: #e9ecef; color: #495057; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.8rem;">Inactive</span>
                    @endif
                 </div>
            </div>
        </div>

        <!-- Main Edit Form -->
        <div class="card" style="background: white; padding: 2.5rem; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <div style="border-bottom: 1px solid #eee; padding-bottom: 1.5rem; margin-bottom: 2rem;">
                <h3 style="color: #333; margin-bottom: 0.5rem; font-weight: 700; font-size: 1.25rem;">Profile Details</h3>
                <p style="color: #777; font-size: 0.95rem; margin: 0;">Update your profile information and contact details.</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div class="form-group" style="margin: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required 
                               style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;">
                    </div>
                     <div class="form-group" style="margin: 0;">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="+62..." 
                               style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Email Address</label>
                    <input type="email" class="form-control" value="{{ $user->email }}" disabled 
                           style="width: 100%; padding: 0.8rem; border: 1px solid #e9ecef; border-radius: 8px; background: #f1f3f5; color: #6c757d;">
                    <small style="color: #999; margin-top: 5px; display: block;">Email cannot be changed directly.</small>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: #333;">Address</label>
                    <textarea name="address" class="form-control" rows="3" 
                              style="width: 100%; padding: 0.8rem; border: 1px solid #e0e0e0; border-radius: 8px; background: #f8f9fa;">{{ old('address', $user->address) }}</textarea>
                </div>

                <div class="form-group" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #eee;">
                    <label style="margin-bottom: 1rem; display: block; font-weight: 500; color: #333;">Update Profile Photo</label>
                    <div style="background: #f8f9fa; border: 2px dashed #e9ecef; border-radius: 12px; padding: 1.5rem; text-align: center;">
                        <input type="file" name="avatar" class="form-control" accept="image/*" style="width: 100%;">
                        <p style="font-size: 0.8rem; color: #999; margin-top: 10px;">Recommended: Square JPG/PNG, max 2MB.</p>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px; border: none; background: var(--primary); color: white; border-radius: 10px; cursor: pointer; font-weight: 600; box-shadow: 0 4px 12px rgba(241, 165, 1, 0.3);">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
