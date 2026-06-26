<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gradeLevel }} Subjects - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .header h1 { font-size: 2em; margin-bottom: 10px; }
        .back-link {
            display: inline-block;
            color: white;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 0.95em;
            opacity: 0.9;
        }
        .back-link:hover { opacity: 1; }
        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }
        .subject-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            border-left: 4px solid #667eea;
        }
        .subject-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .subject-card h3 { font-size: 1.1em; margin-bottom: 10px; }
        .subject-code { font-size: 0.8em; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('learner.dashboard') }}" class="back-link">← Back to Grades</a>

        <div class="header">
            <h1>{{ $gradeLevel }} - Subjects</h1>
            <p>Select a subject to view topics and lessons</p>
        </div>

        <div class="subjects-grid">
            @foreach($subjects as $subject)
                <a href="{{ route('learner.subject', [$gradeLevel, $subject->id]) }}" class="subject-card">
                    <div class="subject-code">{{ $subject->code }}</div>
                    <h3>{{ $subject->name }}</h3>
                </a>
            @endforeach
        </div>

        @if($subjects->isEmpty())
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>No subjects available for this grade yet.</p>
            </div>
        @endif
    </div>
</body>
</html>
