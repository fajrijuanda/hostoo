<header id="navbar" class="navbar-custom">
    @if(!Auth::check() || (!Auth::user()->hasActiveSubscription() && !Auth::user()->isAdmin()) || request()->is('/'))
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Hostoo Logo" class="logo-light" style="height: 48px;">
        <img src="{{ asset('images/dark-logo.png') }}" alt="Hostoo Logo Dark" class="logo-dark" style="height: 48px; display: none;">
    </div>
    @endif
    <div class="header-content" style="flex: 1; display: flex; justify-content: space-between; align-items: center;">
        
        <!-- Search Bar (Only Auth + Active Sub or Admin) -->
        @auth
            @if((Auth::user()->hasActiveSubscription() || Auth::user()->isAdmin()) && !request()->is('/'))
            <button id="sidebarToggle" onclick="toggleSidebar()" style="background:none; border:none; font-size: 1.5rem; cursor:pointer; color:var(--secondary); margin-right: 15px;">
                <i class="fas fa-bars"></i>
            </button>
            <div class="dashboard-search">
                <form action="#" method="GET">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search menu..." name="q" id="menuSearchInput" autocomplete="off">
                        <div id="searchResults"></div>
                    </div>
                </form>
            </div>
            @endif
        @else
             <!-- Empty div to push nav to center with auto margins if needed, or just let space-between handle it -->
        @endauth

        <!-- Centered Nav Links -->
        <nav style="display: flex; justify-content: center; flex: 1;">
            <ul class="nav-links" style="display: flex; gap: 2rem; margin: 0; padding: 0; list-style: none;">
                @if(!Auth::check() || (!Auth::user()->hasActiveSubscription() && !Auth::user()->isAdmin()) || request()->is('/'))
                    <li>
                        @if(Request::is('/'))
                            <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" style="color: var(--text-color); text-decoration: none; font-weight: 500;">Home</a>
                        @else
                            <a href="{{ url('/') }}" style="color: var(--text-color); text-decoration: none; font-weight: 500;">Home</a>
                        @endif
                    </li>
                    <li><a href="{{ url('/#features') }}" style="color: var(--text-color); text-decoration: none; font-weight: 500;">Features</a></li>
                    <li><a href="{{ url('/#plans') }}" style="color: var(--text-color); text-decoration: none; font-weight: 500;">Hosting Plans</a></li>
                    @auth
                        @if(request()->is('/'))
                            <li>
                                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('dashboard') }}" style="color: var(--text-color); text-decoration: none; font-weight: 500;">Dashboard</a>
                            </li>
                        @endif
                    @endauth
                @endif
            </ul>
        </nav>
    
        <div class="auth-buttons" style="display: flex; align-items: center; gap: 15px; margin-left: auto;">
            <!-- Dark Mode Toggle (Common) -->
            <button id="darkModeToggle" onclick="toggleDarkMode()" style="background: none; border: none; cursor: pointer; color: var(--text-color); font-size: 1.2rem; padding: 5px;">
                <i class="fas fa-moon"></i>
            </button>

        @auth
            <div class="user-menu" style="position: relative; display: inline-block;">
                <button onclick="toggleDropdown()" style="background: none; border: none; cursor: pointer; display: flex; align-items: center; gap: 10px; font-weight: 600; color: var(--secondary); box-shadow: none;">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                    @else
                        <div style="width: 35px; height: 35px; background: #eee; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="color: #ccc;"></i>
                        </div>
                    @endif
                    <span>{{ Auth::user()->name }}</span>
                    <i class="fas fa-chevron-down" style="font-size: 0.8rem;"></i>
                </button>

                <!-- Dropdown (Unchanged) -->
                <div id="userDropdown" style="display: none; position: absolute; right: 0; background: white; min-width: 200px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); border-radius: 10px; z-index: 1000; overflow: hidden; margin-top: 10px;">
                    <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #5E6282; text-decoration: none; border-bottom: 1px solid #f1f1f1; transition: background 0.2s;">
                        <i class="fas fa-user-circle fa-fw" style="font-size: 1.1rem; color: #5E6282;"></i> Profile
                    </a>
                    <a href="{{ route('settings.index') }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #5E6282; text-decoration: none; border-bottom: 1px solid #f1f1f1; transition: background 0.2s;">
                        <i class="fas fa-cog fa-fw" style="font-size: 1.1rem; color: #5E6282;"></i> Settings
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;" onsubmit="confirmLogout(event)">
                        @csrf
                        <button type="submit" style="width: 100%; text-align: left; display: flex; align-items: center; justify-content: flex-start; gap: 12px; padding: 12px 20px; background: none; border: none; cursor: pointer; color: #dc3545; font-weight: 600; box-shadow: none; font-family: inherit; font-size: 1rem; transition: background 0.2s;">
                            <i class="fas fa-sign-out-alt fa-fw" style="font-size: 1.1rem;"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-auth" style="border:none; background: var(--primary); color: white; padding: 10px 25px; border-radius: 50px; text-decoration: none; font-weight: 600;">Login</a>
        @endauth
    </div>
