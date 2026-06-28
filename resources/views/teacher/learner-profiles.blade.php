<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Profiles - Teacher Dashboard</title>
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
        .header nav a:hover { opacity: 0.8; }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .controls {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: flex-end;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .control-group {
            display: flex;
            flex-direction: column;
        }
        .control-group label { font-size: 0.9em; font-weight: 600; margin-bottom: 5px; }
        .control-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; }

        .learners-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .learner-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            color: #333;
            display: flex;
            flex-direction: column;
        }
        .learner-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .learner-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            gap: 12px;
        }
        .learner-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.5em;
        }
        .learner-info h3 {
            font-size: 1em;
            margin-bottom: 2px;
        }
        .learner-info p {
            font-size: 0.85em;
            color: #999;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-progressing { background: #fff3cd; color: #856404; }
        .status-inactive { background: #f8d7da; color: #721c24; }

        .stats-mini {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 12px;
        }
        .stat {
            background: #f9f9f9;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-value { font-size: 1.4em; font-weight: bold; color: #667eea; }
        .stat-label { font-size: 0.75em; color: #999; margin-top: 3px; }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e5e5e5;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 15px;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transition: width 0.3s;
        }

        .view-btn {
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9em;
            margin-top: auto;
        }
        .view-btn:hover { background: #5568d3; }

        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            color: white;
            font-weight: 500;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }

        .empty {
            text-align: center;
            padding: 60px 20px;
            color: #999;
            background: white;
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>👥 Learner Profiles</h1>
        <nav>
            <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <a href="{{ route('teacher.analytics') }}">Analytics</a>
            <a href="{{ route('teacher.content-analytics') }}">Content</a>
            <a href="{{ route('teacher.reports') }}">Reports</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <div class="controls">
            <div class="control-group">
                <label>Filter by Grade</label>
                <select onchange="window.location.href = '{{ route('teacher.learner-profiles') }}?grade=' + this.value{{ $sortBy ? "&sort=$sortBy" : '' }}'">
                    <option value="">All Grades</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" {{ $gradeFilter === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div class="control-group">
                <label>Sort By</label>
                <select onchange="window.location.href = '{{ route('teacher.learner-profiles') }}{{ $gradeFilter ? '?grade=' . $gradeFilter : '?' }}sort=' + this.value">
                    <option value="total_access" {{ $sortBy === 'total_access' ? 'selected' : '' }}>Total Access</option>
                    <option value="completion_rate" {{ $sortBy === 'completion_rate' ? 'selected' : '' }}>Completion Rate</option>
                    <option value="status" {{ $sortBy === 'status' ? 'selected' : '' }}>Status</option>
                </select>
            </div>
        </div>

        @if($learners->isEmpty())
            <div class="empty">
                <p>No learners found yet.</p>
            </div>
        @else
            <div class="learners-grid">
                @foreach($learners as $learner)
                    <div class="learner-card">
                        <div class="learner-header">
                            <div class="learner-avatar">{{ substr($learner['name'], 0, 1) }}</div>
                            <div class="learner-info">
                                <h3>{{ $learner['name'] }}</h3>
                                <p>{{ $learner['grade'] ?? 'No grade' }}</p>
                            </div>
                        </div>

                        <span class="status-badge status-{{ $learner['status'] }}">
                            {{ ucfirst($learner['status']) }}
                        </span>

                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $learner['completion_rate'] }}%"></div>
                        </div>

                        <div class="stats-mini">
                            <div class="stat">
                                <div class="stat-value">{{ $learner['total_access'] }}</div>
                                <div class="stat-label">Accessed</div>
                            </div>
                            <div class="stat">
                                <div class="stat-value">{{ $learner['completed'] }}</div>
                                <div class="stat-label">Completed</div>
                            </div>
                        </div>

                        <div class="stat" style="margin-bottom: 12px;">
                            <div class="stat-label">Completion Rate</div>
                            <div class="stat-value" style="color: #667eea;">{{ $learner['completion_rate'] }}%</div>
                        </div>

                        <a href="{{ route('teacher.learner-detail', $learner['id']) }}" class="view-btn">View Details →</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>
