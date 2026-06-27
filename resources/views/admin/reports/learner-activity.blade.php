<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Activity Report - CBE Platform</title>
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
        <h1>👥 Learner Activity Report</h1>
        <div>
            <a href="{{ route('admin.reports') }}">← Back to Reports</a>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <h2 style="margin-bottom: 15px;">Activity Summary</h2>
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-value">{{ $stats['total'] }}</div>
                    <div class="stat-label">Total Learners</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['active_today'] }}</div>
                    <div class="stat-label">Active Today</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['active_this_week'] }}</div>
                    <div class="stat-label">Active This Week</div>
                </div>
                <div class="stat">
                    <div class="stat-value">{{ $stats['new_this_week'] }}</div>
                    <div class="stat-label">New This Week</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2 style="margin-bottom: 15px;">Learners by Grade</h2>
            <table>
                <thead>
                    <tr>
                        <th>Grade Level</th>
                        <th>Number of Learners</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stats['by_grade'] as $grade)
                        <tr>
                            <td>{{ $grade->grade_level }}</td>
                            <td>{{ $grade->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2 style="margin-bottom: 15px;">Recent Learners</h2>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Grade</th>
                        <th>Registered</th>
                        <th>Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($learners as $learner)
                        <tr>
                            <td>{{ $learner->name }}</td>
                            <td>{{ $learner->username }}</td>
                            <td>{{ $learner->grade_level }}</td>
                            <td>{{ $learner->created_at->format('M d, Y') }}</td>
                            <td>{{ $learner->last_login_at ? $learner->last_login_at->format('M d, Y H:i') : 'Never' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #999;">No learners found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
