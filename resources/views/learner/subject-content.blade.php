<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject->name }} - {{ $gradeLevel }}</title>
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
            gap: 8px;
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #666;
            flex-wrap: wrap;
            align-items: center;
        }
        .breadcrumb a { color: #667eea; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 28px 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .header h1 { font-size: 1.8em; margin-bottom: 6px; }
        .header p { opacity: 0.85; font-size: 0.95em; }

        .section { margin-bottom: 30px; }
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1em;
            font-weight: 700;
            color: #444;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e8eaf0;
        }
        .section-title .badge {
            background: #667eea;
            color: white;
            font-size: 0.75em;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        .section-title .icon { font-size: 1.3em; }

        .file-list { display: flex; flex-direction: column; gap: 10px; }
        .file-card {
            background: white;
            border-radius: 8px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 2px 6px rgba(0,0,0,0.07);
            transition: all 0.2s ease;
            border-left: 4px solid #667eea;
        }
        .file-card:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .file-card.video { border-left-color: #e53e3e; }
        .file-card.pdf   { border-left-color: #dd6b20; }
        .file-card.html  { border-left-color: #38a169; }

        .file-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3em;
            flex-shrink: 0;
        }
        .file-icon.video { background: #fff5f5; }
        .file-icon.pdf   { background: #fffaf0; }
        .file-icon.html  { background: #f0fff4; }

        .file-name { font-size: 0.98em; font-weight: 500; }
        .file-type { font-size: 0.8em; color: #999; margin-top: 2px; }

        .empty-section {
            color: #aaa;
            font-size: 0.9em;
            padding: 12px 16px;
            background: white;
            border-radius: 8px;
            border: 1px dashed #ddd;
        }
        .no-content {
            text-align: center;
            padding: 50px 20px;
            color: #999;
            background: white;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('learner.dashboard') }}">Grades</a>
            / <a href="{{ route('learner.grade', $gradeLevel) }}">{{ $gradeLevel }}</a>
            / <span>{{ $subject->name }}</span>
        </div>

        <div class="header">
            <h1>{{ $subject->name }}</h1>
            <p>{{ $gradeLevel }}</p>
        </div>

        @if($videos->isEmpty() && $pdfs->isEmpty() && $htmls->isEmpty())
            <div class="no-content">
                <p>No content available for this subject yet.</p>
            </div>
        @else

        @if($videos->isNotEmpty())
        <div class="section">
            <div class="section-title">
                <span class="icon">▶</span> Videos
                <span class="badge">{{ $videos->count() }}</span>
            </div>
            <div class="file-list">
                @foreach($videos as $file)
                <a href="{{ route('stream.video', base64_encode($file->file_path)) }}"
                   class="file-card video" target="_blank">
                    <div class="file-icon video">▶</div>
                    <div>
                        <div class="file-name">{{ $file->title }}</div>
                        <div class="file-type">Video</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($pdfs->isNotEmpty())
        <div class="section">
            <div class="section-title">
                <span class="icon">📄</span> Documents / Notes
                <span class="badge">{{ $pdfs->count() }}</span>
            </div>
            <div class="file-list">
                @foreach($pdfs as $file)
                <a href="{{ route('serve.pdf', base64_encode($file->file_path)) }}"
                   class="file-card pdf" target="_blank">
                    <div class="file-icon pdf">📄</div>
                    <div>
                        <div class="file-name">{{ $file->title }}</div>
                        <div class="file-type">PDF Document</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if($htmls->isNotEmpty())
        <div class="section">
            <div class="section-title">
                <span class="icon">🎮</span> Interactive / Activities
                <span class="badge">{{ $htmls->count() }}</span>
            </div>
            <div class="file-list">
                @foreach($htmls as $file)
                <a href="{{ route('serve.interactive', base64_encode($file->file_path)) }}"
                   class="file-card html" target="_blank">
                    <div class="file-icon html">🎮</div>
                    <div>
                        <div class="file-name">{{ $file->title }}</div>
                        <div class="file-type">Interactive Activity</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @endif
    </div>
</body>
</html>
