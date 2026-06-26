<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $topic->name }} - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f7fa;
            padding: 20px;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .breadcrumb {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #666;
            flex-wrap: wrap;
        }
        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
        }
        .breadcrumb a:hover { text-decoration: underline; }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .header h1 { font-size: 2em; margin-bottom: 10px; }
        .lessons-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .lesson-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
            border-left: 4px solid #667eea;
        }
        .lesson-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .lesson-card h3 { font-size: 1.1em; margin-bottom: 5px; }
        .lesson-code { font-size: 0.85em; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('learner.dashboard') }}">Grades</a>
            <span>/</span>
            <a href="{{ route('learner.grade', $gradeLevel) }}">{{ $gradeLevel }}</a>
            <span>/</span>
            <a href="{{ route('learner.subject', [$gradeLevel, $subject->id]) }}">{{ $subject->name }}</a>
            <span>/</span>
            <span>{{ $topic->name }}</span>
        </div>

        <div class="header">
            <h1>{{ $topic->name }}</h1>
            <p>{{ $subject->name }} - {{ $gradeLevel }}</p>
        </div>

        <div class="lessons-list">
            @foreach($lessons as $lesson)
                <a href="{{ route('learner.lesson', [$gradeLevel, $subject->id, $topic->id, $lesson->id]) }}" class="lesson-card">
                    <div class="lesson-code">{{ $lesson->code }} - {{ $lesson->name }}</div>
                    <h3>{{ $lesson->name }}</h3>
                </a>
            @endforeach
        </div>

        @if($lessons->isEmpty())
            <div style="text-align: center; padding: 40px; color: #999;">
                <p>No lessons available for this topic yet.</p>
            </div>
        @endif
    </div>
</body>
</html>
