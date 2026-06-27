<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Dashboard - CBE Platform</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background: #f5f7fa;
            color: #1f2937;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 20px;
            text-align: center;
        }
        header h1 { font-size: 1.8em; margin-bottom: 8px; }
        header p { opacity: 0.9; }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .kpis {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .kpi {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        .kpi .num {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 8px;
        }
        .kpi .label {
            font-size: 0.85em;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        #map {
            width: 100%;
            height: 500px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .nav a {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #667eea;
            font-weight: 600;
            border: 2px solid #667eea;
            transition: all 0.3s;
        }
        .nav a:hover {
            background: #667eea;
            color: white;
        }
        .region-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
        }
        .region-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .region-card h3 {
            color: #1f2937;
            margin-bottom: 12px;
        }
        .region-stat {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 0.9em;
            color: #666;
        }
        .region-stat .value {
            font-weight: bold;
            color: #667eea;
        }
        footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 0.85em;
        }
    </style>
</head>
<body>
    <header>
        <h1>☁️ CBE Cloud Dashboard</h1>
        <p>Real-time view of all remote learning devices</p>
    </header>

    <div class="container">
        <div class="nav">
            <a href="{{ route('cloud.dashboard') }}">Dashboard</a>
            <a href="{{ route('cloud.devices') }}">Devices</a>
            <a href="{{ route('cloud.regions') }}">Regions</a>
            <a href="{{ route('cloud.reports') }}">Reports</a>
        </div>

        <div class="kpis">
            <div class="kpi">
                <div class="num">{{ $totalDevices }}</div>
                <div class="label">Total Devices</div>
            </div>
            <div class="kpi">
                <div class="num">{{ $onlineDevices }}/{{ $totalDevices }}</div>
                <div class="label">Online</div>
            </div>
            <div class="kpi">
                <div class="num">{{ number_format($totalStudents) }}</div>
                <div class="label">Students</div>
            </div>
            <div class="kpi">
                <div class="num">{{ number_format($totalLessons) }}</div>
                <div class="label">Lessons Done</div>
            </div>
            <div class="kpi">
                <div class="num">{{ $avgScore }}%</div>
                <div class="label">Avg Score</div>
            </div>
        </div>

        <div id="map"></div>

        <h2 style="margin: 30px 0 20px;">Regional Overview</h2>
        <div class="region-cards">
            @foreach($regionStats as $region)
                <div class="region-card">
                    <h3>📍 {{ $region['region'] }}</h3>
                    <div class="region-stat">
                        <span>Devices:</span>
                        <span class="value">{{ $region['device_count'] }}</span>
                    </div>
                    <div class="region-stat">
                        <span>Online:</span>
                        <span class="value">{{ $region['online'] }}</span>
                    </div>
                    <div class="region-stat">
                        <span>Students:</span>
                        <span class="value">{{ number_format($region['total_students']) }}</span>
                    </div>
                    <div class="region-stat">
                        <span>Lessons:</span>
                        <span class="value">{{ number_format($region['total_lessons']) }}</span>
                    </div>
                    <div class="region-stat">
                        <span>Avg Score:</span>
                        <span class="value">{{ $region['avg_score'] }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <footer>
        CBE Cloud Dashboard · Real-time data from field devices · Last updated {{ now()->format('Y-m-d H:i:s') }}
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const devices = {!! json_encode($devices) !!};

        const map = L.map('map').setView([0, 35], 5);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        const markers = L.featureGroup();

        devices.forEach(device => {
            if (!device.latitude || !device.longitude) return;

            const color = device.is_online ? '#667eea' : '#9ca3af';
            const circle = L.circleMarker([device.latitude, device.longitude], {
                radius: Math.max(5, device.students / 10),
                fillColor: color,
                color: 'white',
                weight: 2,
                fillOpacity: device.is_online ? 0.9 : 0.5
            });

            const popup = `
                <div style="min-width: 200px;">
                    <h4>${device.name}</h4>
                    <p style="font-size: 0.85em; color: #666; margin: 4px 0;">
                        📍 ${device.region || 'Unknown'} · ${device.county || ''}
                    </p>
                    <div style="margin-top: 8px; font-size: 0.85em;">
                        <div>👥 Students: <b>${device.students}</b></div>
                        <div>📚 Lessons: <b>${device.lessons}</b></div>
                        <div>📊 Avg Score: <b>${device.avg_score}%</b></div>
                        <div>⏱ Last sync: <b>${device.last_sync}</b></div>
                    </div>
                </div>
            `;

            circle.bindPopup(popup);
            circle.addTo(markers);
        });

        markers.addTo(map);
        if (devices.length > 0) map.fitBounds(markers.getBounds().pad(0.1));
    </script>
</body>
</html>
