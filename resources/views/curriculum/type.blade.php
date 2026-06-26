<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $curriculumType->name }} - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .breadcrumb { margin-bottom: 20px; }
        .breadcrumb a { color: #667eea; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        header h1 { font-size: 2em; margin-bottom: 10px; }
        header p { opacity: 0.9; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.3s; cursor: pointer; text-decoration: none; color: inherit; border-left: 4px solid #667eea; }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 16px rgba(0,0,0,0.15); }
        .card h3 { color: #667eea; margin-bottom: 8px; }
        .card p { color: #666; font-size: 0.9em; line-height: 1.5; }
        .card-meta { margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee; font-size: 0.85em; color: #999; }
        .badge { display: inline-block; background: #667eea; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/">← CBE Platform</a>
        </div>

        <header>
            <h1>{{ $curriculumType->name }}</h1>
            <p>{{ $curriculumType->description }}</p>
        </header>

        <div class="grid">
            @foreach($learningAreas as $area)
                <a href="{{ route('curriculum.area', [$curriculumType->name, $area->id]) }}" class="card">
                    <h3>{{ $area->name }}</h3>
                    <p>{{ Str::limit($area->description, 80) }}</p>
                    <div class="card-meta">
                        <span class="badge">{{ $area->strands->count() }} Topics</span>
                        <span class="badge" style="margin-left: 5px;">{{ $area->lessons_per_week }} lessons/week</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
