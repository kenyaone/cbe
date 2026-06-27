<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devices - Cloud Dashboard</title>
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
        .nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .nav a {
            padding: 10px 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #667eea;
            font-weight: 600;
        }
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th {
            background: #f3f4f6;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #1f2937;
        }
        td {
            padding: 15px;
            border-top: 1px solid #e5e7eb;
        }
        tr:hover { background: #f9fafb; }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .status.online {
            background: #dcfce7;
            color: #166534;
        }
        .status.offline {
            background: #f3f4f6;
            color: #6b7280;
        }
        .device-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .device-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        <h1>📡 All Devices</h1>
    </header>

    <div class="container">
        <div class="nav">
            <a href="{{ route('cloud.dashboard') }}">← Back to Dashboard</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Device Name</th>
                    <th>Region</th>
                    <th>Status</th>
                    <th>Students</th>
                    <th>Lessons</th>
                    <th>Avg Score</th>
                    <th>Last Sync</th>
                </tr>
            </thead>
            <tbody>
                @forelse($devices as $device)
                    <tr>
                        <td>
                            <a href="{{ route('cloud.device-detail', $device->device_id) }}" class="device-link">
                                {{ $device->device_name }}
                            </a>
                        </td>
                        <td>{{ $device->region ?? 'N/A' }}</td>
                        <td>
                            <span class="status {{ $device->is_online ? 'online' : 'offline' }}">
                                {{ $device->is_online ? '🟢 Online' : '⚫ Offline' }}
                            </span>
                        </td>
                        <td>{{ number_format($device->total_students) }}</td>
                        <td>{{ number_format($device->total_lessons) }}</td>
                        <td>{{ $device->avg_score ? round($device->avg_score, 1) . '%' : '—' }}</td>
                        <td>{{ $device->last_sync_at?->diffForHumans() ?? 'Never' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6b7280; padding: 40px;">
                            No devices synced yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
