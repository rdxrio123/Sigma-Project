document.addEventListener('DOMContentLoaded', function() {
    // Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const mobileSidebarToggle = document.getElementById('mobile-sidebar-toggle');

    if (sidebarToggle && sidebar) {
        // Check if sidebar was previously collapsed
        const isSidebarCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isSidebarCollapsed) {
            sidebar.classList.add('collapsed');
        }

        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            const isNowCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isNowCollapsed);
        });
    }

    // Mobile Sidebar Toggle
    if (mobileSidebarToggle && sidebar) {
        mobileSidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('mobile-visible');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 1024 && !e.target.closest('.dashboard-sidebar') && !e.target.closest('.mobile-sidebar-toggle')) {
                sidebar.classList.remove('mobile-visible');
            }
        });

        // Close sidebar when clicking on a nav link on mobile
        const navLinks = document.querySelectorAll('.dashboard-nav-link:not(.dropdown-toggle), .dropdown-item');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('mobile-visible');
                }
            });
        });
    }

    // Dropdown Toggle Functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');

    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.dashboard-nav-dropdown');
            
            // Close other dropdowns
            document.querySelectorAll('.dashboard-nav-dropdown').forEach(dropdown => {
                if (dropdown !== parent) {
                    dropdown.classList.remove('open');
                    const btn = dropdown.querySelector('.dropdown-toggle');
                    btn.classList.remove('active');
                }
            });

            // Toggle current dropdown
            parent.classList.toggle('open');
            this.classList.toggle('active');
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dashboard-nav-dropdown')) {
            document.querySelectorAll('.dashboard-nav-dropdown').forEach(dropdown => {
                dropdown.classList.remove('open');
                const btn = dropdown.querySelector('.dropdown-toggle');
                btn.classList.remove('active');
            });
        }
    });

    // Handle window resize for responsive behavior
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            sidebar.classList.remove('mobile-visible', 'mobile-hidden');
        }
    });
});
