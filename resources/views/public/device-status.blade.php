<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Status - CBE Platform</title>
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
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .status-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #667eea;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .status-card h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .stat {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
            font-size: 0.95em;
        }
        .stat:last-child { border-bottom: none; }
        .stat-value {
            font-weight: 600;
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
        <h1>📊 CBE Platform Device Status</h1>
    </header>

    <div class="container">
        <div class="status-grid">
            @forelse($regions as $region)
                <div class="status-card">
                    <h3>📍 {{ $region['region'] }}</h3>
                    <div class="stat">
                        <span>Total Devices</span>
                        <span class="stat-value">{{ $region['device_count'] }}</span>
                    </div>
                    <div class="stat">
                        <span>Online</span>
                        <span class="stat-value">{{ $region['online'] }}</span>
                    </div>
                    <div class="stat">
                        <span>Students</span>
                        <span class="stat-value">{{ number_format($region['students']) }}</span>
                    </div>
                    <div class="stat">
                        <span>Lessons Done</span>
                        <span class="stat-value">{{ number_format($region['lessons']) }}</span>
                    </div>
                    <div class="stat">
                        <span>Avg Score</span>
                        <span class="stat-value">{{ $region['avg_score'] }}%</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #6b7280; grid-column: 1 / -1; padding: 40px;">
                    No devices registered yet
                </div>
            @endforelse
        </div>

        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <p style="color: #6b7280;">Last updated: {{ $last_updated->format('Y-m-d H:i:s') }} UTC</p>
            <p style="color: #6b7280; margin-top: 10px;">
                <a href="{{ route('public.device-map') }}" style="color: #667eea; text-decoration: none;">View interactive map →</a>
            </p>
        </div>
    </div>

    <footer>
        CBE Platform · Device Status Dashboard
    </footer>
</body>
</html>
