<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBE Device Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; }
        #map { width: 100%; height: 100vh; }
        .leaflet-popup-content { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Fetch data from API
        fetch('{{ url("/devices/api") }}')
            .then(r => r.json())
            .then(data => {
                const map = L.map('map').setView([0, 20], 4);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap',
                    maxZoom: 19
                }).addTo(map);

                const markers = L.featureGroup();

                data.devices.forEach(device => {
                    if (!device.latitude || !device.longitude) return;

                    const color = device.is_online ? '#667eea' : '#9ca3af';
                    const size = Math.max(6, Math.min(20, device.students / 5));

                    const circle = L.circleMarker([device.latitude, device.longitude], {
                        radius: size,
                        fillColor: color,
                        color: 'white',
                        weight: 2,
                        fillOpacity: device.is_online ? 0.9 : 0.5
                    });

                    const popup = `<div style="min-width: 200px;"><h4>${device.name}</h4><p style="color: #666; margin: 5px 0; font-size: 0.9em;">📍 ${device.region || 'Unknown'}</p><div style="font-size: 0.85em; margin-top: 8px;">👥 ${device.students} students | 📚 ${device.lessons} lessons</div></div>`;

                    circle.bindPopup(popup);
                    circle.addTo(markers);
                });

                markers.addTo(map);
                if (data.devices.length > 0) map.fitBounds(markers.getBounds().pad(0.1));
            })
            .catch(e => console.error('Failed to load devices:', e));
    </script>
</body>
</html>
