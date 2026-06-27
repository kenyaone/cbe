<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Portal - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
            padding: 40px 20px;
        }
        header h1 {
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        .grades-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .grade-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: #333;
        }
        .grade-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .grade-card h2 {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .grade-card p {
            font-size: 0.95em;
            opacity: 0.8;
        }
        .grade-card:hover p {
            opacity: 1;
        }
        .icon {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon svg { width: 48px; height: 48px; }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            color: white;
            border: 2px solid white;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95em;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        .logout-btn:hover {
            background: white;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="top-bar">
            <div></div>
            <form method="POST" action="{{ route('learner.logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

        <header>
            <h1>Choose Your Grade Level</h1>
            <p>Select your grade to access your subjects and lessons</p>
        </header>

        <div class="grades-grid">
            @foreach($gradeLevels as $grade)
                <a href="{{ route('learner.grade', $grade) }}" class="grade-card">
                    <div class="icon">
                        {{-- Open book SVG --}}
                        <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 10C24 10 14 6 6 8v28c8-2 18 2 18 2s10-4 18-2V8c-8-2-18 2-18 2z"
                                fill="rgba(255,255,255,0.25)" stroke="rgba(255,255,255,0.8)" stroke-width="2" stroke-linejoin="round"/>
                            <line x1="24" y1="10" x2="24" y2="38" stroke="rgba(255,255,255,0.8)" stroke-width="2"/>
                        </svg>
                    </div>
                    <h2>{{ $grade }}</h2>
                    <p>
                        @if($grade === 'PP1')
                            Pre-Primary 1
                        @elseif($grade === 'PP2')
                            Pre-Primary 2
                        @else
                            {{ str_replace(['Grade ', 'Form '], ['Grade ', 'Form '], $grade) }}
                        @endif
                    </p>
                </a>
            @endforeach
        </div>

        <div style="text-align: center; color: white; margin-top: 40px;">
            <p style="font-size: 0.9em; opacity: 0.8;">Click on your grade to get started →</p>
        </div>
    </div>
</body>
</html>
