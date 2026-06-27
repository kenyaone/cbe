<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform Statistics - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 20px; }
        .stat { background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 4px solid #667eea; text-align: center; }
        .stat-value { font-size: 2em; font-weight: bold; color: #667eea; }
        .stat-label { font-size: 0.9em; color: #666; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #667eea; color: white; padding: 12px; text-align: left; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📈 Platform Statistics</h1>
        <div>
            <a href="{{ route('admin.reports') }}">← Back to Reports</a>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <h2 style="margin-bottom: 15px;">Overview</h2>
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-value">{{ $stats['grades'] }}</div>
                    <div class="stat-label">Grade Levels</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['subjects'] }}</div>
                    <div class="stat-label">Subjects</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['strands'] }}</div>
                    <div class="stat-label">Strands</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['sub_strands'] }}</div>
                    <div class="stat-label">Sub-Strands</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 style="margin-bottom: 15px;">Subjects by Grade</h2>
            <table>
                <thead>
                    <tr>
                        <th>Grade Level</th>
                        <th>Number of Subjects</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($subjects_by_grade as $grade)
                        <tr>
                            <td>{{ $grade->grade_level }}</td>
                            <td>{{ $grade->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2 style="margin-bottom: 15px;">30-Day Activity Trend</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>New Registrations</th>
                        <th>Active Learners</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trend_data as $day)
                        <tr>
                            <td>{{ $day->date }}</td>
                            <td>{{ $day->registrations }}</td>
                            <td>{{ $day->active }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align: center; color: #999;">No activity data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
