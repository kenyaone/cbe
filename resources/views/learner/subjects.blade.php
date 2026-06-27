<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $gradeLevel }} - Subjects</title>
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
            display: flex; gap: 6px; margin-bottom: 18px;
            font-size: 0.85em; color: #888; flex-wrap: wrap; align-items: center;
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
        .header p  { opacity: 0.8; font-size: 0.9em; }

        .subjects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }

        .subject-card {
            background: white;
            border-radius: 12px;
            padding: 20px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #1f2937;
            box-shadow: 0 1px 4px rgba(0,0,0,0.08);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-left: 4px solid var(--accent, #667eea);
        }
        .subject-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }

        .subj-icon {
            width: 44px; height: 44px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            background: var(--icon-bg, #e0e7ff);
        }
        .subj-icon svg { width: 24px; height: 24px; }

        .subj-text h3 { font-size: 0.95em; font-weight: 600; color: #111827; }
        .subj-text p  { font-size: 0.75em; color: #9ca3af; margin-top: 2px; }

        .arrow { margin-left: auto; color: #d1d5db; flex-shrink: 0; }
        .arrow svg { width: 16px; height: 16px; }

        .empty { text-align: center; padding: 60px 20px; color: #9ca3af;
                 background: white; border-radius: 12px; }
    </style>
</head>
<body>
<div class="container">

    <div class="breadcrumb">
        <a href="{{ route('learner.dashboard') }}">Grades</a>
        <span class="sep">/</span>
        <span>{{ $gradeLevel }}</span>
    </div>

    <div class="header">
        <h1>{{ $gradeLevel }}</h1>
        <p>Choose a subject to start learning</p>
    </div>

    @if($subjects->isEmpty())
        <div class="empty"><p>No subjects available yet.</p></div>
    @else
    <div class="subjects-grid">
        @foreach($subjects as $subject)
        @php
            $name = strtolower($subject->name);
            // Determine accent colour + icon SVG path based on subject name
            if (str_contains($name, 'math')) {
                $accent = '#3b82f6'; $bg = '#dbeafe';
                $icon = '<path d="M12 4v16M4 12h16" stroke="#3b82f6" stroke-width="2.5" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'english')) {
                $accent = '#8b5cf6'; $bg = '#ede9fe';
                $icon = '<path d="M4 6h16M4 10h10M4 14h12M4 18h8" stroke="#8b5cf6" stroke-width="2" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'kiswahili') || str_contains($name, 'swahili')) {
                $accent = '#ec4899'; $bg = '#fce7f3';
                $icon = '<path d="M4 6h16M4 10h10M4 14h12M4 18h8" stroke="#ec4899" stroke-width="2" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'science') || str_contains($name, 'physics') || str_contains($name, 'chemistry') || str_contains($name, 'biology')) {
                $accent = '#10b981'; $bg = '#d1fae5';
                $icon = '<circle cx="12" cy="12" r="3" fill="#10b981"/><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="#10b981" stroke-width="1.5" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'geography') || str_contains($name, 'social')) {
                $accent = '#f59e0b'; $bg = '#fef3c7';
                $icon = '<circle cx="12" cy="12" r="9" stroke="#f59e0b" stroke-width="1.5"/><path d="M12 3a15 15 0 010 18M3 12h18" stroke="#f59e0b" stroke-width="1.5"/>';
            } elseif (str_contains($name, 'history')) {
                $accent = '#ef4444'; $bg = '#fee2e2';
                $icon = '<path d="M12 8v4l3 3" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/><circle cx="12" cy="12" r="9" stroke="#ef4444" stroke-width="1.5"/>';
            } elseif (str_contains($name, 'cre') || str_contains($name, 'christian') || str_contains($name, 'religious') || str_contains($name, 'ire') || str_contains($name, 'islamic')) {
                $accent = '#6366f1'; $bg = '#e0e7ff';
                $icon = '<path d="M12 3v18M7 8h10" stroke="#6366f1" stroke-width="2" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'business') || str_contains($name, 'economics') || str_contains($name, 'commerce')) {
                $accent = '#0ea5e9'; $bg = '#e0f2fe';
                $icon = '<rect x="3" y="10" width="4" height="8" rx="1" fill="#0ea5e9"/><rect x="10" y="6" width="4" height="12" rx="1" fill="#0ea5e9"/><rect x="17" y="3" width="4" height="15" rx="1" fill="#0ea5e9"/>';
            } elseif (str_contains($name, 'computer') || str_contains($name, 'technology') || str_contains($name, 'technical') || str_contains($name, 'electrical') || str_contains($name, 'pre-tech')) {
                $accent = '#64748b'; $bg = '#f1f5f9';
                $icon = '<rect x="3" y="5" width="18" height="12" rx="2" stroke="#64748b" stroke-width="1.5"/><path d="M8 21h8M12 17v4" stroke="#64748b" stroke-width="1.5" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'agric')) {
                $accent = '#84cc16'; $bg = '#ecfccb';
                $icon = '<path d="M12 22V12m0 0C12 7 7 5 3 6c1 4 4 7 9 6zm0 0c0-5 5-7 9-6-1 4-4 7-9 6z" stroke="#84cc16" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
            } elseif (str_contains($name, 'creative') || str_contains($name, 'art') || str_contains($name, 'music') || str_contains($name, 'perform')) {
                $accent = '#f97316'; $bg = '#ffedd5';
                $icon = '<circle cx="12" cy="12" r="4" stroke="#f97316" stroke-width="1.5"/><path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="#f97316" stroke-width="1.5" stroke-linecap="round"/>';
            } elseif (str_contains($name, 'physical') || str_contains($name, 'sport') || str_contains($name, 'pe ')) {
                $accent = '#14b8a6'; $bg = '#ccfbf1';
                $icon = '<circle cx="12" cy="8" r="3" stroke="#14b8a6" stroke-width="1.5"/><path d="M6 20v-4a6 6 0 0112 0v4" stroke="#14b8a6" stroke-width="1.5" stroke-linecap="round"/>';
            } else {
                $accent = '#667eea'; $bg = '#e0e7ff';
                $icon = '<path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" stroke="#667eea" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>';
            }
        @endphp
        <a href="{{ route('learner.subject', [$gradeLevel, $subject->id]) }}"
           class="subject-card"
           style="--accent:{{ $accent }}; --icon-bg:{{ $bg }}">
            <div class="subj-icon">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    {!! $icon !!}
                </svg>
            </div>
            <div class="subj-text">
                <h3>{{ $subject->name }}</h3>
                <p>{{ $subject->code }}</p>
            </div>
            <span class="arrow">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </span>
        </a>
        @endforeach
    </div>
    @endif

</div>
</body>
</html>
