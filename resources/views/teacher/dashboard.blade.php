<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5em;
        }
        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
        }
        .header nav a:hover {
            opacity: 0.8;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            color: white;
            font-weight: 500;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            color: #999;
            font-size: 0.9em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
        }
        .recent-activity {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .recent-activity h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .activity-item {
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 15px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        .activity-item .student {
            font-weight: 600;
            color: #667eea;
        }
        .activity-item .lesson {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .activity-item .time {
            color: #999;
            font-size: 0.85em;
            margin-top: 5px;
        }
        .nav-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>👨‍🏫 Teacher Dashboard</h1>
        </div>
        <nav>
            <a href="{{ route('teacher.learner-progress') }}">Learner Progress</a>
            <a href="{{ route('teacher.reports') }}">Reports</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Learners</h3>
                <div class="number">{{ $totalLearners }}</div>
            </div>
            <div class="stat-card">
                <h3>Active Learners</h3>
                <div class="number">{{ $activeLearners }}</div>
            </div>
        </div>

        <div class="nav-buttons">
            <a href="{{ route('teacher.learner-progress') }}" class="btn btn-primary">View Learner Progress</a>
            <a href="{{ route('teacher.reports') }}" class="btn btn-primary">View Reports</a>
        </div>

        <div class="recent-activity">
            <h2>Recent Activity</h2>
            @if($recentActivity->count() > 0)
                @foreach($recentActivity as $activity)
                    <div class="activity-item">
                        <div class="student">{{ $activity->user->name }} ({{ $activity->user->username }})</div>
                        <div class="lesson">
                            @if($activity->subStrand)
                                {{ $activity->subStrand->learningArea->name ?? 'Unknown Subject' }} - {{ $activity->subStrand->name }}
                            @else
                                Content accessed
                            @endif
                        </div>
                        <div class="time">{{ $activity->last_accessed_at->diffForHumans() }}</div>
                    </div>
                @endforeach
            @else
                <p style="color: #999; text-align: center; padding: 20px;">No activity yet</p>
            @endif
        </div>
    </div>
</body>
</html>