</header>

<style>
    /* Search Results Dropdown */
    #searchResults {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        margin-top: 10px;
        z-index: 99999; /* Highest priority to overlap animations */
        max-height: 300px;
        overflow-y: auto;
        display: none; /* Hidden by default */
        padding: 5px 0;
        text-align: left;
    }
    
    body.dark-mode #searchResults {
        background: #2d2d2d;
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    }
    
    .search-result-item {
        display: flex;
        align-items: center;
        gap: 15px; /* Increased gap */
        padding: 12px 20px; /* Increased padding */
        color: var(--text-color);
        text-decoration: none;
        transition: background 0.2s;
        font-size: 0.95rem;
        position: relative;
    }
    
    .search-result-item:hover, .search-result-item.search-selected {
        background: #f1f1f1; /* Lighter grey */
    }
    
    body.dark-mode .search-result-item:hover, 
    body.dark-mode .search-result-item.search-selected {
        background: #2a2a2a;
    }
    
    .search-result-item i {
        color: var(--secondary);
        width: 30px; /* Wider container for icon */
        text-align: center;
        flex-shrink: 0; /* Prevent icon shrinking */
        font-size: 1.1rem;
    }
    
    .no-results {
        padding: 15px;
        color: #999;
        font-size: 0.9rem;
        text-align: center;
    }

    /* Input styling adjustment to handle relative positioning for dropdown */
    .dashboard-search .search-wrapper {
        position: relative; 
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('menuSearchInput');
        const searchResults = document.getElementById('searchResults');
        
        if (!searchInput) return;

        // Index the Sidebar Menu
        let menuItems = [];
        const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
        
        sidebarLinks.forEach(link => {
            const text = link.querySelector('span') ? link.querySelector('span').innerText.trim() : link.innerText.trim();
            const href = link.getAttribute('href');
            // Extract icon class
            const iconEl = link.querySelector('i');
            const iconClass = iconEl ? iconEl.className : 'fas fa-link';
            
            // Only add if visible and has text
            if (text && href && href !== '#') {
                menuItems.push({ text, href, icon: iconClass });
            }
        });

        // Search Logic
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase().trim();
            
            if (query.length === 0) {
                searchResults.style.display = 'none';
                return;
            }
            
            const filtered = menuItems.filter(item => item.text.toLowerCase().includes(query));
            
            renderResults(filtered);
        });
        
        // Render Function
        function renderResults(items) {
            searchResults.innerHTML = '';
            
            if (items.length === 0) {
                searchResults.innerHTML = '<div class="no-results">No menu items found.</div>';
            } else {
                items.forEach((item, index) => {
                    const a = document.createElement('a');
                    a.href = item.href;
                    a.className = 'search-result-item' + (index === 0 ? ' search-selected' : ''); // Highlight first
                    a.innerHTML = `<i class="${item.icon}"></i> <span>${item.text}</span>`;
                    searchResults.appendChild(a);
                });
            }
            
            searchResults.style.display = 'block';
        }
        
        // Keyboard Navigation (Enter to go to first result)
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstResult = searchResults.querySelector('.search-result-item');
                if (firstResult) {
                    window.location.href = firstResult.getAttribute('href');
                }
            }
        });

        // Hide when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
        
        // Show again if focused
        searchInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0) {
                searchResults.style.display = 'block';
            }
        });
    });

    // Dark Mode Logic
    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
        const icon = document.querySelector('#darkModeToggle i');
        
        if (document.body.classList.contains('dark-mode')) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        }
    }

    // Check Preference
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        const icon = document.querySelector('#darkModeToggle i');
        if(icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    }
    
    // Dropdown Logic
    function toggleDropdown() {
        var dropdown = document.getElementById("userDropdown");
        if (dropdown.style.display === "none" || dropdown.style.display === "") {
            dropdown.style.display = "block";
            // Close search results if open
             const searchResults = document.getElementById('searchResults');
             if(searchResults) searchResults.style.display = 'none';
        } else {
            dropdown.style.display = "none";
        }
    }
    
    window.onclick = function(event) {
        if (!event.target.closest('.user-menu')) {
            var dropdown = document.getElementById("userDropdown");
            if (dropdown) {
                dropdown.style.display = "none";
            }
        }
    }


</script>
