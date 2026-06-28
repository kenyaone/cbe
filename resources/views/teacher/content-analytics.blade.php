<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Analytics - Teacher Dashboard</title>
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
        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: flex-end;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        .filter-group label { font-size: 0.9em; font-weight: 600; margin-bottom: 5px; }
        .filter-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        @media (max-width: 1000px) { .grid-2 { grid-template-columns: 1fr; } }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card h2 { font-size: 1.2em; margin-bottom: 20px; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9em;
        }
        .table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e5e5e5;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #e5e5e5;
        }
        .table tr:hover { background: #fafafa; }

        .content-rank {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .rank-badge {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9em;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-pdf { background: #fed7aa; color: #92400e; }
        .badge-video { background: #fecaca; color: #7f1d1d; }
        .badge-interactive { background: #a7f3d0; color: #065f46; }
        .badge-text { background: #c7d2fe; color: #312e81; }
        .badge-html { background: #dbeafe; color: #082f49; }

        .performance-meter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .meter-bar {
            flex: 1;
            height: 6px;
            background: #e5e5e5;
            border-radius: 3px;
            overflow: hidden;
        }
        .meter-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        .meter-label { min-width: 50px; text-align: right; font-weight: 600; color: #667eea; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat-mini {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        .stat-mini .value { font-size: 1.4em; font-weight: bold; color: #667eea; }
        .stat-mini .label { font-size: 0.8em; color: #999; margin-top: 4px; }

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
    </style>
</head>
<body>
    <div class="header">
        <h1>📚 Content Analytics</h1>
        <nav>
            <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <a href="{{ route('teacher.analytics') }}">Analytics</a>
            <a href="{{ route('teacher.learner-profiles') }}">Learners</a>
            <a href="{{ route('teacher.reports') }}">Reports</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <div class="filters">
            <div class="filter-group">
                <label>Filter by Grade</label>
                <select onchange="window.location.href = '{{ route('teacher.content-analytics') }}?grade=' + this.value">
                    <option value="">All Grades</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}" {{ $gradeFilter === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="card">
                <h2>Most Accessed Content</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Content</th>
                            <th>Type</th>
                            <th>Access Count</th>
                            <th>Completion %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mostAccessed as $index => $content)
                            <tr>
                                <td>
                                    <div class="rank-badge">{{ $index + 1 }}</div>
                                </td>
                                <td>{{ Str::limit($content['title'], 30) }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($content['type']) }}">
                                        {{ $content['type'] }}
                                    </span>
                                </td>
                                <td>{{ $content['access_count'] }}</td>
                                <td>
                                    <div class="performance-meter">
                                        <div class="meter-bar">
                                            <div class="meter-fill" style="width: {{ $content['completion_rate'] }}%"></div>
                                        </div>
                                        <div class="meter-label">{{ $content['completion_rate'] }}%</div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align: center; color: #999;">No content accessed yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Content by Subject Performance</h2>
                @if($subjectContent->isEmpty())
                    <p style="text-align: center; color: #999; padding: 40px 20px;">No content data available</p>
                @else
                    <div class="stat-grid">
                        <div class="stat-mini">
                            <div class="label">Total Subjects</div>
                            <div class="value">{{ $subjectContent->count() }}</div>
                        </div>
                        <div class="stat-mini">
                            <div class="label">Total Files</div>
                            <div class="value">{{ $subjectContent->sum('file_count') }}</div>
                        </div>
                        <div class="stat-mini">
                            <div class="label">Total Access</div>
                            <div class="value">{{ $subjectContent->sum('access_count') }}</div>
                        </div>
                    </div>

                    <table class="table" style="font-size: 0.85em;">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Grade</th>
                                <th>Files</th>
                                <th>Access</th>
                                <th>Completion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectContent as $subject)
                                <tr>
                                    <td><strong>{{ Str::limit($subject['subject'], 20) }}</strong></td>
                                    <td>{{ $subject['grade'] }}</td>
                                    <td>{{ $subject['file_count'] }}</td>
                                    <td>{{ $subject['access_count'] }}</td>
                                    <td>
                                        <div class="performance-meter">
                                            <div class="meter-bar">
                                                <div class="meter-fill" style="width: {{ $subject['completion_rate'] }}%"></div>
                                            </div>
                                            <div class="meter-label" style="min-width: 40px;">{{ $subject['completion_rate'] }}%</div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
