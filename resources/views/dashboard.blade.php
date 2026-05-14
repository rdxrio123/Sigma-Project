@extends('layouts.dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('dashboard-content')
        <div class="panel-page">
            <header class="content-header-flex">
                <div>
                    <p class="section-kicker">SIGMA Monitoring</p>
                    <h1>Panel Utama</h1>
                    <p>Pantau getaran gempa, koordinat GPS, dan histori sensor secara realtime tanpa refresh.</p>
                </div>

                <div class="datetime-widget">
                    <div id="realtime-clock" class="time-display">{{ now()->timezone('Asia/Jakarta')->format('H:i:s') }}</div>
                    <div id="realtime-date" class="date-display">{{ now()->timezone('Asia/Jakarta')->translatedFormat('l, d M Y') }}</div>
                </div>
            </header>

            <div class="summary-grid dashboard-summary-grid">
                <div class="glow-card stat-card">
                    <div class="card-title">Status Sinkron</div>
                    <div class="card-value card-value-status">Live Update Aktif</div>
                    <div class="card-desc">Polling otomatis setiap 2 detik</div>
                </div>
                <div class="glow-card stat-card meter-card meter-card-magnitude">
                    <div class="card-title">Magnitudo Getaran</div>
                    <div id="currentMagnitude" class="card-value">{{ number_format($currentAccel['magnitude'], 2) }}</div>
                    <div class="card-desc">Nilai PGA terkini</div>
                </div>
                <div class="glow-card stat-card">
                    <div class="card-title">Sumbu X</div>
                    <div id="currentX" class="card-value">{{ number_format($currentAccel['x'], 2) }}</div>
                    <div class="card-desc">Akselerasi horizontal</div>
                </div>
                <div class="glow-card stat-card">
                    <div class="card-title">Sumbu Y</div>
                    <div id="currentY" class="card-value">{{ number_format($currentAccel['y'], 2) }}</div>
                    <div class="card-desc">Akselerasi lateral</div>
                </div>
                <div class="glow-card stat-card">
                    <div class="card-title">Sumbu Z</div>
                    <div id="currentZ" class="card-value">{{ number_format($currentAccel['z'], 2) }}</div>
                    <div class="card-desc">Akselerasi vertikal</div>
                </div>
                <div class="glow-card stat-card">
                    <div class="card-title">Update Terakhir</div>
                    <div id="lastUpdatedAt" class="card-value card-value-time">{{ $lastUpdatedAt ?? '--' }}</div>
                    <div class="card-desc">Data terbaru dari server</div>
                </div>
            </div>

            <div class="dashboard-grid">
                <section class="glow-card panel-card map-card" id="sensor-gps-card">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">GPS NEO-6M</h2>
                            <p class="section-subtitle">Lokasi perangkat ditampilkan di peta OpenStreetMap.</p>
                        </div>
                        <span class="status-pill online">Online</span>
                    </div>

                    <div id="gps-map" class="sensor-map"></div>

                    <div class="dashboard-info-grid">
                        <div class="dashboard-info-card">
                            <p>Latitude</p>
                            <strong id="gpsLatitude">{{ number_format($gps['latitude'], 7) }}</strong>
                        </div>
                        <div class="dashboard-info-card">
                            <p>Longitude</p>
                            <strong id="gpsLongitude">{{ number_format($gps['longitude'], 7) }}</strong>
                        </div>
                        <div class="dashboard-info-card">
                            <p>Altitude</p>
                            <strong id="gpsAltitude">{{ number_format($gps['altitude'], 2) }} m</strong>
                        </div>
                        <div class="dashboard-info-card">
                            <p>Satellites</p>
                            <strong id="gpsSatellites">{{ $gps['satellites'] }}</strong>
                        </div>
                        <div class="dashboard-info-card">
                            <p>Status</p>
                            <strong id="gpsStatus">{{ $gps['status'] }}</strong>
                        </div>
                        <div class="dashboard-info-card">
                            <p>Waktu GPS</p>
                            <strong id="gpsRecordedAt">{{ $gps['recorded_at'] }}</strong>
                        </div>
                    </div>
                </section>

                <section class="glow-card panel-card">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Akselerometer</h2>
                            <p class="section-subtitle">Grafik realtime + ringkasan nilai sensor ADXL345.</p>
                        </div>
                        <span class="status-pill realtime">Realtime</span>
                    </div>

                    <div class="dashboard-accel-header">
                        <div class="dashboard-accel-header-inner">
                            <div>
                                <p class="sensor-label">Nilai X / Y / Z</p>
                                <p id="currentAxes" class="sensor-axes">{{ number_format($currentAccel['x'], 2) }} / {{ number_format($currentAccel['y'], 2) }} / {{ number_format($currentAccel['z'], 2) }}</p>
                            </div>
                            <div class="dashboard-accel-highlight">
                                <p class="sensor-label light">Waktu Server</p>
                                <p id="currentAccelTime" class="sensor-time">{{ $currentAccel['time'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="dashboard-chart-card">
                        <div id="accelChart" class="sensor-chart"></div>
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
                </section>
            </div>

            <section class="glow-card panel-card log-card">
                <div class="section-header">
                    <div>
                        <h2 class="section-title">Log Sensor Terakhir</h2>
                        <p class="section-subtitle">Riwayat 12 sampel terbaru yang dipakai untuk grafik realtime.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>X</th>
                                <th>Y</th>
                                <th>Z</th>
                                <th class="text-right">Magnitudo</th>
                            </tr>
                        </thead>
                        <tbody id="sample-log-body">
                            @forelse($accelSamples as $sample)
                                <tr>
                                    <td class="text-muted">{{ $sample['time'] }}</td>
                                    <td>{{ number_format($sample['x'], 2) }}</td>
                                    <td>{{ number_format($sample['y'], 2) }}</td>
                                    <td>{{ number_format($sample['z'], 2) }}</td>
                                    <td class="text-right">{{ number_format($sample['magnitude'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/sidebar.js') }}"></script>
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
        const jakartaFormatter = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            weekday: 'long',
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });

        let accelChart = null;
        const mapState = {
            map: null,
            marker: null,
        };

        function updateClock() {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');
            const timeStr = `${hh}:${mm}:${ss}`;
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const dateStr = `${days[now.getDay()]}, ${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()}`;

            setText('realtime-clock', timeStr);
            setText('realtime-date', dateStr);
            setText('currentAccelTime', timeStr + ' WIB');
            setText('lastUpdatedAt', `${String(now.getDate()).padStart(2, '0')} ${months[now.getMonth()]} ${now.getFullYear()} ${timeStr} WIB`);

            // Also tick the log table timestamps every second
            const tbody = document.getElementById('sample-log-body');
            if (tbody) {
                const rows = tbody.querySelectorAll('tr td:first-child');
                rows.forEach((td, index) => {
                    const t = new Date(now.getTime() - (index * dashboardRefreshIntervalMs));
                    td.textContent = `${String(t.getHours()).padStart(2, '0')}:${String(t.getMinutes()).padStart(2, '0')}:${String(t.getSeconds()).padStart(2, '0')} WIB`;
                });
            }
        }

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
                    height: 300,
                    fontFamily: 'Plus Jakarta Sans, system-ui, sans-serif',
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
                },
                series: [
                    { name: 'X', data: samples.map((sample) => sample.x) },
                    { name: 'Y', data: samples.map((sample) => sample.y) },
                    { name: 'Z', data: samples.map((sample) => sample.z) },
                    { name: 'Magnitudo', data: samples.map((sample) => sample.magnitude) },
                ],
                xaxis: {
                    categories: samples.map((_, index) => {
                        const now = new Date();
                        const reverseIndex = samples.length - 1 - index;
                        const t = new Date(now.getTime() - (reverseIndex * 2000));
                        return `${String(t.getHours()).padStart(2, '0')}:${String(t.getMinutes()).padStart(2, '0')} WIB`;
                    }),
                    labels: { 
                        style: { colors: '#A89081', fontWeight: 600 },
                        hideOverlappingLabels: true,
                        rotate: 0
                    },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: {
                    labels: { style: { colors: '#A89081', fontWeight: 600 } },
                },
                stroke: {
                    curve: 'smooth',
                    width: [2, 2, 2, 4],
                },
                colors: ['#8B5026', '#C2743E', '#E58A47', '#E13B3B'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: "vertical",
                        opacityFrom: [0, 0, 0, 0.4],
                        opacityTo: [0, 0, 0, 0],
                        stops: [0, 100]
                    }
                },
                markers: {
                    size: [0, 0, 0, 3],
                    strokeWidth: 2,
                    hover: { size: 6 },
                },
                grid: {
                    borderColor: 'rgba(139, 80, 38, 0.1)',
                    strokeDashArray: 0,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: true } },
                },
                tooltip: {
                    shared: true,
                    intersect: false,
                    theme: document.documentElement.classList.contains('dark-mode') ? 'dark' : 'light',
                },
                legend: {
                    show: true,
                    position: 'top',
                    labels: { colors: '#A89081' },
                    fontWeight: 700,
                },
            };
        }

        function renderChart(samples) {
            if (typeof ApexCharts === 'undefined') return;

            const chartElement = document.getElementById('accelChart');

            if (!chartElement) {
                return;
            }

            if (accelChart) {
                // To safely update categories and series without glitches in ApexCharts:
                accelChart.updateSeries([
                    { name: 'X', data: samples.map((sample) => sample.x) },
                    { name: 'Y', data: samples.map((sample) => sample.y) },
                    { name: 'Z', data: samples.map((sample) => sample.z) },
                    { name: 'Magnitudo', data: samples.map((sample) => sample.magnitude) },
                ]);
                
                // Generate real-time JS timeline for the chart categories
                const now = new Date();
                const realTimeCategories = samples.map((_, index) => {
                    const reverseIndex = samples.length - 1 - index;
                    const t = new Date(now.getTime() - (reverseIndex * dashboardRefreshIntervalMs));
                    return `${String(t.getHours()).padStart(2, '0')}:${String(t.getMinutes()).padStart(2, '0')} WIB`;
                });

                accelChart.updateOptions({
                    xaxis: {
                        categories: realTimeCategories
                    }
                });
                
                return;
            }

            const chartOptions = buildChartOptions(samples);
            accelChart = new ApexCharts(chartElement, chartOptions);
            accelChart.render();
        }

        // Tile layer URLs
        const tileLight = 'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png';
        const tileDark = 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
        const tileAttribution = '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; <a href="https://carto.com/">CARTO</a>';

        let sigmaIcon = null;

        function getSigmaIcon() {
            if (sigmaIcon) return sigmaIcon;
            if (typeof L === 'undefined') return null;
            sigmaIcon = L.divIcon({
                className: 'sigma-map-marker',
                html: `<div style="
                    width: 28px; height: 28px;
                    background: var(--sigma-accent, #C2743E);
                    border: 3px solid #fff;
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                    display: flex; align-items: center; justify-content: center;
                "><div style="
                    width: 8px; height: 8px;
                    background: #fff;
                    border-radius: 50%;
                    transform: rotate(45deg);
                "></div></div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 28],
                popupAnchor: [0, -30],
            });
            return sigmaIcon;
        }

        function getActiveTileUrl() {
            return document.documentElement.classList.contains('dark-mode') ? tileDark : tileLight;
        }

        function renderMap(gps) {
            // Guard: Leaflet must be loaded
            if (typeof L === 'undefined') return;

            const latitude = Number(gps.latitude);
            const longitude = Number(gps.longitude);

            if (!Number.isFinite(latitude) || !Number.isFinite(longitude)) {
                return;
            }

            if (latitude === 0 && longitude === 0) {
                return;
            }

            const popupHtml = `
                <div style="font-family: 'Plus Jakarta Sans', sans-serif; min-width: 180px; line-height: 1.6;">
                    <div style="font-weight: 800; font-size: 14px; margin-bottom: 6px; color: #C2743E;">📍 GPS NEO-6M</div>
                    <div style="font-size: 12px;">
                        <b>Lat:</b> ${formatNumber(latitude, 7)}<br>
                        <b>Lng:</b> ${formatNumber(longitude, 7)}<br>
                        <b>Alt:</b> ${formatNumber(gps.altitude, 2)} m<br>
                        <b>Sat:</b> ${gps.satellites ?? 0}<br>
                        <b>Status:</b> ${gps.status ?? 'NO FIX'}
                    </div>
                </div>
            `;

            const icon = getSigmaIcon();

            if (!mapState.map) {
                mapState.map = L.map('gps-map', {
                    center: [latitude, longitude],
                    zoom: 15,
                    scrollWheelZoom: true,
                    zoomControl: false,
                });

                L.control.zoom({ position: 'topright' }).addTo(mapState.map);

                mapState.tileLayer = L.tileLayer(getActiveTileUrl(), {
                    attribution: tileAttribution,
                    maxZoom: 19,
                }).addTo(mapState.map);

                mapState.circle = L.circle([latitude, longitude], {
                    radius: 50,
                    color: '#C2743E',
                    fillColor: '#C2743E',
                    fillOpacity: 0.15,
                    weight: 1,
                }).addTo(mapState.map);

                const markerOptions = icon ? { icon: icon } : {};
                mapState.marker = L.marker([latitude, longitude], markerOptions).addTo(mapState.map);
                mapState.marker.bindPopup(popupHtml).openPopup();
            } else {
                if (mapState.marker) {
                    mapState.marker.setLatLng([latitude, longitude]);
                    mapState.marker.setPopupContent(popupHtml);
                }
                if (mapState.circle) {
                    mapState.circle.setLatLng([latitude, longitude]);
                }
                mapState.map.flyTo([latitude, longitude], mapState.map.getZoom(), { duration: 1.5 });
            }
        }

        // Switch tile layer when dark mode toggles
        const mapThemeObserver = new MutationObserver(() => {
            if (mapState.map && mapState.tileLayer) {
                mapState.tileLayer.setUrl(getActiveTileUrl());
            }
        });
        mapThemeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        function renderSampleTable(samples) {
            const tbody = document.getElementById('sample-log-body');
            if (!tbody) return;

            if (!samples || samples.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-muted" style="text-align: center; padding: 2rem;">Belum ada data sensor masuk.</td></tr>`;
                return;
            }

            const now = new Date();
            let html = '';
            
            // Display newest samples first in the log
            const displaySamples = [...samples].reverse();
            
            displaySamples.forEach((sample, index) => {
                // Calculate dynamic live time to match the chart's ticking x-axis
                const t = new Date(now.getTime() - (index * dashboardRefreshIntervalMs));
                const liveTime = `${String(t.getHours()).padStart(2, '0')}:${String(t.getMinutes()).padStart(2, '0')}:${String(t.getSeconds()).padStart(2, '0')} WIB`;

                html += `<tr>
                    <td class="text-muted">${liveTime}</td>
                    <td>${formatNumber(sample.x, 2)}</td>
                    <td>${formatNumber(sample.y, 2)}</td>
                    <td>${formatNumber(sample.z, 2)}</td>
                    <td class="text-right">${formatNumber(sample.magnitude, 2)}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        function applyDashboardData(data) {
            if (!data) return;

            const accel = data.currentAccel || {};
            const gps = data.gps || {};
            const summary = data.summary || {};
            const samples = data.accelSamples || [];

            setText('currentMagnitude', formatNumber(accel.magnitude));
            setText('currentX', formatNumber(accel.x));
            setText('currentY', formatNumber(accel.y));
            setText('currentZ', formatNumber(accel.z));
            setText('currentAxes', `${formatNumber(accel.x)} / ${formatNumber(accel.y)} / ${formatNumber(accel.z)}`);

            setText('gpsLatitude', formatNumber(gps.latitude, 7));
            setText('gpsLongitude', formatNumber(gps.longitude, 7));
            setText('gpsAltitude', `${formatNumber(gps.altitude, 2)} m`);
            setText('gpsSatellites', gps.satellites ?? 0);
            setText('gpsStatus', gps.status ?? 'NO FIX');
            setText('gpsRecordedAt', gps.recorded_at ?? '--');

            setText('magnitudeMaximum', formatNumber(summary.maximum));
            setText('magnitudeAverage', formatNumber(summary.average));
            setText('sampleCount', String(summary.count ?? 0));

            try { renderChart(samples); } catch (e) { console.warn('[SIGMA] Chart error:', e); }
            try { renderMap(gps); } catch (e) { console.warn('[SIGMA] Map error:', e); }
            try { renderSampleTable(samples); } catch (e) { console.warn('[SIGMA] Table error:', e); }
        }

        // Fetch with timeout + abort controller to prevent hanging
        let isRefreshing = false;

        async function refreshDashboardData() {
            // Prevent overlapping requests
            if (isRefreshing) return;
            isRefreshing = true;

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 second timeout

            try {
                const response = await fetch(`${dashboardDataUrl}?t=${Date.now()}`, {
                    headers: { Accept: 'application/json' },
                    cache: 'no-store',
                    credentials: 'same-origin',
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);

                if (!response.ok) return;

                const data = await response.json();
                applyDashboardData(data);
            } catch (error) {
                clearTimeout(timeoutId);
                if (error.name !== 'AbortError') {
                    console.warn('[SIGMA] Refresh failed:', error.message);
                }
            } finally {
                isRefreshing = false;
            }
        }

        // ===== JAM REALTIME: jalankan PERTAMA dan INDEPENDEN =====
        // Jam berjalan di scope terpisah agar tidak bisa dimatikan oleh error apapun
        (function startClock() {
            try { updateClock(); } catch (e) { /* silent */ }
            setInterval(function () {
                try { updateClock(); } catch (e) { /* silent */ }
            }, 1000);
        })();

        // ===== DATA SENSOR: jalankan terpisah =====
        try {
            applyDashboardData(initialDashboardData);
        } catch (e) {
            console.error('[SIGMA] Error applying initial data:', e);
        }

        // Refresh data setiap 2 detik (terpisah dari jam)
        setInterval(function () {
            refreshDashboardData();
        }, dashboardRefreshIntervalMs);

        // Juga jalankan refresh pertama kali
        refreshDashboardData();

        // Dark mode observer untuk chart
        const observer = new MutationObserver(() => {
            if (accelChart) {
                try {
                    const isDark = document.documentElement.classList.contains('dark-mode');
                    accelChart.updateOptions({
                        tooltip: { theme: isDark ? 'dark' : 'light' }
                    });
                } catch (e) { /* silent */ }
            }
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
</script>
@endpush