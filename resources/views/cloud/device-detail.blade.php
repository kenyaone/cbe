<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $device->device_name }} - Cloud Dashboard</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .back { color: #667eea; text-decoration: none; margin-bottom: 20px; display: inline-block; }
        .device-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .device-header h1 { margin-bottom: 10px; }
        .meta { display: flex; gap: 20px; font-size: 0.9em; color: #666; flex-wrap: wrap; }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .stat-card .num {
            font-size: 1.8em;
            font-weight: bold;
            color: #667eea;
        }
        .stat-card .label {
            font-size: 0.85em;
            color: #6b7280;
            margin-top: 5px;
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
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9em;
        }
        td {
            padding: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 0.9em;
        }
        .status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .status.completed {
            background: #dcfce7;
            color: #166534;
        }
        .status.in-progress {
            background: #fef3c7;
            color: #92400e;
        }
    </style>
</head>
<body>
    <header>
        <h1>📱 {{ $device->device_name }}</h1>
    </header>

    <div class="container">
        <a href="{{ route('cloud.devices') }}" class="back">← Back to Devices</a>

        <div class="device-header">
            <h2>Device Information</h2>
            <div class="meta">
                <div>📍 {{ $device->region ?? 'Unknown' }} · {{ $device->county ?? '' }}</div>
                <div>🌐 {{ $device->latitude }}, {{ $device->longitude }}</div>
                <div>📡 {{ $device->is_online ? '🟢 Online' : '⚫ Offline' }}</div>
                <div>⏱ {{ $device->last_sync_at?->format('Y-m-d H:i:s') ?? 'Never synced' }}</div>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="num">{{ $stats['total_learners'] }}</div>
                <div class="label">Total Learners</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['completed'] }}</div>
                <div class="label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['in_progress'] }}</div>
                <div class="label">In Progress</div>
            </div>
            <div class="stat-card">
                <div class="num">{{ $stats['avg_progress'] }}%</div>
                <div class="label">Avg Progress</div>
            </div>
        </div>

        <h2 style="margin-bottom: 15px;">Learner Progress</h2>
        <table>
            <thead>
                <tr>
                    <th>Learner</th>
                    <th>Subject</th>
                    <th>Content</th>
                    <th>Progress</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($progress as $p)
                    <tr>
                        <td><strong>{{ $p->learner_name }}</strong></td>
                        <td>{{ $p->subject }}</td>
                        <td>{{ $p->content_title }}</td>
                        <td>{{ $p->progress_percentage }}%</td>
                        <td>
                            <span class="status {{ $p->status === 'completed' ? 'completed' : 'in-progress' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280;">
                            No progress data yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
