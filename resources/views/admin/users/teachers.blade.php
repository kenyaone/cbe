<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Admin</title>
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
        .create-btn {
            padding: 12px 24px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .create-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
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

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
        }
        .pagination a, .pagination span {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #667eea;
        }
        .pagination a:hover {
            background: #667eea;
            color: white;
        }
        .pagination .active span {
            background: #667eea;
            color: white;
            border-color: #667eea;
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
        <h1>👨‍🏫 Manage Teachers</h1>
        <div class="header-nav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.admins') }}">Admins</a>
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
            <h2 style="font-size: 1.3em;">Teachers ({{ $teachers->total() }})</h2>
            <a href="{{ route('admin.teachers.create') }}" class="create-btn">+ Add New Teacher</a>
        </div>

        @if($teachers->isEmpty())
            <div class="empty">
                <p>No teachers found. <a href="{{ route('admin.teachers.create') }}" style="color: #667eea;">Create one →</a></p>
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
                    @foreach($teachers as $teacher)
                        <tr>
                            <td><strong>{{ $teacher->name }}</strong></td>
                            <td>{{ $teacher->username }}</td>
                            <td>{{ $teacher->email }}</td>
                            <td>{{ $teacher->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="actions">
                                    <form method="POST" action="{{ route('admin.teachers.reset-password', $teacher->id) }}" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn-small btn-reset" onclick="return confirm('Reset password for {{ $teacher->name }}?')">
                                            🔐 Reset Password
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pagination">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</body>
</html>
