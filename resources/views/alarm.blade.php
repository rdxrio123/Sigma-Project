@extends('layouts.dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('dashboard-content')
        <div class="dashboard-container">
            <header class="dashboard-header">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-[#7c4c2e]">SIGMA Alarm</p>
                    <h1 class="dashboard-title">Halaman Alarm</h1>
                    <p class="dashboard-description">Lihat peringatan sistem, kondisi sensor, dan status alarm terkini di sini.</p>
                </div>
            </header>

            <section class="dashboard-section">
                <div class="dashboard-section-header">
                    <div>
                        <h2 class="dashboard-section-title">Daftar Alarm</h2>
                        <p class="dashboard-section-subtitle">Semua notifikasi peringatan terpusat dalam satu tampilan.</p>
                    </div>
                </div>

                <div class="dashboard-info-grid">
                    @foreach ($alarms as $alarm)
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">{{ $alarm['status'] }}</p>
                            <h3 class="mt-3 text-xl font-semibold">{{ $alarm['title'] }}</h3>
                            <p class="mt-2 text-sm text-[#5d4331]">{{ $alarm['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

<script src="{{ asset('js/theme.js') }}"></script>
@endsection