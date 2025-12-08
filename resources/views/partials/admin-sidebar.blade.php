<div class="sidebar" style="display: flex; flex-direction: column;">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ url('/') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Hostoo Logo" class="logo-light" style="height: 40px; width: auto; object-fit: contain; object-position: left;">
                <img src="{{ asset('images/dark-logo.png') }}" alt="Hostoo Logo Dark" class="logo-dark" style="width: 140px; height: auto; display: none;">

                <!-- Collapsed Logos -->
                <img src="{{ asset('images/logo-collapsed.png') }}" alt="H" class="logo-collapsed-light" style="display: none; height: 40px; width: auto; margin: 0 auto;">
                <img src="{{ asset('images/dark-logo-collapsed.png') }}" alt="H" class="logo-collapsed-dark" style="display: none; height: 40px; width: auto; margin: 0 auto;">
            </a>
        </div>
    </div>
    <ul class="sidebar-menu" style="flex: 1;">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.subscriptions') }}" class="{{ request()->routeIs('admin.subscriptions') ? 'active' : '' }}">
                <i class="fas fa-list"></i> <span>Subscriptions</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.plans.index') }}" class="{{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> <span>Hosting Plans</span>
            </a>
        </li>
    </ul>

    <!-- Logout at Bottom -->
    <div style="padding: 0 0 2rem 0;">
        <form action="{{ route('logout') }}" method="POST" onsubmit="confirmLogout(event)">
            @csrf
            <button type="submit" class="sidebar-logout-btn">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </button>
        </form>
    </div>
</div>
