@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section('content')
        <div class="dashboard-container">
            <header class="dashboard-header">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-[#7c4c2e]">SIGMA Monitoring</p>
                    <h1 class="dashboard-title">SELAMAT DATANG DI SIGMA</h1>
                    <p class="dashboard-description">Pantau Getaran Gempa Dengan Sensor ADXL345 dan Menentukan Lokasi Secara Real Time</p>
                </div>
                <div class="dashboard-stats-grid">
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.em] text-[#7c5a44]">Magnitudo Getaran</p>
                        <p class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['magnitude'], 2) }}</p>
                    </div>
                </div>
            </header>

            <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                <section class="dashboard-section bg-soft">
                    <div class="dashboard-section-header">
                        <div>
                            <h2 class="dashboard-section-title">GPS NEO-6M</h2>
                            <p class="dashboard-section-subtitle">Titik koordinat saat ini ditampilkan pada peta.</p>
                        </div>
                        <span class="dashboard-chip">Actively tracking</span>
                    </div>

                    <div id="gps-map" class="dashboard-map"></div>

                    <div class="dashboard-info-grid">
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Latitude</p>
                            <p class="mt-2 text-base font-semibold">{{ $gps['latitude'] }}</p>
                        </div>
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Longitude</p>
                            <p class="mt-2 text-base font-semibold">{{ $gps['longitude'] }}</p>
                        </div>
                    </div>
                </section>

                <section class="dashboard-section">
                    <div class="dashboard-accelerometer">
                        <div class="dashboard-accel-header">
                            <div class="dashboard-accel-header-inner">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Nilai X / Y / Z</p>
                                    <p class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['x'], 2) }} / {{ number_format($currentAccel['y'], 2) }} / {{ number_format($currentAccel['z'], 2) }}</p>
                                </div>
                                <div class="dashboard-accel-highlight">
                                    <p class="text-xs uppercase tracking-[0.2em]">Terkini</p>
                                    <p class="mt-1 text-xl font-semibold">{{ $currentAccel['time'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-chart-card">
                            <canvas id="accelChart" class="h-[260px] w-full"></canvas>
                        </div>

                        <div class="dashboard-score-grid">
                            <div class="dashboard-score-card">
                                <p class="dashboard-score-card-title">Magnitudo Maksimum</p>
                                <p class="dashboard-score-card-value">{{ number_format(max(array_column($accelSamples, 'magnitude')), 2) }}</p>
                            </div>
                            <div class="dashboard-score-card">
                                <p class="dashboard-score-card-title">Rata-rata getaran</p>
                                <p class="dashboard-score-card-value">{{ number_format(array_sum(array_column($accelSamples, 'magnitude')) / count($accelSamples), 2) }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/theme.js') }}"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-o9N1j7kFqP1x2X/Lb+Q1Nn5Hnq0xearA/eCht+YLH6w=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-o3p3f3l0Qp5a4gC6KxEJQiqKfxT8IL4P6r4+4xXw4oQ=" crossorigin=""></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const gps = {
            lat: parseFloat('{{ $gps['latitude'] }}'),
            lng: parseFloat('{{ $gps['longitude'] }}'),
        };

        const map = L.map('gps-map', {
            center: [gps.lat, gps.lng],
            zoom: 13,
            scrollWheelZoom: false,
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        L.marker([gps.lat, gps.lng]).addTo(map)
            .bindPopup(`GPS NEO-6M<br>Lat: ${gps.lat}<br>Lng: ${gps.lng}`)
            .openPopup();

        const accelData = {
            labels: @json(array_column($accelSamples, 'time')),
            datasets: [{
                label: 'Magnitudo (g)',
                data: @json(array_column($accelSamples, 'magnitude')),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.18)',
                fill: true,
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
            }],
        };

        new Chart(document.getElementById('accelChart'), {
            type: 'line',
            data: accelData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280' },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(107, 114, 128, 0.15)' },
                        ticks: { color: '#6b7280' },
                    },
                },
            },
        });
    });
</script>
@endsection