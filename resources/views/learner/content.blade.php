<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lesson->name }} - CBE Platform</title>
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
        .resources {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .resource-item {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }
        .resource-type {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .resource-name { font-weight: 600; font-size: 1.1em; margin-bottom: 10px; }
        .video-player {
            width: 100%;
            height: auto;
            border-radius: 6px;
            margin-top: 10px;
        }
        .btn-link {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            color: white;
            background: #667eea;
            transition: all 0.3s ease;
        }
        .btn-link:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        .no-resources {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .coming-soon {
            color: #999;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 6px;
            text-align: center;
            margin-top: 10px;
        }
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
            <a href="{{ route('learner.topic', [$gradeLevel, $subject->id, $topic->id]) }}">{{ $topic->name }}</a>
            <span>/</span>
            <span>{{ $lesson->name }}</span>
        </div>

        <div class="header">
            <h1>{{ $lesson->name }}</h1>
            <p>{{ $topic->name }} - {{ $subject->name }}</p>
        </div>

        <div class="resources">
            @forelse($contentFiles as $content)
                <div class="resource-item">
                    <span class="resource-type">{{ $content->contentType->name }}</span>
                    <div class="resource-name">{{ $content->title }}</div>

                    @if($content->contentType->name === 'Video')
                        @if(file_exists($content->file_path))
                            <video controls controlsList="nodownload" class="video-player" style="width: 100%; max-height: 500px;">
                                <source src="{{ route('stream.video', base64_encode($content->file_path)) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <div class="coming-soon">🔜 Coming Soon</div>
                        @endif

                    @elseif($content->contentType->name === 'Interactive')
                        @if(file_exists($content->file_path))
                            <a href="{{ route('serve.interactive', base64_encode($content->file_path)) }}" target="_blank" class="btn-link">
                                🎮 Open Interactive Activity
                            </a>
                        @else
                            <div class="coming-soon">🔜 Coming Soon</div>
                        @endif

                    @elseif($content->contentType->name === 'PDF')
                        @if(file_exists($content->file_path))
                            <iframe src="{{ route('serve.pdf', base64_encode($content->file_path)) }}" style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 6px; margin-top: 10px;" frameborder="0"></iframe>
                            <a href="{{ route('serve.pdf', base64_encode($content->file_path)) }}" target="_blank" class="btn-link" style="display: inline-block;">
                                📥 Download PDF
                            </a>
                        @else
                            <div class="coming-soon">🔜 Coming Soon</div>
                        @endif

                    @elseif($content->contentType->name === 'HTML')
                        @if(file_exists($content->file_path))
                            <a href="{{ route('serve.interactive', base64_encode($content->file_path)) }}" target="_blank" class="btn-link">
                                🌐 Open Interactive Content
                            </a>
                        @else
                            <div class="coming-soon">🔜 Coming Soon</div>
                        @endif
                    @endif
                </div>
            @empty
                <div class="no-resources">
                    📦 No resources available for this lesson yet.
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
