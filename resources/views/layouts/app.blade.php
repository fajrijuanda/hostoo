<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel Hosting') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Volkhov:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=13.0">
    @stack('styles')
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @include('components.ui-feedback')
    @php
        $user = Auth::user();
        $hasActiveSub = $user && ($user->hasActiveSubscription() || $user->isAdmin());
        // Profile page should NOT use sidebar layout if user is not subscribed
        // Profile page should NOT use sidebar layout if user is not subscribed
        $isProtected = $hasActiveSub && (
            request()->is('dashboard*') || 
            request()->is('databases*') || 
            request()->is('files*') || 
            request()->is('emails*') || 
            request()->is('domains*') || 
            request()->is('subscription*') || 
            request()->is('php-manager*') || 
            request()->is('github*') || 
            request()->is('profile*') || 
            request()->is('plan*') || 
            request()->is('settings*') || 
            request()->is('admin*')
        );
        
        // Ensure profile page uses container layout if not subscribed
        if ((request()->is('profile*') || request()->is('settings*')) && !$hasActiveSub) {
            $isProtected = false;
        }
    @endphp

    @if($hasActiveSub)
        @if(Auth::user()->isAdmin())
            @include('partials.admin-sidebar')
        @else
            @include('partials.sidebar')
        @endif
    @endif

    <div class="{{ $isProtected ? 'main-with-sidebar' : 'container' }}">
        <!-- Header -->
        @include('partials.navbar')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        @if(!$hasActiveSub)
            @include('partials.footer')
        @endif
    </div>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>
    <script src="{{ asset('js/navbar.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/cursor-clouds.js') }}"></script>

    <script src="{{ asset('js/back-to-top.js') }}"></script>
    @stack('scripts')
</body>
</html>
