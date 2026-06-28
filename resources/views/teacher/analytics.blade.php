<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Teacher Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            max-width: 1400px;
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
        .filter-group input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; }
        .filter-btn {
            padding: 8px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }
        .filter-btn:hover { background: #5568d3; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .grid-1 { display: grid; grid-template-columns: 1fr; gap: 30px; margin-bottom: 30px; }
        @media (max-width: 1200px) { .grid-2 { grid-template-columns: 1fr; } }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card h2 { font-size: 1.2em; margin-bottom: 20px; color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }

        .chart-container { position: relative; height: 350px; }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        .metric {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid #667eea;
        }
        .metric-value { font-size: 1.8em; font-weight: bold; color: #667eea; }
        .metric-label { font-size: 0.85em; color: #999; margin-top: 5px; }

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

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .badge-high { background: #d4edda; color: #155724; }
        .badge-medium { background: #fff3cd; color: #856404; }
        .badge-low { background: #f8d7da; color: #721c24; }

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
        <h1>📊 Analytics</h1>
        <nav>
            <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <a href="{{ route('teacher.learner-profiles') }}">Learners</a>
            <a href="{{ route('teacher.content-analytics') }}">Content</a>
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
                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
            </div>
            <div class="filter-group">
                <label>End Date</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
            </div>
            <button class="filter-btn" onclick="applyFilters()">Apply Filters</button>
        </div>

        <!-- Engagement Trend -->
        <div class="grid-1">
            <div class="card">
                <h2>Daily Engagement Trend</h2>
                <div class="chart-container">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Learner Engagement & Subject Performance -->
        <div class="grid-2">
            <div class="card">
                <h2>Learner Engagement Distribution</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Learner</th>
                            <th>Grade</th>
                            <th>Total Access</th>
                            <th>Completion Rate</th>
                            <th>Avg Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($learnerEngagement as $learner)
                            <tr>
                                <td><strong>{{ $learner['name'] }}</strong></td>
                                <td>{{ $learner['grade'] ?? 'N/A' }}</td>
                                <td>{{ $learner['total_access'] }}</td>
                                <td>
                                    <span class="badge {{ $learner['completion_rate'] >= 75 ? 'badge-high' : ($learner['completion_rate'] >= 50 ? 'badge-medium' : 'badge-low') }}">
                                        {{ $learner['completion_rate'] }}%
                                    </span>
                                </td>
                                <td>{{ $learner['avg_progress'] }}%</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align: center; color: #999;">No learner data yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>Subject Performance Metrics</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Grade</th>
                            <th>Access Count</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjectMetrics as $subject)
                            <tr>
                                <td><strong>{{ $subject['name'] }}</strong></td>
                                <td>{{ $subject['grade'] }}</td>
                                <td>{{ $subject['total_access'] }}</td>
                                <td>
                                    <span class="badge {{ $subject['completion_rate'] >= 75 ? 'badge-high' : ($subject['completion_rate'] >= 50 ? 'badge-medium' : 'badge-low') }}">
                                        {{ $subject['completion_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: #999;">No subject data yet</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Content Type Performance -->
        <div class="grid-1">
            <div class="card">
                <h2>Content Type Performance</h2>
                <div class="metric-grid">
                    @forelse($contentMetrics as $metric)
                        <div class="metric">
                            <div class="metric-label">{{ $metric['type'] }}</div>
                            <div class="metric-value">{{ $metric['access_count'] }}</div>
                            <div class="metric-label" style="color: #667eea; margin-top: 8px;">{{ $metric['completion_rate'] }}% complete</div>
                        </div>
                    @empty
                        <p style="color: #999;">No content data yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        // Engagement Trend Chart
        const trendData = {!! json_encode($dailyTrend) !!};
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: Object.keys(trendData),
                datasets: [{
                    label: 'Daily Learner Activity',
                    data: Object.values(trendData),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        function applyFilters() {
            const startDate = document.querySelector('input[name="start_date"]').value;
            const endDate = document.querySelector('input[name="end_date"]').value;
            window.location.href = `{{ route('teacher.analytics') }}?start_date=${startDate}&end_date=${endDate}`;
        }
    </script>
</body>
</html>
