@extends('layouts.app')

@section('content')
<div class="dashboard-shell">
    @include('components.sidebar')

    <main class="dashboard-main">
        @yield('dashboard-content')
    </main>
</div>
@endsection
