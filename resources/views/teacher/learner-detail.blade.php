<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $learner->name }} - Teacher Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f0f2f5;
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
        .header h1 { font-size: 1.5em; }
        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .breadcrumb {
            display: flex;
            gap: 8px;
            margin-bottom: 30px;
            font-size: 0.9em;
            color: #666;
        }
        .breadcrumb a { color: #667eea; text-decoration: none; cursor: pointer; }
        .breadcrumb a:hover { text-decoration: underline; }

        .profile-header {
            background: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3em;
            flex-shrink: 0;
        }
        .profile-info h2 { font-size: 1.5em; margin-bottom: 8px; }
        .profile-info p { color: #999; margin-bottom: 4px; }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .stat-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .stat-box .value { font-size: 1.6em; font-weight: bold; color: #667eea; }
        .stat-box .label { font-size: 0.85em; color: #999; margin-top: 4px; }

        .progress-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .progress-section h3 { margin-bottom: 20px; font-size: 1.1em; border-bottom: 2px solid #667eea; padding-bottom: 10px; }

        .progress-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
        .progress-item:last-child { border-bottom: none; }
        .progress-label { flex: 1; }
        .progress-label strong { display: block; margin-bottom: 4px; }
        .progress-label span { font-size: 0.85em; color: #999; }

        .progress-bar {
            flex: 2;
            height: 8px;
            background: #e5e5e5;
            border-radius: 4px;
            overflow: hidden;
            margin: 0 20px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .progress-stats {
            flex: 1;
            text-align: right;
        }
        .progress-stats .rate { font-size: 0.95em; font-weight: 600; color: #667eea; }
        .progress-stats .count { font-size: 0.85em; color: #999; margin-top: 2px; }

        .activity-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .activity-section h3 { margin-bottom: 20px; font-size: 1.1em; border-bottom: 2px solid #667eea; padding-bottom: 10px; }

        .activity-item {
            padding: 15px;
            border-left: 4px solid #667eea;
            background: #f9f9f9;
            margin-bottom: 12px;
            border-radius: 4px;
        }
        .activity-item .title { font-weight: 600; margin-bottom: 4px; }
        .activity-item .subject { font-size: 0.9em; color: #667eea; }
        .activity-item .time { font-size: 0.85em; color: #999; margin-top: 6px; }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            color: white;
            font-weight: 500;
        }

        .back-btn {
            color: white;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }
        .back-btn:hover { opacity: 0.8; }
    </style>
</head>
<body>
    <div class="header">
        <h1>👤 {{ $learner->name }}</h1>
        <nav>
            <a href="{{ route('teacher.learner-profiles') }}" class="back-btn">← Back to Profiles</a>
            <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <div class="profile-header">
            <div class="profile-avatar">{{ substr($learner->name, 0, 1) }}</div>
            <div class="profile-info">
                <h2>{{ $learner->name }}</h2>
                <p><strong>Username:</strong> {{ $learner->username }}</p>
                <p><strong>Grade Level:</strong> {{ $learner->grade_level ?? 'Not set' }}</p>

                <div class="grid-3">
                    <div class="stat-box">
                        <div class="value">{{ $stats['total_access'] }}</div>
                        <div class="label">Total Accessed</div>
                    </div>
                    <div class="stat-box">
                        <div class="value">{{ $stats['completed'] }}</div>
                        <div class="label">Completed</div>
                    </div>
                    <div class="stat-box">
                        <div class="value">{{ $stats['completion_rate'] }}%</div>
                        <div class="label">Completion Rate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance by Subject -->
        <div class="progress-section">
            <h3>Performance by Subject</h3>
            @forelse($bySubject as $subject => $data)
                <div class="progress-item">
                    <div class="progress-label">
                        <strong>{{ $subject }}</strong>
                        <span>{{ $data['completed'] }} of {{ $data['total'] }} completed</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $data['completion_rate'] }}%"></div>
                    </div>
                    <div class="progress-stats">
                        <div class="rate">{{ $data['completion_rate'] }}%</div>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #999; padding: 20px;">No activity yet</p>
            @endforelse
        </div>

        <!-- Recent Activity -->
        <div class="activity-section">
            <h3>Recent Activity</h3>
            @forelse($recentActivity as $activity)
                <div class="activity-item">
                    <div class="title">{{ $activity->contentFile?->title ?? 'Content' }}</div>
                    <div class="subject">
                        @if($activity->subStrand && $activity->subStrand->learningArea)
                            {{ $activity->subStrand->learningArea->name }}
                        @else
                            Content Accessed
                        @endif
                    </div>
                    <div class="time">
                        Accessed {{ $activity->last_accessed_at ? $activity->last_accessed_at->diffForHumans() : 'Unknown' }}
                        • Progress: {{ $activity->progress_percentage }}%
                        • Status: <strong>{{ ucfirst($activity->status) }}</strong>
                    </div>
                </div>
            @empty
                <p style="text-align: center; color: #999; padding: 20px;">No activity yet</p>
            @endforelse
        </div>
    </div>
</body>
</html>
