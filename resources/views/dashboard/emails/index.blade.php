@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem;">
        <h2>Email Accounts</h2>
        <p>Create and manage email accounts for your domains.</p>
    </div>

    <div class="profile-grid">
        <!-- Create Email Form (Left Column/Sidebar) -->
        <div class="white-card">
            <h3 style="margin-bottom: 1.5rem;">Create New Email</h3>
            
            <form action="{{ route('emails.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <div style="display: flex; align-items: center; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; height: 45px;">
                        <input type="text" name="email_prefix" class="form-control" placeholder="username" style="border: none; flex: 1; height: 100%;" required>
                        <span style="background: #f8f9fa; padding: 0 10px; color: #666; border-left: 1px solid #ddd; height: 100%; display: flex; align-items: center;">@</span>
                        <select name="domain" class="form-control" style="border: none; flex: 1; height: 100%; background: #fff; cursor: pointer;" required>
                            @foreach($domains as $domain)
                                <option value="{{ $domain->domain_name }}">{{ $domain->domain_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small style="color: #999; margin-top: 5px; display: block;">Enter username and select domain.</small>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Strong Password" required>
                </div>

                <button type="submit" class="btn btn-submit" style="width: 100%;">Create Account</button>
            </form>
        </div>

        <!-- Email List (Right Column/Main) -->
        <div class="white-card">
            <h3 style="margin-bottom: 1.5rem;">Active Accounts</h3>
            
            @if($emails->count() > 0)
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: separate; border-spacing: 0;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="text-align: left; padding: 1rem; color: #666; font-weight: 600; border-radius: 10px 0 0 10px;">Email Address</th>
                            <th style="text-align: left; padding: 1rem; color: #666; font-weight: 600;">Created</th>
                            <th style="text-align: right; padding: 1rem; color: #666; font-weight: 600; border-radius: 0 10px 10px 0;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emails as $email)
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 1.2rem 1rem; font-weight: 500; border-bottom: 1px solid #eee;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 36px; height: 36px; background: rgba(223, 105, 81, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    {{ $email->email }}
                                </div>
                            </td>
                            <td style="padding: 1.2rem 1rem; color: #666; border-bottom: 1px solid #eee;">
                                {{ $email->created_at->format('d M Y') }}
                            </td>
                            <td style="padding: 1.2rem 1rem; text-align: right; border-bottom: 1px solid #eee;">
                                <div style="display: flex; justify-content: flex-end; gap: 8px;">
                                    <a href="#" class="btn btn-info btn-sm" style="background: #e3f2fd; color: #0d47a1; border: none; padding: 6px 12px; border-radius: 8px; text-decoration: none;">
                                        <i class="fas fa-key"></i> Webmail
                                    </a>
                                    <form action="{{ route('emails.destroy', $email->id) }}" method="POST" onsubmit="return confirm('Delete this email account?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 12px; border-radius: 8px;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
                <div style="text-align: center; padding: 4rem 1rem; color: #999;">
                    <div style="margin-bottom: 1rem; width: 80px; height: 80px; background: #f8f9fa; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fas fa-inbox" style="font-size: 2.5rem; color: #e0e0e0;"></i>
                    </div>
                    <p style="font-size: 1.1rem; margin-bottom: 0.5rem;">No email accounts created yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
