<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subStrand->name }} - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; }
        .breadcrumb { margin-bottom: 20px; font-size: 0.9em; }
        .breadcrumb a { color: #667eea; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #999; }
        header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        header h1 { font-size: 1.8em; margin-bottom: 10px; }
        .lesson-info { background: white; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .lesson-info h3 { color: #667eea; margin-bottom: 15px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .info-item { padding: 15px; background: #f8f9fa; border-radius: 6px; }
        .info-label { font-size: 0.85em; color: #999; margin-bottom: 5px; }
        .info-value { font-weight: 600; color: #333; }
        .resources { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .resources h3 { color: #667eea; margin-bottom: 15px; }
        .resource-item { background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 10px; border-left: 4px solid #667eea; }
        .resource-type { display: inline-block; background: #667eea; color: white; padding: 2px 8px; border-radius: 3px; font-size: 0.75em; font-weight: 600; }
        .resource-name { font-weight: 600; margin: 8px 0; }
        .resource-path { font-size: 0.85em; color: #999; margin-top: 5px; word-break: break-all; }
        .resource-size { font-size: 0.85em; color: #999; }
        .no-resources { color: #999; padding: 20px; text-align: center; }
        .video-player { margin-top: 20px; background: #000; border-radius: 8px; overflow: hidden; }
        video { width: 100%; height: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/">← CBE Platform</a>
            <span> / </span>
            <a href="{{ route('curriculum.type', $subStrand->strand->learningArea->curriculumType->name) }}">
                {{ $subStrand->strand->learningArea->curriculumType->name }}
            </a>
            <span> / </span>
            <a href="{{ route('curriculum.area', [
                $subStrand->strand->learningArea->curriculumType->name,
                $subStrand->strand->learningArea->id
            ]) }}">
                {{ $subStrand->strand->learningArea->name }}
            </a>
            <span> / </span>
            <a href="{{ route('curriculum.strand', [
                $subStrand->strand->learningArea->curriculumType->name,
                $subStrand->strand->learningArea->id,
                $subStrand->strand->id
            ]) }}">
                {{ $subStrand->strand->name }}
            </a>
            <span> / {{ $subStrand->name }}</span>
        </div>

        <header>
            <h1>{{ $subStrand->code }} {{ $subStrand->name }}</h1>
            @if($subStrand->description)
                <p>{{ $subStrand->description }}</p>
            @endif
        </header>

        <div class="lesson-info">
            <h3>📊 Lesson Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Learning Area</div>
                    <div class="info-value">{{ $subStrand->strand->learningArea->name }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Topic</div>
                    <div class="info-value">{{ $subStrand->strand->code }} {{ $subStrand->strand->name }}</div>
                </div>
                @if($subStrand->lesson_count)
                    <div class="info-item">
                        <div class="info-label">Total Lessons</div>
                        <div class="info-value">{{ $subStrand->lesson_count }}</div>
                    </div>
                @endif
                <div class="info-item">
                    <div class="info-label">Resources</div>
                    <div class="info-value">{{ $contentFiles->count() }}</div>
                </div>
            </div>
        </div>

        @if($subStrand->learningOutcomes->count() > 0)
        <div class="resources">
            <h3>🎯 Learning Outcomes</h3>
            <ul style="margin-left: 20px;">
                @foreach($subStrand->learningOutcomes as $outcome)
                    <li style="margin: 8px 0; color: #333;">{{ $outcome->description }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($subStrand->competencies->count() > 0)
        <div class="resources">
            <h3>💪 Core Competencies</h3>
            @foreach($subStrand->competencies as $competency)
                <div class="resource-item">
                    <strong>{{ $competency->type }}</strong>
                    @if($competency->description)
                        <p style="color: #666; margin: 5px 0;">{{ $competency->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        @if($subStrand->inquiryQuestions->count() > 0)
        <div class="resources">
            <h3>❓ Key Inquiry Questions</h3>
            <ul style="margin-left: 20px;">
                @foreach($subStrand->inquiryQuestions as $question)
                    <li style="margin: 8px 0; color: #333;">{{ $question->question }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($subStrand->learningExperiences->count() > 0)
        <div class="resources">
            <h3>📚 Suggested Learning Experiences</h3>
            <ul style="margin-left: 20px;">
                @foreach($subStrand->learningExperiences as $experience)
                    <li style="margin: 8px 0; color: #333;">{{ $experience->description }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($subStrand->values->count() > 0)
        <div class="resources">
            <h3>💎 Values</h3>
            @foreach($subStrand->values as $value)
                <div class="resource-item">
                    <strong>{{ $value->name }}</strong>
                    @if($value->description)
                        <p style="color: #666; margin: 5px 0;">{{ $value->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
        @endif

        <div class="resources">
            <h3>📚 Learning Resources</h3>
            @forelse($contentFiles as $content)
                <div class="resource-item">
                    <span class="resource-type">{{ $content->contentType->name }}</span>
                    <div class="resource-name">{{ $content->title }}</div>
                    @if($content->description)
                        <p style="color: #666; font-size: 0.9em; margin: 5px 0;">{{ $content->description }}</p>
                    @endif
                    <div class="resource-path">📁 {{ basename($content->file_path) }}</div>
                    @if($content->file_size)
                        <div class="resource-size">
                            💾 {{ number_format($content->file_size / 1024 / 1024, 2) }} MB
                        </div>
                    @endif

                    @if($content->contentType->name === 'Video')
                        @if(file_exists($content->file_path))
                            <div class="video-player" style="margin-top: 10px;">
                                <video controls controlsList="nodownload" style="width: 100%;">
                                    <source src="{{ route('stream.video', base64_encode($content->file_path)) }}" type="video/mp4">
                                    Your browser does not support the video tag. Please download the file directly.
                                </video>
                            </div>
                            <p style="font-size: 0.85em; color: #999; margin-top: 5px;">💡 Tip: Use the video player controls to play, pause, and seek through the lesson.</p>
                        @else
                            <p style="color: #999; margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 6px; text-align: center;">🔜 Coming Soon</p>
                        @endif

                    @elseif($content->contentType->name === 'Interactive')
                        @if(file_exists($content->file_path))
                            <a href="{{ route('serve.interactive', base64_encode($content->file_path)) }}" target="_blank" class="btn-link" style="display: inline-block; margin-top: 10px; background: #667eea; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                🎮 Open Interactive Activity
                            </a>
                            <p style="font-size: 0.85em; color: #999; margin-top: 5px;">Opens in a new window for full interaction</p>
                        @else
                            <p style="color: #999; margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 6px; text-align: center;">🔜 Coming Soon</p>
                        @endif

                    @elseif($content->contentType->name === 'PDF')
                        @if(file_exists($content->file_path))
                            <div style="margin-top: 10px;">
                                <iframe src="{{ route('serve.pdf', base64_encode($content->file_path)) }}" style="width: 100%; height: 600px; border: 1px solid #ddd; border-radius: 6px;" frameborder="0"></iframe>
                                <p style="font-size: 0.85em; color: #999; margin-top: 10px;">
                                    <a href="{{ route('serve.pdf', base64_encode($content->file_path)) }}" target="_blank" style="color: #e74c3c; text-decoration: none;">📥 Download PDF</a>
                                </p>
                            </div>
                        @else
                            <p style="color: #999; margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 6px; text-align: center;">🔜 Coming Soon</p>
                        @endif

                    @elseif($content->contentType->name === 'HTML')
                        @if(file_exists($content->file_path))
                            <a href="{{ route('serve.interactive', base64_encode($content->file_path)) }}" target="_blank" class="btn-link" style="display: inline-block; margin-top: 10px; background: #27ae60; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                🌐 Open Interactive Content
                            </a>
                        @else
                            <p style="color: #999; margin-top: 10px; padding: 10px; background: #f0f0f0; border-radius: 6px; text-align: center;">🔜 Coming Soon</p>
                        @endif

                    @elseif($content->contentType->name === 'Lesson')
                        <a href="{{ route('view.lesson', $content->id) }}" class="btn-link" style="display: inline-block; margin-top: 10px; background: #667eea; color: white; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                            📚 View Lesson Content
                        </a>
                        <p style="font-size: 0.85em; color: #999; margin-top: 5px;">Full lesson with learning outcomes and activities</p>

                    @else
                        <p style="color: #999; margin-top: 10px; font-size: 0.9em;">Content type: {{ $content->contentType->name }}</p>
                    @endif
                </div>
            @empty
                <div class="no-resources">
                    📦 No resources yet for this lesson
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>
