@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div style="margin-bottom: 2rem;">
        <h2>Domain Manager</h2>
        <p>Connect and manage your custom domains.</p>
    </div>

    <div class="profile-grid">
        
        <!-- Connect New Domain (Left Column) -->
        <div class="white-card">
            <div style="margin-bottom: 1.5rem;">
                <h3 style="margin-bottom: 0.5rem;"><i class="fas fa-link" style="color: var(--primary); margin-right: 8px;"></i> Connect Domain</h3>
                <p style="color: #666; font-size: 0.9rem; line-height: 1.5;">
                    Point your domain's <strong>A Record</strong> to our server IP to get started.
                </p>
            </div>

            <div class="alert alert-info" style="margin-bottom: 1.5rem; font-size: 0.9rem;">
                <strong><i class="fas fa-info-circle"></i> DNS Configuration:</strong><br>
                <div style="margin-top: 8px; font-size: 0.85rem;">Set A Record to:</div>
                <code style="background: rgba(0,0,0,0.05); color: var(--primary); padding: 5px 10px; border-radius: 6px; font-weight: 700; font-size: 1.1rem; display: block; margin-top: 5px; text-align: center; border: 1px dashed var(--primary);">192.168.1.100</code>
            </div>

            <form action="{{ route('domains.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Domain Name</label>
                    <input type="text" name="domain_name" class="form-control" placeholder="example.com" required>
                </div>
                <button type="submit" class="btn btn-submit" style="width: 100%;">
                    Connect Domain
                </button>
            </form>
        </div>

        <!-- Domain List (Right Column) -->
        <div class="white-card">
            <h3 style="margin-bottom: 1.5rem;">Your Domains</h3>
            
            @if($domains->isEmpty())
                <div style="text-align: center; padding: 3rem 1rem; color: #999;">
                    <div style="margin-bottom: 1rem; width: 80px; height: 80px; background: #f8f9fa; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="fas fa-globe" style="font-size: 2.5rem; color: #e0e0e0;"></i>
                    </div>
                    <p style="font-size: 1.1rem; font-weight: 500;">No domains connected yet.</p>
                    <p style="font-size: 0.9rem;">Use the form to connect your first domain.</p>
                </div>
            @else
                <div class="domains-list-wrapper">
                    @foreach($domains as $domain)
                    <div class="domain-item" style="display: flex; justify-content: space-between; align-items: center; padding: 1.25rem; background: var(--background); border-radius: 12px; margin-bottom: 1rem; border: 1px solid transparent; transition: all 0.2s;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div class="domain-icon" style="width: 45px; height: 45px; background: rgba(223, 105, 81, 0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                <i class="fas fa-globe" style="font-size: 1.2rem;"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; font-size: 1.05rem;">{{ $domain->domain_name }}</div>
                                <div style="font-size: 0.85rem; margin-top: 3px;">
                                    @if($domain->status == 'active')
                                        <span style="color: #28a745; display: inline-flex; align-items: center; gap: 5px;"><i class="fas fa-check-circle"></i> Active</span>
                                    @else
                                        <span style="color: #ffc107; display: inline-flex; align-items: center; gap: 5px;"><i class="fas fa-clock"></i> Pending DNS</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('domains.destroy', $domain->id) }}" method="POST" onsubmit="return confirm('Disconnect this domain? This will make your site inaccessible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon" style="background: #ffebee; color: #c62828; border: none; width: 40px; height: 40px; border-radius: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" title="Disconnect">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</div>

@push('styles')
<style>
    .domain-item:hover {
        background: white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        transform: translateY(-2px);
    }
    body.dark-mode .domain-item {
        background: #2C2C2C !important;
    }
    body.dark-mode .domain-item:hover {
        background: #333 !important;
    }
</style>
@endpush
@endsection
