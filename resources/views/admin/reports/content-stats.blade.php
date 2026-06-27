<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Statistics - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; }
        table th { background: #667eea; color: white; padding: 12px; text-align: left; }
        table td { padding: 12px; border-bottom: 1px solid #eee; }
        table tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📁 Content Statistics</h1>
        <div>
            <a href="{{ route('admin.reports') }}">← Back to Reports</a>
        </div>
    </div>

    <div class="container">
        <div class="section">
            <h2 style="margin-bottom: 15px;">Content by Type</h2>
            <table>
                <thead>
                    <tr>
                        <th>Content Type</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_type as $type)
                        <tr>
                            <td>{{ $type->name }}</td>
                            <td>{{ $type->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2 style="margin-bottom: 15px;">Content by Grade Level</h2>
            <table>
                <thead>
                    <tr>
                        <th>Grade Level</th>
                        <th>Number of Files</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_grade as $grade)
                        <tr>
                            <td>{{ $grade->grade_level }}</td>
                            <td>{{ $grade->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section">
            <h2 style="margin-bottom: 15px;">Content by Subject (Top 10)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Number of Files</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($by_subject->take(10) as $subject)
                        <tr>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
