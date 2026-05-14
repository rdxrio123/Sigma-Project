@extends('layouts.dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-o9N1j7kFqP1x2X/Lb+Q1Nn5Hnq0xearA/eCht+YLH6w=" crossorigin="" />
@endpush

@section('dashboard-content')
        <div class="dashboard-container">
            <header class="dashboard-header">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-[#7c4c2e]">SIGMA Monitoring</p>
                    <h1 class="dashboard-title">SELAMAT DATANG DI SIGMA</h1>
                    <p class="dashboard-description">Pantau Getaran Gempa Dengan Sensor ADXL345 dan Menentukan Lokasi Secara Real Time</p>
                </div>
                <div class="dashboard-stats-grid">
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Status Sinkron</p>
                        <p class="mt-3 text-base font-semibold text-emerald-700">Live Update Aktif</p>
                    </div>
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Magnitudo Getaran</p>
                        <p id="currentMagnitude" class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['magnitude'], 2) }}</p>
                    </div>
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Sumbu X</p>
                        <p id="currentX" class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['x'], 2) }}</p>
                    </div>
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Sumbu Y</p>
                        <p id="currentY" class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['y'], 2) }}</p>
                    </div>
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Sumbu Z</p>
                        <p id="currentZ" class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['z'], 2) }}</p>
                    </div>
                    <div class="dashboard-card">
                        <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Update Terakhir</p>
                        <p id="lastUpdatedAt" class="mt-3 text-base font-semibold leading-6">{{ $lastUpdatedAt ?? '--' }}</p>
                    </div>
                </div>
            </header>

            <div class="grid gap-6 lg:grid-cols-2">
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
                            <p id="gpsLatitude" class="mt-2 text-base font-semibold">{{ number_format($gps['latitude'], 7) }}</p>
                        </div>
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Longitude</p>
                            <p id="gpsLongitude" class="mt-2 text-base font-semibold">{{ number_format($gps['longitude'], 7) }}</p>
                        </div>
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Altitude</p>
                            <p id="gpsAltitude" class="mt-2 text-base font-semibold">{{ number_format($gps['altitude'], 2) }} m</p>
                        </div>
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Satellites</p>
                            <p id="gpsSatellites" class="mt-2 text-base font-semibold">{{ $gps['satellites'] }}</p>
                        </div>
                        <div class="dashboard-info-card">
                            <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Status</p>
                            <p id="gpsStatus" class="mt-2 text-base font-semibold">{{ $gps['status'] }}</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-[#7c5a44]">Update terakhir: <span id="gpsRecordedAt">{{ $gps['recorded_at'] }}</span></p>
                </section>

                <section class="dashboard-section">
                    <div class="dashboard-accelerometer">
                        <div class="dashboard-accel-header">
                            <div class="dashboard-accel-header-inner">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-[#7c5a44]">Nilai X / Y / Z</p>
                                    <p id="currentAxes" class="mt-3 text-2xl font-semibold">{{ number_format($currentAccel['x'], 2) }} / {{ number_format($currentAccel['y'], 2) }} / {{ number_format($currentAccel['z'], 2) }}</p>
                                </div>
                                <div class="dashboard-accel-highlight">
                                    <p class="text-xs uppercase tracking-[0.2em]">Realtime</p>
                                    <p id="currentAccelTime" class="mt-1 text-xl font-semibold">{{ $currentAccel['time'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-chart-card">
                            <div id="accelChart" class="h-[260px] w-full"></div>
                        </div>

                        <div class="dashboard-score-grid">
                            <div class="dashboard-score-card">
                                <p class="dashboard-score-card-title">Magnitudo Maksimum</p>
                                <p id="magnitudeMaximum" class="dashboard-score-card-value">{{ number_format($summary['maximum'], 2) }}</p>
                            </div>
                            <div class="dashboard-score-card">
                                <p class="dashboard-score-card-title">Rata-rata getaran</p>
                                <p id="magnitudeAverage" class="dashboard-score-card-value">{{ number_format($summary['average'], 2) }}</p>
                            </div>
                            <div class="dashboard-score-card">
                                <p class="dashboard-score-card-title">Jumlah sampel</p>
                                <p id="sampleCount" class="dashboard-score-card-value">{{ $summary['count'] }}</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/theme.js') }}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-o3p3f3l0Qp5a4gC6KxEJQiqKfxT8IL4P6r4+4xXw4oQ=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initialDashboardData = {
            gps: @json($gps),
            currentAccel: @json($currentAccel),
            accelSamples: @json($accelSamples),
            summary: @json($summary),
            lastUpdatedAt: @json($lastUpdatedAt),
        };
        const dashboardDataUrl = @json($dashboardDataUrl);
        const dashboardRefreshIntervalMs = 2000;

        let accelChart = null;
        const mapState = {
            map: null,
            marker: null,
        };

        function formatNumber(value, digits = 2) {
            const numericValue = Number(value);

            if (!Number.isFinite(numericValue)) {
                return (0).toFixed(digits);
            }

            return numericValue.toFixed(digits);
        }

        function setText(id, value) {
            const element = document.getElementById(id);

            if (element) {
                element.textContent = value;
            }
        }

        function buildChartOptions(samples) {
            return {
                chart: {
                    type: 'line',
                    height: 260,
                    toolbar: {
                        show: false,
                    },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 600,
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350,
                        },
                    },
                    background: 'transparent',
                    foreColor: '#6b7280',
                },
                series: [
                    {
                        name: 'X',
                        data: samples.map((sample) => sample.x),
                    },
                    {
                        name: 'Y',
                        data: samples.map((sample) => sample.y),
                    },
                    {
                        name: 'Z',
                        data: samples.map((sample) => sample.z),
                    },
                    {
                        name: 'Magnitudo',
                        data: samples.map((sample) => sample.magnitude),
                    },
                ],
                xaxis: {
                    categories: samples.map((sample) => sample.time),
                    labels: {
                        style: {
                            colors: '#6b7280',
                        },
                    },
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#6b7280',
                        },
                    },
                },
                stroke: {
                    curve: 'smooth',
                    width: [3, 3, 3, 4],
                },
                colors: ['#2563eb', '#16a34a', '#f59e0b', '#ef4444'],
                fill: {
                    type: 'solid',
                    opacity: [0, 0, 0, 0.18],
                },
                markers: {
                    size: [3, 3, 3, 4],
                    hover: {
                        size: 6,
                    },
                },
                grid: {
                    borderColor: 'rgba(107, 114, 128, 0.15)',
                    strokeDashArray: 4,
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: 'light',
                },
                legend: {
                    show: true,
                    position: 'top',
                    labels: {
                        colors: '#6b7280',
                    },
                },
            };
        }

        function renderChart(samples) {
            const chartElement = document.getElementById('accelChart');

            if (!chartElement) {
                return;
            }

            const chartOptions = buildChartOptions(samples);

            if (accelChart) {
                accelChart.updateOptions({
                    xaxis: chartOptions.xaxis,
                    colors: chartOptions.colors,
                    stroke: chartOptions.stroke,
                    markers: chartOptions.markers,
                    grid: chartOptions.grid,
                    tooltip: chartOptions.tooltip,
                    legend: chartOptions.legend,
                }, false, false);
                accelChart.updateSeries(chartOptions.series, true);

                return;
            }

            accelChart = new ApexCharts(chartElement, chartOptions);
            accelChart.render();
        }

        function renderMap(gps) {
            const latitude = Number(gps.latitude);
            const longitude = Number(gps.longitude);

            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return;
            }

            if (!mapState.map) {
                mapState.map = L.map('gps-map', {
                    center: [latitude, longitude],
                    zoom: 13,
                    scrollWheelZoom: false,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(mapState.map);

                mapState.marker = L.marker([latitude, longitude]).addTo(mapState.map);
            } else {
                mapState.marker.setLatLng([latitude, longitude]);
                mapState.map.setView([latitude, longitude], mapState.map.getZoom());
            }

            mapState.marker.bindPopup(`GPS NEO-6M<br>Lat: ${formatNumber(latitude, 7)}<br>Lng: ${formatNumber(longitude, 7)}`);
        }

        function applyDashboardData(data) {
            setText('currentMagnitude', formatNumber(data.currentAccel.magnitude));
            setText('currentX', formatNumber(data.currentAccel.x));
            setText('currentY', formatNumber(data.currentAccel.y));
            setText('currentZ', formatNumber(data.currentAccel.z));
            setText('currentAxes', `${formatNumber(data.currentAccel.x)} / ${formatNumber(data.currentAccel.y)} / ${formatNumber(data.currentAccel.z)}`);
            setText('currentAccelTime', data.currentAccel.time ?? '--');
            setText('lastUpdatedAt', data.lastUpdatedAt ?? '--');

            setText('gpsLatitude', formatNumber(data.gps.latitude, 7));
            setText('gpsLongitude', formatNumber(data.gps.longitude, 7));
            setText('gpsAltitude', `${formatNumber(data.gps.altitude, 2)} m`);
            setText('gpsSatellites', data.gps.satellites ?? 0);
            setText('gpsStatus', data.gps.status ?? 'NO FIX');
            setText('gpsRecordedAt', data.gps.recorded_at ?? '--');

            setText('magnitudeMaximum', formatNumber(data.summary.maximum));
            setText('magnitudeAverage', formatNumber(data.summary.average));
            setText('sampleCount', String(data.summary.count ?? 0));

            renderChart(data.accelSamples);
            renderMap(data.gps);
        }

        async function refreshDashboardData() {
            try {
                const response = await fetch(`${dashboardDataUrl}?t=${Date.now()}`, {
                    headers: {
                        Accept: 'application/json',
                    },
                    cache: 'no-store',
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();
                applyDashboardData(data);
            } catch (error) {
                console.error('Failed to refresh dashboard data', error);
            }
        }

        applyDashboardData(initialDashboardData);
        refreshDashboardData();
        setInterval(refreshDashboardData, dashboardRefreshIntervalMs);
    });
</script>
@endpush