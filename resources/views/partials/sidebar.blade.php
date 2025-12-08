<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Hostoo Logo" class="logo-light" style="height: 40px; width: auto; object-fit: contain; object-position: left;">
            <img src="{{ asset('images/dark-logo.png') }}" alt="Hostoo Logo Dark" class="logo-dark" style="width: 140px; height: auto; display: none;">
            
            <!-- Collapsed Logos -->
            <img src="{{ asset('images/logo-collapsed.png') }}" alt="H" class="logo-collapsed-light" style="display: none; height: 40px; width: auto; margin: 0 auto;">
            <img src="{{ asset('images/dark-logo-collapsed.png') }}" alt="H" class="logo-collapsed-dark" style="display: none; height: 40px; width: auto; margin: 0 auto;">
        </div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-columns"></i> <span>Dashboard</span>
            </a>
        </li>
        
        <li>
            <a href="{{ route('domains.index') }}" class="{{ request()->routeIs('domains.*') ? 'active' : '' }}">
                <i class="fas fa-globe"></i> <span>Domains</span>
            </a>
        </li>

        @if(\Illuminate\Support\Facades\Auth::user()->domains()->exists())
        <li>
            <a href="{{ route('dashboard.files') }}" class="{{ request()->routeIs('dashboard.files') ? 'active' : '' }}">
                <i class="fas fa-folder-open"></i> <span>File Manager</span>
            </a>
        </li>
        <li>
            <a href="{{ route('databases.index') }}" class="{{ request()->routeIs('databases.*') ? 'active' : '' }}">
                <i class="fas fa-database"></i> <span>Databases</span>
            </a>
        </li>
        <li>
            <a href="{{ route('php.manager') }}" class="{{ request()->routeIs('php.*') ? 'active' : '' }}">
                <i class="fas fa-server"></i> <span>PHP Environment</span>
            </a>
        </li>
        <li>
            <a href="{{ route('emails.index') }}" class="{{ request()->routeIs('emails.*') ? 'active' : '' }}">
                <i class="fas fa-envelope"></i> <span>Email Accounts</span>
            </a>
        </li>
        @endif

        <li>
            <a href="{{ route('subscription.index') }}" class="{{ request()->routeIs('subscription.*') ? 'active' : '' }}">
                <i class="fas fa-crown"></i> <span>Subscriptions</span>
            </a>
        </li>
    </ul>
</div>
