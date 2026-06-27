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
        .container { max-width: 1200px; margin: 0 auto; }
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
            margin-bottom: 10px;
        }
        .resource-type.video { background: #10b981; }
        .resource-type.interactive { background: #f59e0b; }
        .resource-type.pdf { background: #ef4444; }
        .player {
            background: #000;
            border-radius: 8px;
            overflow: hidden;
            margin: 15px 0;
        }
        .player video, .player iframe {
            width: 100%;
            height: auto;
            min-height: 300px;
            display: block;
        }
        .error-message {
            background: #fecaca;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
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
            <span>{{ $lesson->name }}</span>
        </div>

        <div class="header">
            <h1>{{ $lesson->name }}</h1>
            <p>{{ $subject->name }} - {{ $gradeLevel }}</p>
        </div>

        <div class="resources">
            @if($contentFiles->isEmpty())
                <div class="error-message">
                    <strong>No content available</strong> - This lesson doesn't have any files yet.
                </div>
            @else
                @foreach($contentFiles as $file)
                    <div class="resource-item">
                        <div class="resource-type {{ strtolower($file->contentType->name) }}">
                            {{ $file->contentType->name }}
                        </div>
                        <h3>{{ $file->title }}</h3>

                        @if($file->contentType->name === 'Video')
                            <div class="player">
                                <video controls>
                                    <source src="{{ route('stream.video', ['filePath' => base64_encode($file->file_path)]) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @elseif($file->contentType->name === 'Interactive')
                            <div class="player">
                                <iframe src="{{ route('serve.interactive', ['filePath' => base64_encode($file->file_path)]) }}" style="border: none;"></iframe>
                            </div>
                        @elseif($file->contentType->name === 'PDF')
                            <div class="player">
                                <iframe src="{{ route('serve.pdf', ['filePath' => base64_encode($file->file_path)]) }}" style="border: none;"></iframe>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</body>
</html>
