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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 40px 60px;
            text-align: center;
            position: relative;
        }
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        .top-nav {
            position: absolute;
            top: 20px;
            right: 40px;
            display: flex;
            gap: 15px;
        }
        .top-nav a {
            color: white;
            text-decoration: none;
            font-size: 0.9em;
            opacity: 0.9;
            transition: opacity 0.3s;
        }
        .top-nav a:hover { opacity: 1; }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            color: white;
            font-weight: 500;
            font-size: 0.9em;
            cursor: pointer;
            transition: background 0.3s;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: -40px auto 40px;
            padding: 0 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            text-align: center;
        }
        .stat-card h3 {
            color: #999;
            font-size: 0.85em;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            flex-direction: column;
        }
        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }
        .feature-icon {
            font-size: 2.5em;
            margin-bottom: 12px;
        }
        .feature-card h3 {
            font-size: 1.1em;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }
        .feature-card p {
            font-size: 0.9em;
            color: #666;
            margin-bottom: 16px;
            flex-grow: 1;
        }
        .feature-btn {
            padding: 10px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9em;
            cursor: pointer;
            transition: background 0.3s;
            align-self: flex-start;
        }
        .feature-btn:hover {
            background: #5568d3;
        }

        .recent-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .recent-section h2 {
            font-size: 1.3em;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .activity-item {
            padding: 15px;
            border-left: 4px solid #667eea;
            margin-bottom: 12px;
            background: #f9f9f9;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .activity-item:hover {
            background: #f0f2f5;
        }
        .activity-item .student {
            font-weight: 600;
            color: #667eea;
            display: block;
        }
        .activity-item .lesson {
            color: #666;
            font-size: 0.9em;
            margin-top: 4px;
        }
        .activity-item .time {
            color: #999;
            font-size: 0.85em;
            margin-top: 4px;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="top-nav">
            <a href="{{ route('teacher.analytics') }}">Analytics</a>
            <a href="{{ route('teacher.learner-profiles') }}">Profiles</a>
            <a href="{{ route('teacher.content-analytics') }}">Content</a>
            <a href="{{ route('teacher.reports') }}">Reports</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
        <h1>👨‍🏫 Teacher Dashboard</h1>
        <p>Welcome to the CBE Platform Teaching Suite</p>
    </div>

    <div class="container">
        <!-- Key Metrics -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Learners</h3>
                <div class="number">{{ $totalLearners }}</div>
            </div>
            <div class="stat-card">
                <h3>Active Now</h3>
                <div class="number">{{ $activeLearners }}</div>
            </div>
        </div>

        <!-- Advanced Features -->
        <div class="features-grid">
            <a href="{{ route('teacher.analytics') }}" class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Analytics</h3>
                <p>Engagement trends, subject performance, content metrics, and learner distribution analysis.</p>
                <button class="feature-btn">Explore →</button>
            </a>

            <a href="{{ route('teacher.learner-profiles') }}" class="feature-card">
                <div class="feature-icon">👥</div>
                <h3>Learner Profiles</h3>
                <p>View all learners, filter by grade, sort by performance, and drill into individual progress.</p>
                <button class="feature-btn">Browse →</button>
            </a>

            <a href="{{ route('teacher.content-analytics') }}" class="feature-card">
                <div class="feature-icon">📚</div>
                <h3>Content Analytics</h3>
                <p>Track content performance, access counts, completion rates, and subject-wise engagement.</p>
                <button class="feature-btn">View →</button>
            </a>

            <a href="{{ route('teacher.reports') }}" class="feature-card">
                <div class="feature-icon">📈</div>
                <h3>Reports</h3>
                <p>Generate comprehensive reports on learner progress and learning outcomes.</p>
                <button class="feature-btn">Generate →</button>
            </a>
        </div>

        <!-- Recent Activity -->
        <div class="recent-section">
            <h2>Recent Learner Activity</h2>
            @if($recentActivity->count() > 0)
                @foreach($recentActivity as $activity)
                    <div class="activity-item">
                        <span class="student">{{ $activity->user->name }} ({{ $activity->user->username }})</span>
                        <span class="lesson">
                            @if($activity->subStrand && $activity->subStrand->learningArea)
                                {{ $activity->subStrand->learningArea->name }} - {{ $activity->subStrand->name }}
                            @else
                                Content accessed
                            @endif
                        </span>
                        <span class="time">{{ $activity->last_accessed_at ? $activity->last_accessed_at->diffForHumans() : 'Unknown' }}</span>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <p>No learner activity yet. Students will appear here as they access content.</p>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
