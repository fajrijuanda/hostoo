<!-- Page Loader -->
<div id="app-loader">
    <div class="loader-plane-container">
        <img src="{{ Vite::asset('resources/images/cursor_plane.png') }}" alt="Loading..." class="loader-plane">
        <div class="loader-clouds">
            <!-- Dynamic CSS Clouds -->
            <div class="loader-cloud" style="top: 10px; left: 10px; animation-delay: 0s; transform: scale(0.8);"></div>
            <div class="loader-cloud" style="top: 40px; right: 10px; animation-delay: 1s; transform: scale(0.6);"></div>
            <div class="loader-cloud" style="bottom: 10px; left: 30px; animation-delay: 2s; transform: scale(0.9);"></div>
            <div class="loader-cloud" style="bottom: 50px; right: 40px; animation-delay: 1.5s; transform: scale(0.7);"></div>
        </div>
    </div>
    <div class="loader-text">Loading Hostoo...</div>
</div>

<!-- Custom Plane Alert Modal -->
<div id="custom-alert-overlay">
    <div class="plane-alert-box">

        <div class="plane-alert-icon" id="alert-icon">
            <!-- Icon injected by JS -->
            ✈️
        </div>
        <h3 class="plane-alert-title" id="alert-title">Title</h3>
        <p class="plane-alert-text" id="alert-text">Message goes here...</p>
        
        <div class="plane-alert-actions" id="alert-actions">
            <!-- Buttons injected by JS -->
            <button class="plane-alert-btn btn-confirm" onclick="Hostoo.closeAlert()">OK</button>
        </div>
    </div>
</div>

<script>
    // Hostoo UI Namespace
    window.Hostoo = {
        // --- Loader Logic ---
        hideLoader: function() {
            const loader = document.getElementById('app-loader');
            if(loader) {
                loader.classList.add('hidden');
                setTimeout(() => loader.style.display = 'none', 500);
            }
        },
        showLoader: function() {
            const loader = document.getElementById('app-loader');
            if(loader) {
                loader.style.display = 'flex';
                // force reflow
                void loader.offsetWidth;
                loader.classList.remove('hidden');
            }
        },

        // --- Alert Logic ---
        alert: function(options) {
            return new Promise((resolve) => {
                const title = options.title || 'Alert';
                const text = options.text || '';
                const type = options.type || 'info'; // success, error, warning, info
                const showCancel = options.showCancel || false;
                const confirmText = options.confirmText || 'OK';
                
                // Set Content
                document.getElementById('alert-title').innerText = title;
                document.getElementById('alert-text').innerText = text;
                
                // Set Icon based on type
                const iconContainer = document.getElementById('alert-icon');
                let icon = '✈️';
                let color = '#007bff';
                if(type === 'success') { icon = '✅'; color = '#28a745'; }
                if(type === 'error') { icon = '❌'; color = '#dc3545'; }
                if(type === 'warning') { icon = '⚠️'; color = '#ffc107'; }
                
                iconContainer.innerHTML = icon;
                iconContainer.style.color = color;
                iconContainer.style.background = color + '20'; // 20% opacity bg

                // Set Buttons
                const actionsContainer = document.getElementById('alert-actions');
                actionsContainer.innerHTML = '';

                if(showCancel) {
                    const cancelBtn = document.createElement('button');
                    cancelBtn.className = 'plane-alert-btn btn-cancel';
                    cancelBtn.innerText = 'Cancel';
                    cancelBtn.onclick = () => {
                        Hostoo.closeAlert();
                        resolve(false);
                    };
                    actionsContainer.appendChild(cancelBtn);
                }

                const confirmBtn = document.createElement('button');
                confirmBtn.className = 'plane-alert-btn btn-confirm';
                confirmBtn.innerText = confirmText;
                confirmBtn.style.background = color; // match theme
                confirmBtn.onclick = () => {
                    Hostoo.closeAlert();
                    resolve(true);
                };
                actionsContainer.appendChild(confirmBtn);

                // Show Modal
                const overlay = document.getElementById('custom-alert-overlay');
                overlay.style.display = 'flex';
                void overlay.offsetWidth; // force reflow
                overlay.classList.add('show');
            });
        },

        closeAlert: function() {
            const overlay = document.getElementById('custom-alert-overlay');
            overlay.classList.remove('show');
            setTimeout(() => overlay.style.display = 'none', 300);
        }
    };

    // Auto-hide loader on page load
    window.addEventListener('load', () => {
         setTimeout(Hostoo.hideLoader, 800); // slight delay for effect

         // Auto-Flash Messages
         @if(session('success'))
            Hostoo.alert({
                title: 'Success',
                text: "{{ session('success') }}",
                type: 'success'
            });
         @endif

         @if(session('error'))
            Hostoo.alert({
                title: 'Error',
                text: "{{ session('error') }}",
                type: 'error'
            });
         @endif
         
         @if(session('warning'))
            Hostoo.alert({
                title: 'Warning',
                text: "{{ session('warning') }}",
                type: 'warning'
            });
         @endif

         @if(session('status'))
            Hostoo.alert({
                title: 'Info',
                text: "{{ session('status') }}",
                type: 'info'
            });
         @endif

         @if($errors->any())
             let errorMsg = "";
             @foreach($errors->all() as $error)
                 errorMsg += "{{ $error }}\n";
             @endforeach
             Hostoo.alert({
                 title: 'Validation Error',
                 text: errorMsg,
                 type: 'error'
             });
         @endif
    });

    // Global Logout Confirmation
    function confirmLogout(event) {
        event.preventDefault();
        const form = event.target;
        
        Hostoo.alert({
            title: 'Logout?',
            text: 'Are you sure you want to sign out?',
            type: 'warning',
            showCancel: true,
            confirmText: 'Yes, Logout'
        }).then(confirmed => {
            if(confirmed) {
                form.submit();
            }
        });
    }
</script>
