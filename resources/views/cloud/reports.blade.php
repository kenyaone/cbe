<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Cloud Dashboard</title>
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
            margin-right: 5px;
        }
        .filters {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filters a {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: 2px solid #667eea;
            color: #667eea;
            font-weight: 600;
        }
        .filters a.active {
            background: #667eea;
            color: white;
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
        }
        td {
            padding: 12px;
            border-top: 1px solid #e5e7eb;
        }
        .progress-bar {
            background: #e5e7eb;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            background: #667eea;
            height: 100%;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h1>📊 Reports & Analytics</h1>
    </header>

    <div class="container">
        <div class="nav">
            <a href="{{ route('cloud.dashboard') }}">← Back to Dashboard</a>
        </div>

        <div class="filters">
            <a href="{{ route('cloud.reports', ['period' => 7]) }}" class="{{ request('period', 7) == 7 ? 'active' : '' }}">Last 7 days</a>
            <a href="{{ route('cloud.reports', ['period' => 30]) }}" class="{{ request('period') == 30 ? 'active' : '' }}">Last 30 days</a>
            <a href="{{ route('cloud.reports', ['period' => 90]) }}" class="{{ request('period') == 90 ? 'active' : '' }}">Last 90 days</a>
        </div>

        <h2 style="margin-bottom: 15px; margin-top: 30px;">Device Performance</h2>
        <table>
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Region</th>
                    <th>Lessons (7d)</th>
                    <th>Avg Score</th>
                    <th>Active Days</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deviceStats as $stat)
                    <tr>
                        <td><strong>{{ $stat['device_name'] }}</strong></td>
                        <td>{{ $stat['region'] ?? '—' }}</td>
                        <td>{{ number_format($stat['total_lessons']) }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; align-items: center;">
                                <div class="progress-bar" style="flex: 1;">
                                    <div class="progress-fill" style="width: {{ min(100, $stat['avg_score']) }}%"></div>
                                </div>
                                <span>{{ $stat['avg_score'] }}%</span>
                            </div>
                        </td>
                        <td>{{ $stat['active_days'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280;">
                            No data available for this period
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <h2 style="margin-bottom: 15px; margin-top: 30px;">Daily Trends</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Active Learners</th>
                    <th>Lessons Completed</th>
                    <th>Avg Score</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chartData as $day)
                    <tr>
                        <td><strong>{{ $day['date'] }}</strong></td>
                        <td>{{ number_format($day['total_learners']) }}</td>
                        <td>{{ number_format($day['lessons_completed']) }}</td>
                        <td>{{ $day['avg_score'] }}%</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7280;">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
