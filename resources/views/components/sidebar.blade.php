<aside class="sidebar" id="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-header">
            <div class="sidebar-brand-wrap">
                <h2 class="sidebar-brand">SIGMA</h2>
                <p class="sidebar-subtitle">Monitoring Getaran</p>
            </div>
            <button id="sidebar-close-toggle" class="sidebar-close-toggle" type="button" aria-label="Tutup sidebar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('alarm') }}" class="sidebar-link {{ request()->routeIs('alarm') ? 'active' : '' }}">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Alarm</span>
            </a>
            <a href="#gps-map" class="sidebar-link">
                <i class="fa-solid fa-location-dot"></i>
                <span>GPS</span>
            </a>
            <a href="#accelChart" class="sidebar-link">
                <i class="fa-solid fa-chart-line"></i>
                <span>Grafik</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <span class="sidebar-user">Halo, {{ auth()->user()?->name ?? auth()->user()?->email ?? 'User' }}</span>
            <button id="theme-toggle-desktop" class="btn-logout-sidebar" style="background: var(--sigma-surface-2); color: var(--sigma-text); border: 1px solid var(--sigma-border); margin-bottom: 0.5rem;" type="button">
                <i class="fa-solid fa-moon"></i>
                <span>Dark Mode</span>
            </button>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout-sidebar">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
