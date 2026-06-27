<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f5f7fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar h1 { font-size: 1.5em; }
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        .navbar a:hover {
            background: rgba(255,255,255,0.2);
        }
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            margin: 10px 0;
        }
        .stat-label {
            color: #666;
            font-size: 0.9em;
        }
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .menu-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            color: inherit;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .menu-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        .menu-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .menu-desc {
            color: #666;
            font-size: 0.9em;
        }
        .recent-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .recent-section h2 {
            margin-bottom: 15px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #ddd;
            background: #f9f9f9;
        }
        table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🔐 Admin Dashboard</h1>
        <div>
            <a href="{{ route('admin.users') }}">Users</a>
            <a href="{{ route('admin.learners') }}">Learners</a>
            <a href="{{ route('admin.content') }}">Content</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" style="background: none; border: none; color: white; cursor: pointer; padding: 8px 12px; border-radius: 4px; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='none'">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        <h2 style="margin-bottom: 20px;">Platform Overview</h2>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Admin Users</div>
                <div class="stat-value">{{ $total_admins }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Learner Accounts</div>
                <div class="stat-value">{{ $total_learners }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Content Files</div>
                <div class="stat-value">{{ $total_content_files }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Grades</div>
                <div class="stat-value">{{ $total_grades }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Subjects</div>
                <div class="stat-value">{{ $total_subjects }}</div>
            </div>
        </div>

        <h2 style="margin: 30px 0 20px;">Management</h2>

        <div class="menu-grid">
            <a href="{{ route('admin.users') }}" class="menu-card">
                <div class="menu-icon">👥</div>
                <div class="menu-title">Admin Users</div>
                <div class="menu-desc">Create and manage admin accounts</div>
            </a>
            <a href="{{ route('admin.learners') }}" class="menu-card">
                <div class="menu-icon">📚</div>
                <div class="menu-title">Learners</div>
                <div class="menu-desc">View and manage learner accounts</div>
            </a>
            <a href="{{ route('admin.content') }}" class="menu-card">
                <div class="menu-icon">📁</div>
                <div class="menu-title">Content</div>
                <div class="menu-desc">Manage content files and organization</div>
            </a>
            <a href="{{ route('admin.curriculum') }}" class="menu-card">
                <div class="menu-icon">📖</div>
                <div class="menu-title">Curriculum</div>
                <div class="menu-desc">View curriculum structure</div>
            </a>
        </div>

        @if($recent_learners->count() > 0)
            <div class="recent-section">
                <h2>Recent Learner Registrations</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Grade Level</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_learners as $learner)
                            <tr>
                                <td>{{ $learner->name }}</td>
                                <td>{{ $learner->username }}</td>
                                <td>{{ $learner->grade_level }}</td>
                                <td>{{ $learner->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</body>
</html>
