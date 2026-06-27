<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Learners - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        table { width: 100%; background: white; border-collapse: collapse; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table th { background: #667eea; color: white; padding: 15px; text-align: left; }
        table td { padding: 15px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>🔐 Manage Learners</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users') }}">Admins</a>
        </div>
    </div>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Grade Level</th>
                    <th>Last Login</th>
                    <th>Registered</th>
                </tr>
            </thead>
            <tbody>
                @forelse($learners as $learner)
                    <tr>
                        <td>{{ $learner->name }}</td>
                        <td>{{ $learner->username }}</td>
                        <td>{{ $learner->grade_level }}</td>
                        <td>{{ $learner->last_login_at ? $learner->last_login_at->format('M d, Y H:i') : 'Never' }}</td>
                        <td>{{ $learner->created_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999;">No learners found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $learners->links() }}
    </div>
</body>
</html>
