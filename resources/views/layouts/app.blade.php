<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIGMA') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="antialiased">
    <div id="app" style="position: relative;">
        <button id="theme-toggle-btn" 
            style="position: absolute; 
                   top: 2rem; 
                   right: 2rem; 
                   background: rgba(0, 0, 0, 0.3); 
                   backdrop-filter: blur(8px); 
                   -webkit-backdrop-filter: blur(8px); 
                   border: 1px solid rgba(255, 255, 255, 0.1); 
                   cursor: pointer; 
                   color: #ffffff; 
                   padding: 0.75rem; 
                   display: flex; 
                   align-items: center; 
                   justify-content: center; 
                   border-radius: 0.75rem; 
                   transition: all 0.3s ease;" 
            title="Toggle Theme">
            <svg class="w-5 h-5" style="width: 20px; height: 20px;" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.536l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.828-2.828a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm.707-7071a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM9 4a1 1 0 011 1v1a1 1 0 11-2 0V5a1 1 0 011-1zm0 14a1 1 0 01-1-1v-1a1 1 0 112 0v1a1 1 0 01-1 1zm8-1a1 1 0 111 0 4 4 0 01-4 4 1 1 0 110-2 2 2 0 002-2zM3 15a1 1 0 11-2 0 4 4 0 014-4 1 1 0 110 2 2 2 0 00-2 2z" clip-rule="evenodd"></path>
            </svg>
        </button>

        @php
            $showSidebar = !request()->routeIs(['login', 'register']);
        @endphp

        @if ($showSidebar)
            <div class="dashboard-shell">
                <aside class="dashboard-sidebar">
                    <div class="dashboard-sidebar-inner">
                        <div style="display: flex; justify-content: center; align-items: center; margin-bottom: 0.5rem;">
                            <h2 class="dashboard-brand">S.I.G.M.A</h2>
                        </div>
                        <nav class="dashboard-nav">
                            <a href="{{ route('dashboard') }}" class="dashboard-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span>Dashboard</span>
                            </a>
                             <a href="{{ route('dashboard') }}" class="dashboard-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                                <span>Sensor</span>
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

                <main class="dashboard-main">
                    @yield('content')
                </main>
            </div>
        @else
            @yield('content')
        @endif
    </div>

    @stack('scripts')
</body>
</html>