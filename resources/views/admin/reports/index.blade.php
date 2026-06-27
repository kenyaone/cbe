<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s; text-decoration: none; color: inherit; }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
        .stat-icon { font-size: 2.5em; margin-bottom: 10px; }
        .stat-title { font-weight: 600; margin-bottom: 5px; }
        .stat-value { font-size: 2em; color: #667eea; }
        .stat-label { font-size: 0.9em; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📊 Reports & Analytics</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.content.upload') }}">Upload Content</a>
        </div>
    </div>

    <div class="container">
        <div class="stats-grid">
            <a href="{{ route('admin.reports.learner-activity') }}" class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-title">Learner Activity</div>
                <div class="stat-value">{{ $learner_activity['total'] }}</div>
                <div class="stat-label">Total Learners</div>
                <div class="stat-label">{{ $learner_activity['active_today'] }} active today</div>
            </a>

            <a href="{{ route('admin.reports.content-stats') }}" class="stat-card">
                <div class="stat-icon">📁</div>
                <div class="stat-title">Content Statistics</div>
                <div class="stat-value">{{ $content_stats['total_files'] }}</div>
                <div class="stat-label">Total Files</div>
                <div class="stat-label">{{ $content_stats['videos'] }} videos</div>
            </a>

            <a href="{{ route('admin.reports.platform-stats') }}" class="stat-card">
                <div class="stat-icon">📈</div>
                <div class="stat-title">Platform Statistics</div>
                <div class="stat-value">{{ $platform_stats['grades'] }}</div>
                <div class="stat-label">Grades</div>
                <div class="stat-label">{{ $platform_stats['subjects'] }} subjects</div>
            </a>
        </div>
    </div>
</body>
</html>
