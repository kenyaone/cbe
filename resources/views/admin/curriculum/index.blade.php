<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curriculum Structure - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f7fa; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; display: flex; justify-content: space-between; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; }
        .container { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .grade-card { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .grade-title { font-size: 1.3em; font-weight: 600; color: #667eea; margin-bottom: 15px; }
        .grade-subjects { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .subject { background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 4px solid #667eea; }
        .subject-name { font-weight: 600; color: #333; }
        .subject-count { font-size: 0.9em; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📖 Curriculum Structure</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.users') }}">Admins</a>
        </div>
    </div>

    <div class="container">
        @foreach($grades as $grade)
            <div class="grade-card">
                <div class="grade-title">{{ $grade }}</div>
                <div class="grade-subjects">
                    @php
                        $subjects = \App\Models\LearningArea::where('grade_level', $grade)->orderBy('order')->get();
                    @endphp
                    @foreach($subjects as $subject)
                        <div class="subject">
                            <div class="subject-name">{{ $subject->name }}</div>
                            <div class="subject-count">
                                {{ $subject->strands->count() }} strands
                                <br>
                                @php
                                    $files = 0;
                                    foreach ($subject->strands as $strand) {
                                        $files += \App\Models\ContentFile::whereIn('contentable_id', $strand->subStrands->pluck('id')->toArray())->count();
                                    }
                                @endphp
                                {{ $files }} files
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</body>
</html>
