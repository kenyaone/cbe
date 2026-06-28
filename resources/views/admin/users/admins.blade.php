<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
        .header-nav {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        .header-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95em;
        }
        .header-nav a:hover { opacity: 0.8; }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            color: white;
            font-weight: 500;
            cursor: pointer;
            font-size: 0.95em;
        }
        .logout-btn:hover { background: rgba(255,255,255,0.3); }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #155724;
        }

        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        table th {
            background: #f5f5f5;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e5e5e5;
        }
        table td {
            padding: 15px;
            border-bottom: 1px solid #e5e5e5;
        }
        table tr:hover { background: #f9f9f9; }

        .actions {
            display: flex;
            gap: 10px;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.85em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-reset {
            background: #ffc107;
            color: #333;
        }
        .btn-reset:hover {
            background: #ffb300;
            transform: translateY(-1px);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: #e0e7ff;
            color: #667eea;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }

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
        <h1>🔐 Manage Admins</h1>
        <div class="header-nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.teachers') }}">Teachers</a>
            <a href="{{ route('admin.learners') }}">Learners</a>
            <form method="POST" action="{{ route('admin.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="controls">
            <h2 style="font-size: 1.3em;">Admin Accounts ({{ $admins->total() }})</h2>
        </div>

        @if($admins->isEmpty())
            <div class="empty">
                <p>No admin accounts found.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                        <tr>
                            <td>
                                <strong>{{ $admin->name }}</strong>
                                <span class="badge">Admin</span>
                            </td>
                            <td>{{ $admin->username }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <form method="POST" action="{{ route('admin.admins.reset-password', $admin->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-small btn-reset" onclick="return confirm('Reset password for {{ $admin->name }}?')">
                                            🔐 Reset Password
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</body>
</html>
