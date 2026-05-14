@extends('layouts.app')

@section('content')
<div class="dashboard-shell">
    <button id="mobile-sidebar-toggle" class="mobile-sidebar-toggle" title="Toggle sidebar">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
        </svg>
    </button>
    @include('components.sidebar')

    <main class="dashboard-main">
        @yield('dashboard-content')
    </main>
</div>
@endsection
