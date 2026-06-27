<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - CBE Platform</title>
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
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-btn:hover {
            text-decoration: underline;
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
        .section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .section h2 {
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #f9f9f9;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #eee;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        tr:hover {
            background: #f9f9f9;
        }
        .progress-bar {
            background: #eee;
            height: 8px;
            border-radius: 4px;
            overflow: hidden;
            min-width: 100px;
        }
        .progress-fill {
            background: #667eea;
            height: 100%;
            border-radius: 4px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .badge.high {
            background: #d4edda;
            color: #155724;
        }
        .badge.medium {
            background: #fff3cd;
            color: #856404;
        }
        .badge.low {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>📈 Reports</h1>
        </div>
        <nav>
            <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <a href="{{ route('teacher.learner-progress') }}">Learner Progress</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <a href="{{ route('teacher.dashboard') }}" class="back-btn">← Back to Dashboard</a>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Lessons Accessed</h3>
                <div class="number">{{ $totalLessonsAccessed }}</div>
            </div>
            <div class="stat-card">
                <h3>Completed Lessons</h3>
                <div class="number">{{ $completedLessons }}</div>
            </div>
            <div class="stat-card">
                <h3>In Progress</h3>
                <div class="number">{{ $inProgressLessons }}</div>
            </div>
        </div>

        <!-- Learners by Grade -->
        @if($byGrade->count() > 0)
            <div class="section">
                <h2>Learners by Grade Level</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Grade Level</th>
                            <th>Total Learners</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($byGrade as $grade)
                            <tr>
                                <td>{{ $grade->grade_level ?? 'Not specified' }}</td>
                                <td><strong>{{ $grade->total_learners }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Most Accessed Content -->
        @if($mostAccessed->count() > 0)
            <div class="section">
                <h2>Most Accessed Content</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Content</th>
                            <th>Subject</th>
                            <th>Access Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($mostAccessed as $item)
                            <tr>
                                <td>
                                    @if($item->contentFile)
                                        {{ $item->contentFile->title }}
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td>
                                    @if($item->contentFile && $item->contentFile->contentable)
                                        @php
                                            $subject = null;
                                            if (method_exists($item->contentFile->contentable, 'strand')) {
                                                $subject = $item->contentFile->contentable->strand->learningArea->name ?? null;
                                            }
                                        @endphp
                                        {{ $subject ?? 'Unknown' }}
                                    @else
                                        Unknown
                                    @endif
                                </td>
                                <td><strong>{{ $item->access_count }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Learner Completion Rates -->
        @if($learnerStats->count() > 0)
            <div class="section">
                <h2>Learner Completion Rates</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Username</th>
                            <th>Lessons Accessed</th>
                            <th>Completed</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($learnerStats as $stat)
                            <tr>
                                <td><strong>{{ $stat['name'] }}</strong></td>
                                <td>@{{ $stat['username'] }}</td>
                                <td>{{ $stat['total_accessed'] }}</td>
                                <td>{{ $stat['completed'] }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="progress-bar" style="flex: 1;">
                                            <div class="progress-fill" style="width: {{ $stat['completion_rate'] }}%"></div>
                                        </div>
                                        <span class="badge @if($stat['completion_rate'] >= 75) high @elseif($stat['completion_rate'] >= 50) medium @else low @endif">
                                            {{ $stat['completion_rate'] }}%
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="section">
                <p style="text-align: center; color: #999;">No learner data available yet</p>
            </div>
        @endif
    </div>
</body>
</html>
