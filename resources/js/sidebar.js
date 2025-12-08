window.toggleSidebar = function () {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-with-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const isMobile = window.innerWidth <= 992;

    if (isMobile) {
        // Mobile: Toggle Overlay Mode
        if (sidebar) sidebar.classList.toggle('mobile-active');
        if (overlay) overlay.classList.toggle('active');
    } else {
        // Desktop: Toggle Collapse Mode
        if (sidebar) sidebar.classList.toggle('collapsed');
        if (mainContent) mainContent.classList.toggle('collapsed');

        // Save state
        if (sidebar) {
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed);
        }
    }
}

// Restore Sidebar State on Load
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-with-sidebar');
    const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
    const isMobile = window.innerWidth <= 992;

    if (!isMobile && isCollapsed && sidebar && mainContent) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('collapsed');
    }

    // Handle overlay click
    const overlay = document.getElementById('sidebarOverlay');
    if (overlay) {
        overlay.addEventListener('click', () => {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) sidebar.classList.remove('mobile-active');
            overlay.classList.remove('active');
        });
    }
});
