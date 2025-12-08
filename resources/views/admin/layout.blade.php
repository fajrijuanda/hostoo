<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Hostoo</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=13.0">
    <style>
        body { background-color: #f4f6f9; }
        /* Admin specific overrides if needed, but rely on layout.css for layout */
    </style>
</head>
<body>
    @include('components.ui-feedback')

    @include('partials.admin-sidebar')

    <div class="main-with-sidebar">
        <!-- Admin Header with Toggle -->
        <header style="margin-bottom: 3.5rem; display: flex; align-items: center; justify-content: space-between;">
             <button id="sidebarToggle" onclick="toggleSidebar()" style="background:none; border:none; font-size: 1.5rem; cursor:pointer; color:var(--secondary);">
                <i class="fas fa-bars"></i>
            </button>
            <div style="font-weight: 600; font-size: 1.1rem; color: var(--secondary);">
                Admin Panel <span style="font-weight: 400; color: #888;">| {{ Auth::user()->name }}</span>
            </div>
        </header>

        @yield('content')
    </div>

    <div id="sidebarOverlay" class="sidebar-overlay"></div>

    <script src="{{ asset('js/cursor-clouds.js') }}"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>
</html>
