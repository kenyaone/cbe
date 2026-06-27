<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regions - Cloud Dashboard</title>
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
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .nav { margin-bottom: 20px; }
        .nav a {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #667eea;
            font-weight: 600;
        }
        .regions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .region-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .region-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.95em;
        }
        .stat-row:last-child { border-bottom: none; }
        .stat-label { color: #6b7280; }
        .stat-value {
            font-weight: 600;
            color: #667eea;
        }
    </style>
</head>
<body>
    <header>
        <h1>🌍 Regional Overview</h1>
    </header>

    <div class="container">
        <div class="nav">
            <a href="{{ route('cloud.dashboard') }}">← Back to Dashboard</a>
        </div>

        <div class="regions">
            @forelse($regions as $region)
                <div class="region-card">
                    <h3>📍 {{ $region['region'] }}</h3>
                    <div class="stat-row">
                        <span class="stat-label">Devices</span>
                        <span class="stat-value">{{ $region['device_count'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Online</span>
                        <span class="stat-value">{{ $region['online'] }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Students</span>
                        <span class="stat-value">{{ number_format($region['total_students']) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Lessons Done</span>
                        <span class="stat-value">{{ number_format($region['total_lessons']) }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="stat-label">Avg Score</span>
                        <span class="stat-value">{{ $region['avg_score'] }}%</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #6b7280; grid-column: 1 / -1; padding: 40px;">
                    No regional data available
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
