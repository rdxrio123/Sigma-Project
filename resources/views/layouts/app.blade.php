<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIGMA') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    @stack('styles')
</head>
<body class="antialiased">
    <div id="app" style="position: relative;">
        <button id="theme-toggle-btn" class="theme-toggle-btn"
            style="position: absolute; 
                   top: 2rem; 
                   right: 2rem; 
                   z-index: 9999;
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

        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>