<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CBE Platform - Curriculum Browser</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        header { color: white; text-align: center; margin-bottom: 40px; }
        header h1 { font-size: 2.5em; margin-bottom: 10px; }
        header p { font-size: 1.1em; opacity: 0.9; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 8px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); transition: transform 0.3s, box-shadow 0.3s; cursor: pointer; text-decoration: none; color: inherit; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.3); }
        .card h2 { color: #667eea; margin-bottom: 10px; font-size: 1.8em; }
        .card p { color: #666; line-height: 1.6; }
        .card-meta { margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; font-size: 0.9em; color: #999; }
        a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>📚 CBE Platform</h1>
            <p>Competency-Based Education Curriculum Browser</p>
        </header>

        <div class="grid">
            @foreach($curriculumTypes as $type)
                <a href="{{ route('curriculum.type', $type->name) }}" class="card">
                    <h2>{{ $type->name }}</h2>
                    <p>{{ $type->description }}</p>
                    <div class="card-meta">
                        <strong>{{ $type->learningAreas->count() }}</strong> Learning Areas
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</body>
</html>
