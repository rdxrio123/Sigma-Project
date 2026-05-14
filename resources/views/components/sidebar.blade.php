<aside class="dashboard-sidebar" id="sidebar">
    <div class="dashboard-sidebar-inner">
        <div class="dashboard-sidebar-header">
            <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 0.5rem; flex: 1;">
                <h2 class="dashboard-brand">S.I.G.M.A</h2>
            </div>
            <button id="sidebar-toggle" class="sidebar-toggle" title="Toggle sidebar">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        <nav class="dashboard-nav">
            <a href="{{ route('dashboard') }}" class="dashboard-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                </svg>
                <span>Dashboard</span>
            </a>

            <div class="dashboard-nav-dropdown">
                <button class="dashboard-nav-link dropdown-toggle" type="button">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M6 3h12a1 1 0 011 1v4a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1zm0 12h12a1 1 0 011 1v4a1 1 0 01-1 1H6a1 1 0 01-1-1v-4a1 1 0 011-1zM4 10h16a1 1 0 010 2H4a1 1 0 010-2z"></path>
                    </svg>
                    <span>Sensor</span>
                    <svg class="w-4 h-4 dropdown-icon" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
                <div class="dropdown-menu">
                    <a href="#" class="dropdown-item">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6 4h12v4H6V4zm2 7h8v2H8v-2zm-2 3h12v4H6v-4z"></path>
                        </svg>
                        <span>Sensor 1</span>
                    </a>
                    <a href="#" class="dropdown-item">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 4.84 6.46 11.45 6.74 11.74a1 1 0 001.52 0C12.54 20.45 19 13.84 19 9c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"></path>
                        </svg>
                        <span>Sensor 2</span>
                    </a>
                </div>
            </div>

       
            <a href="#" class="dashboard-nav-link">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path>
                </svg>
                <span>Histori</span>
            </a>
            <a href="#" class="dashboard-nav-link">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10.666a1 1 0 11-1.64-1.118L9.687 10H5a1 1 0 01-.82-1.573l7-10.666a1 1 0 011.12-.385z" clip-rule="evenodd"></path>
                </svg>
                <span>Pengaturan Controller</span>
            </a>
                 <a href="{{ route('alarm') }}" class="dashboard-nav-link {{ request()->routeIs('alarm') ? 'active' : '' }}">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2L3 7v11a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V7l-7-5z"></path>
                </svg>
                <span>Alarm</span>
            </a>
            <a href="#" class="dashboard-nav-link">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c1.56.379 2.978-1.56 2.978-2.978a1.532 1.532 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.532 1.532 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                </svg>
                <span>Pengaturan</span>
            </a>
        </nav>
    </div>
</aside>
