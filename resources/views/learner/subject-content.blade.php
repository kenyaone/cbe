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
            background: #f0f2f5;
            padding: 20px;
            min-height: 100vh;
        }
        .container { max-width: 900px; margin: 0 auto; }

        .breadcrumb {
            display: flex;
            gap: 6px;
            margin-bottom: 18px;
            font-size: 0.85em;
            color: #888;
            flex-wrap: wrap;
            align-items: center;
        }
        .breadcrumb a { color: #667eea; text-decoration: none; font-weight: 500; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .sep { color: #ccc; }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 26px 28px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(102,126,234,0.35);
        }
        .header h1 { font-size: 1.6em; font-weight: 700; margin-bottom: 4px; }
        .header p { opacity: 0.8; font-size: 0.9em; }

        /* ── Section ── */
        .section { margin-bottom: 28px; }
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }
        .section-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .section-icon.video  { background: #fee2e2; }
        .section-icon.pdf    { background: #ffedd5; }
        .section-icon.html   { background: #d1fae5; }
        .section-icon svg { width: 20px; height: 20px; }

        .section-label {
            font-size: 1em;
            font-weight: 700;
            color: #374151;
        }
        .section-count {
            margin-left: auto;
            background: #e5e7eb;
            color: #6b7280;
            font-size: 0.75em;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
        }

        /* ── File cards ── */
        .file-list { display: flex; flex-direction: column; gap: 8px; }

        .file-card {
            background: white;
            border-radius: 10px;
            padding: 13px 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #1f2937;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-left: 4px solid transparent;
        }
        .file-card:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
        }
        .file-card.video { border-left-color: #ef4444; }
        .file-card.pdf   { border-left-color: #f97316; }
        .file-card.html  { border-left-color: #10b981; }

        /* Per-file icon */
        .file-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .file-icon.video { background: #fef2f2; }
        .file-icon.pdf   { background: #fff7ed; }
        .file-icon.html  { background: #ecfdf5; }
        .file-icon svg   { width: 22px; height: 22px; }

        .file-info { flex: 1; min-width: 0; }
        .file-name {
            font-size: 0.92em;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .file-meta {
            font-size: 0.75em;
            color: #9ca3af;
            margin-top: 2px;
        }

        .file-arrow {
            color: #d1d5db;
            flex-shrink: 0;
        }
        .file-arrow svg { width: 16px; height: 16px; }

        /* Empty / no content */
        .no-content {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        }
        .no-content svg { width: 48px; height: 48px; margin: 0 auto 12px; display: block; color: #e5e7eb; }
    </style>
</head>
<body>
<div class="container">

    <div class="breadcrumb">
        <a href="{{ route('learner.dashboard') }}">Grades</a>
        <span class="sep">/</span>
        <a href="{{ route('learner.grade', $gradeLevel) }}">{{ $gradeLevel }}</a>
        <span class="sep">/</span>
        <span>{{ $subject->name }}</span>
    </div>

    <div class="header">
        <h1>{{ $subject->name }}</h1>
        <p>{{ $gradeLevel }}</p>
    </div>

    @if($videos->isEmpty() && $pdfs->isEmpty() && $htmls->isEmpty())
        <div class="no-content">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p>No content available for this subject yet.</p>
        </div>

    @else

    {{-- ── VIDEOS ── --}}
    @if($videos->isNotEmpty())
    <div class="section">
        <div class="section-header">
            <div class="section-icon video">
                {{-- Play button --}}
                <svg viewBox="0 0 24 24" fill="#ef4444" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#fecaca"/>
                    <polygon points="10,8 17,12 10,16" fill="#ef4444"/>
                </svg>
            </div>
            <span class="section-label">Video Lessons</span>
            <span class="section-count">{{ $videos->count() }}</span>
        </div>
        <div class="file-list">
            @foreach($videos as $file)
            <a href="{{ route('stream.video', base64_encode($file->file_path)) }}"
               class="file-card video" target="_blank">
                <div class="file-icon video">
                    {{-- Mini play icon --}}
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="10" stroke="#ef4444" stroke-width="1.5"/>
                        <polygon points="10,8 17,12 10,16" fill="#ef4444"/>
                    </svg>
                </div>
                <div class="file-info">
                    <div class="file-name">{{ $file->title }}</div>
                    <div class="file-meta">Video &middot; Tap to play</div>
                </div>
                <span class="file-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── PDFs / NOTES ── --}}
    @if($pdfs->isNotEmpty())
    <div class="section">
        <div class="section-header">
            <div class="section-icon pdf">
                {{-- Document icon --}}
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="4" y="2" width="12" height="16" rx="2" fill="#fed7aa"/>
                    <path d="M14 2l4 4" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                    <rect x="14" y="2" width="4" height="4" rx="1" fill="#fdba74"/>
                    <line x1="7" y1="9"  x2="14" y2="9"  stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="7" y1="12" x2="14" y2="12" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="7" y1="15" x2="11" y2="15" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <span class="section-label">Notes &amp; Documents</span>
            <span class="section-count">{{ $pdfs->count() }}</span>
        </div>
        <div class="file-list">
            @foreach($pdfs as $file)
            <a href="{{ route('serve.pdf', base64_encode($file->file_path)) }}"
               class="file-card pdf" target="_blank">
                <div class="file-icon pdf">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 2h9l5 5v15a1 1 0 01-1 1H6a1 1 0 01-1-1V3a1 1 0 011-1z" fill="#fed7aa" stroke="#f97316" stroke-width="1.5"/>
                        <path d="M14 2v5h5" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="8" y1="13" x2="16" y2="13" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                        <line x1="8" y1="17" x2="13" y2="17" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="file-info">
                    <div class="file-name">{{ $file->title }}</div>
                    <div class="file-meta">PDF &middot; Tap to open</div>
                </div>
                <span class="file-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── INTERACTIVE ── --}}
    @if($htmls->isNotEmpty())
    <div class="section">
        <div class="section-header">
            <div class="section-icon html">
                {{-- Lightning / interactive icon --}}
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="4" width="20" height="14" rx="3" fill="#a7f3d0"/>
                    <rect x="2" y="4" width="20" height="14" rx="3" stroke="#10b981" stroke-width="1.5"/>
                    <line x1="8" y1="22" x2="16" y2="22" stroke="#10b981" stroke-width="1.5" stroke-linecap="round"/>
                    <line x1="12" y1="18" x2="12" y2="22" stroke="#10b981" stroke-width="1.5" stroke-linecap="round"/>
                    <polygon points="13,8 9,13 12,13 11,17 15,12 12,12" fill="#10b981"/>
                </svg>
            </div>
            <span class="section-label">Interactive Activities</span>
            <span class="section-count">{{ $htmls->count() }}</span>
        </div>
        <div class="file-list">
            @foreach($htmls as $file)
            <a href="{{ route('serve.interactive', base64_encode($file->file_path)) }}"
               class="file-card html" target="_blank">
                <div class="file-icon html">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="2" y="4" width="20" height="14" rx="2" fill="#a7f3d0" stroke="#10b981" stroke-width="1.5"/>
                        <polygon points="13,8 9,13 12,13 11,16 15,11 12,11" fill="#10b981"/>
                    </svg>
                </div>
                <div class="file-info">
                    <div class="file-name">{{ $file->title }}</div>
                    <div class="file-meta">Interactive &middot; Tap to start</div>
                </div>
                <span class="file-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
</body>
</html>
