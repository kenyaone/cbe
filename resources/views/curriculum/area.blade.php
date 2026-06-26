<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $learningArea->name }} - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background: #f5f7fa; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .breadcrumb { margin-bottom: 20px; font-size: 0.9em; }
        .breadcrumb a { color: #667eea; text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb span { color: #999; }
        header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-bottom: 30px; }
        header h1 { font-size: 2em; margin-bottom: 10px; }
        header p { opacity: 0.9; }
        .strands { display: flex; flex-direction: column; gap: 20px; }
        .strand { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .strand-header { background: #f8f9fa; padding: 20px; border-left: 4px solid #667eea; }
        .strand-header h2 { color: #667eea; margin-bottom: 5px; font-size: 1.3em; }
        .strand-header p { color: #666; font-size: 0.9em; }
        .sub-strands { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; padding: 20px; background: white; }
        .sub-strand { background: #f8f9fa; padding: 15px; border-radius: 6px; cursor: pointer; transition: all 0.3s; text-decoration: none; color: inherit; border: 2px solid transparent; }
        .sub-strand:hover { background: white; border-color: #667eea; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); }
        .sub-strand-code { font-weight: bold; color: #667eea; font-size: 0.9em; }
        .sub-strand-name { font-weight: 600; margin: 8px 0; }
        .sub-strand-meta { font-size: 0.85em; color: #999; }
    </style>
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/">← CBE Platform</a>
            <span> / </span>
            <a href="{{ route('curriculum.type', $learningArea->curriculumType->name) }}">{{ $learningArea->curriculumType->name }}</a>
            <span> / {{ $learningArea->name }}</span>
        </div>

        <header>
            <h1>{{ $learningArea->name }}</h1>
            <p>{{ $learningArea->description }}</p>
        </header>

        <div class="strands">
            @foreach($strands as $strand)
                <div class="strand">
                    <div class="strand-header">
                        <h2>{{ $strand->code }} {{ $strand->name }}</h2>
                    </div>
                    <div class="sub-strands">
                        @forelse($strand->subStrands as $subStrand)
                            <a href="{{ route('curriculum.sub-strand', [
                                $learningArea->curriculumType->name,
                                $learningArea->id,
                                $strand->id,
                                $subStrand->id
                            ]) }}" class="sub-strand">
                                <div class="sub-strand-code">{{ $subStrand->code }}</div>
                                <div class="sub-strand-name">{{ $subStrand->name }}</div>
                                @if($subStrand->lesson_count)
                                    <div class="sub-strand-meta">📚 {{ $subStrand->lesson_count }} lessons</div>
                                @endif
                                @if($subStrand->contentFiles->count() > 0)
                                    <div class="sub-strand-meta">🎬 {{ $subStrand->contentFiles->count() }} resources</div>
                                @endif
                            </a>
                        @empty
                            <p style="color: #999;">No sub topics yet</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
