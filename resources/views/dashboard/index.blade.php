@extends('layouts.app')

@section('content')
<section class="dashboard" style="padding: 1.5rem 0;">
    <div class="section-title" style="text-align: left; margin-bottom: 2rem;">
        <h3 class="section-subtitle">Client Area</h3>
        <h2 class="section-heading">My Dashboard</h2>
    </div>

    <!-- Subscription Status -->
    <div class="status-card white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); margin-bottom: 2rem; display: flex; align-items: center; gap: 2rem;">
        @php
            $subscription = Auth::user()->subscriptions()->where('status', 'active')->latest()->first() ?? Auth::user()->subscriptions()->latest()->first();
        @endphp

        <div class="status-icon" style="width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; 
            background: {{ $subscription && $subscription->status == 'active' ? '#e8f5e9' : '#fff3e0' }}; 
            color: {{ $subscription && $subscription->status == 'active' ? '#2e7d32' : '#ef6c00' }};">
            <i class="fas {{ $subscription && $subscription->status == 'active' ? 'fa-check-circle' : 'fa-clock' }}"></i>
        </div>
        
        <div style="flex: 1;">
            <h4 style="margin-bottom: 0.5rem; color: #777;">Current Status</h4>
            @if($subscription)
                <h3 style="color: var(--secondary); margin-bottom: 0.2rem; text-transform: capitalize;">
                    {{ $subscription->status }} 
                    @if($subscription->status == 'active')
                        <span style="font-size: 0.9rem; font-weight: normal; color: #777;">(Expires: {{ $subscription->ends_at }})</span>
                    @endif
                </h3>
                @if($subscription->status == 'pending')
                    <p style="font-size: 0.9rem; color: #ef6c00;">Waiting for payment confirmation.</p>
                @endif
            @else
                <h3 style="color: var(--secondary);">No Active Plan</h3>
                <a href="{{ url('/#plans') }}" style="color: var(--primary); font-weight: 600; font-size: 0.9rem;">Buy a Plan</a>
            @endif
        </div>

        @if(Auth::user()->storage_limit > 0)
        <!-- Storage Usage -->
        <div style="flex: 1; border-left: 1px solid #eee; padding-left: 2rem;">
            @php
                $usage = Auth::user()->storage_usage;
                $limit = Auth::user()->storage_limit;
                $percent = min(100, ($usage / $limit) * 100);
                $usageGB = number_format($usage / 1073741824, 2);
                $limitGB = number_format($limit / 1073741824, 0); // 25 or 50
                $color = $percent > 90 ? '#dc3545' : ($percent > 70 ? '#ffc107' : '#28a745');
            @endphp
            <h4 style="margin-bottom: 0.5rem; color: #777;">Storage Usage</h4>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600;">
                <span>{{ $usageGB }} GB</span>
                <span>{{ $limitGB }} GB</span>
            </div>
            <div style="width: 100%; height: 10px; background: #f1f1f1; border-radius: 5px; overflow: hidden;">
                <div style="width: {{ $percent }}%; height: 100%; background: {{ $color }}; transition: width 0.5s;"></div>
            </div>
            <p style="font-size: 0.8rem; color: #999; margin-top: 0.5rem;">{{ number_format($percent, 1) }}% Used</p>
        </div>
        @endif
    </div>

    <!-- Domain Migration Instructions -->
    <!-- Domain Status / Migration -->
    @if(isset($domain) && $domain)
        <div class="alert-box white-card" style="padding: 1.5rem; margin-bottom: 3rem; border-radius: 12px; box-shadow: var(--shadow); border-left: 5px solid {{ $domain->status == 'active' ? '#28a745' : '#ffc107' }};">
            <h4 style="color: var(--secondary); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-globe"></i> Your Website
                @if($domain->status == 'active')
                    <span style="font-size: 0.8rem; background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 20px;">Active</span>
                @else
                    <span style="font-size: 0.8rem; background: #fff3e0; color: #ef6c00; padding: 2px 8px; border-radius: 20px;">Pending Setup</span>
                @endif
            </h4>
            <div style="font-size: 1.2rem; font-weight: 600; margin-bottom: 0.5rem;">
                <a href="http://{{ $domain->domain_name }}" target="_blank" style="color: var(--primary); text-decoration: underline;">
                    {{ $domain->domain_name }} <i class="fas fa-external-link-alt" style="font-size: 0.8rem;"></i>
                </a>
            </div>
            @if($domain->status !== 'active')
                <p style="font-size: 0.9rem; color: #666;">
                    Please ensure your domain <strong>A Record</strong> points to <code>192.168.1.100</code> at your registrar.
                </p>
            @endif
        </div>
    @else
        <div class="alert-box domain-setup-alert">
            <h4 class="alert-title"><i class="fas fa-globe"></i> Domain Setup Required</h4>
            <p style="margin-bottom: 1rem;">To make your website accessible globally, you need a domain name.</p>
            
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="https://www.hostinger.com/domain-name-search" target="_blank" class="btn" style="background: #2196f3; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 500; text-decoration: none;">
                    <i class="fas fa-shopping-cart"></i> Buy Domain (Hostinger)
                </a>
                <a href="{{ route('domains.index') }}" class="btn" style="background: white; color: #2196f3; border: 1px solid #2196f3; padding: 10px 20px; border-radius: 8px; font-weight: 500; text-decoration: none;">
                    <i class="fas fa-exchange-alt"></i> I have a Domain
                </a>
            </div>
        </div>
    @endif

    <!-- Quick Links -->
    <div class="quick-links" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
        @php
            $hasDomain = Auth::user()->domains()->exists();
            $disabledClass = 'btn-disabled-feature';
            $tooltip = 'Please migrate a domain first to unlock this feature.';
        @endphp

        <!-- File Manager -->
        <div class="link-card white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); text-align: center; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1rem;">
                <i class="fas fa-folder fa-3x" style="color: {{ $hasDomain ? 'var(--primary)' : '#ccc' }};"></i>
            </div>
            <h3>File Manager</h3>
            <p style="margin-bottom: 1.5rem; color: #777;">Upload and manage your PHP files and projects.</p>
            <span title="{{ !$hasDomain ? $tooltip : '' }}" style="display: inline-block; margin-top: auto;">
                <a href="{{ route('dashboard.files') }}" class="btn-primary {{ !$hasDomain ? $disabledClass : '' }}" 
                   style="display: inline-block; padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                    Open File Manager
                </a>
            </span>
        </div>
        
        <!-- Databases -->
        <div class="link-card white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); text-align: center; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1rem;">
                <i class="fas fa-database fa-3x" style="color: {{ $hasDomain ? 'var(--secondary)' : '#ccc' }};"></i>
            </div>
            <h3>Databases</h3>
            <p style="margin-bottom: 1.5rem; color: #777;">Manage your MySQL databases.</p>
            <span title="{{ !$hasDomain ? $tooltip : '' }}" style="display: inline-block; margin-top: auto;">
                <a href="{{ route('databases.index') }}" class="btn-primary {{ !$hasDomain ? $disabledClass : '' }}" 
                   style="display: inline-block; padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                    Open Databases
                </a>
            </span>
        </div>

        <!-- Email Accounts -->
        <div class="link-card white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); text-align: center; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1rem;">
                <i class="fas fa-envelope fa-3x" style="color: {{ $hasDomain ? 'var(--primary)' : '#ccc' }};"></i>
            </div>
            <h3>Email Accounts</h3>
            <p style="margin-bottom: 1.5rem; color: #777;">Create and manage email addresses.</p>
             <span title="{{ !$hasDomain ? $tooltip : '' }}" style="display: inline-block; margin-top: auto;">
                <a href="{{ route('emails.index') }}" class="btn-primary {{ !$hasDomain ? $disabledClass : '' }}" 
                   style="display: inline-block; padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                    Manage Emails
                </a>
            </span>
        </div>

        <!-- GitHub Sync -->
        <div class="link-card white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); text-align: center; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1rem;">
                <i class="fab fa-github fa-3x" style="color: {{ $hasDomain ? '#333' : '#ccc' }};"></i>
            </div>
            <h3>GitHub Sync</h3>
            <p style="margin-bottom: 1.5rem; color: #777;">Auto-deploy from repository.</p>
             <span title="{{ !$hasDomain ? $tooltip : '' }}" style="display: inline-block; margin-top: auto;">
                <a href="{{ route('github.index') }}" class="btn-primary {{ !$hasDomain ? $disabledClass : '' }}" 
                   style="display: inline-block; padding: 0.5rem 1.5rem; font-size: 0.9rem;">
                    Configure
                </a>
            </span>
        </div>

        <!-- Settings (Always enabled) -->
        <div class="link-card white-card" style="padding: 2rem; border-radius: 20px; box-shadow: var(--shadow); text-align: center; display: flex; flex-direction: column; height: 100%;">
            <div style="margin-bottom: 1rem;">
                <i class="fas fa-cog fa-3x" style="color: var(--secondary);"></i>
            </div>
            <h3>Settings</h3>
            <p style="margin-bottom: 1.5rem; color: #777;">Password & Security.</p>
            <a href="{{ route('settings.index') }}" class="btn-primary" style="display: inline-block; padding: 0.5rem 1.5rem; font-size: 0.9rem; margin-top: auto;">Open Settings</a>
        </div>
    </div>
</section>
@endsection
