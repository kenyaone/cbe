<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBE Platform - Global Device Map</title>
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
            padding: 30px 20px;
            text-align: center;
        }
        header h1 { font-size: 2em; margin-bottom: 8px; }
        header p { opacity: 0.9; margin-bottom: 15px; }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card .value {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-card .label {
            font-size: 0.85em;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        #map {
            width: 100%;
            height: 600px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-box h2 {
            margin-bottom: 15px;
            color: #1f2937;
        }
        .legend {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border: 3px solid white;
        }
        footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 0.85em;
        }
        .embed-section {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .embed-code {
            background: white;
            padding: 15px;
            border-radius: 6px;
            overflow-x: auto;
            font-family: monospace;
            font-size: 0.85em;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <header>
        <h1>🌍 CBE Platform Global Device Map</h1>
        <p>Real-time view of Competency-Based Education devices across Africa</p>
    </header>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="value">{{ $stats['total_devices'] }}</div>
                <div class="label">Devices</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $stats['online_devices'] }}</div>
                <div class="label">Online</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ number_format($stats['total_students']) }}</div>
                <div class="label">Students</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ number_format($stats['total_lessons']) }}</div>
                <div class="label">Lessons Done</div>
            </div>
            <div class="stat-card">
                <div class="value">{{ $stats['avg_score'] }}%</div>
                <div class="label">Avg Score</div>
            </div>
        </div>

        <div id="map"></div>

        <div class="info-box">
            <h2>📍 About This Map</h2>
            <p style="margin-bottom: 15px;">
                This map shows all active CBE Platform devices deployed across regions. Each device represents a complete offline learning center that can operate without internet connection. When internet becomes available, devices automatically sync their learner progress to the central cloud server.
            </p>
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: #667eea;"></div>
                    <span><strong>🟢 Online</strong> - Synced in last hour</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #9ca3af;"></div>
                    <span><strong>⚫ Offline</strong> - No sync for 1+ hour</span>
                </div>
                <div class="legend-item">
                    <span><strong>📍 Marker Size</strong> = Number of students</span>
                </div>
            </div>
        </div>

        <div class="info-box">
            <h2>🔗 Embed This Map</h2>
            <p style="margin-bottom: 15px;">Add this map to your website:</p>
            <div class="embed-code">&lt;iframe src="{{ url('/devices/embed') }}" width="100%" height="600" style="border: none; border-radius: 8px;"&gt;&lt;/iframe&gt;</div>
        </div>

        <div class="info-box">
            <h2>📊 Access Data via API</h2>
            <p style="margin-bottom: 15px;">Get real-time device data as JSON:</p>
            <div class="embed-code">curl {{ url('/devices/api') }}</div>
            <p style="margin-top: 15px;">Returns all devices with status, location, and statistics.</p>
        </div>
    </div>

    <footer>
        CBE Platform Global Device Network · Real-time data updates every hour
    </footer>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const devices = {!! json_encode($devices) !!};

        const map = L.map('map').setView([0, 20], 4);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const markers = L.featureGroup();

        devices.forEach(device => {
            if (!device.latitude || !device.longitude) return;

            const color = device.is_online ? '#667eea' : '#9ca3af';
            const size = Math.max(6, Math.min(20, device.total_students / 5));

            const circle = L.circleMarker([device.latitude, device.longitude], {
                radius: size,
                fillColor: color,
                color: 'white',
                weight: 2,
                fillOpacity: device.is_online ? 0.9 : 0.5
            });

            const popup = `
                <div style="min-width: 200px;">
                    <h4 style="margin-bottom: 8px;">${device.device_name}</h4>
                    <div style="font-size: 0.85em; color: #666; margin-bottom: 10px;">
                        📍 ${device.region || 'Unknown'}
                    </div>
                    <div style="font-size: 0.9em;">
                        <div>👥 Students: <b>${device.total_students}</b></div>
                        <div>📚 Lessons: <b>${device.total_lessons}</b></div>
                        <div>📊 Score: <b>${device.avg_score}%</b></div>
                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #eee;">
                            ${device.is_online ? '🟢 Online' : '⚫ Offline'}
                        </div>
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
