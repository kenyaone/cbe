<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learner Progress - CBE Platform</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5em;
        }
        .header nav a {
            color: white;
            text-decoration: none;
            margin-left: 30px;
            font-weight: 500;
        }
        .header nav a:hover {
            opacity: 0.8;
        }
        .logout-btn {
            background: rgba(255,255,255,0.2);
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            color: white;
            font-weight: 500;
        }
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }
        .learner-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .learner-list-header {
            background: #667eea;
            color: white;
            padding: 20px;
            font-weight: 600;
        }
        .learner-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.3s;
        }
        .learner-item:hover {
            background: #f9f9f9;
        }
        .learner-item.active {
            background: #f0f0ff;
            border-left: 4px solid #667eea;
        }
        .learner-name {
            font-weight: 600;
            color: #333;
        }
        .learner-username {
            font-size: 0.85em;
            color: #999;
        }
        .progress-panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .progress-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .progress-header h2 {
            margin-bottom: 10px;
        }
        .lesson-item {
            padding: 15px;
            background: #f9f9f9;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .lesson-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .lesson-subject {
            font-size: 0.85em;
            color: #999;
            margin-bottom: 8px;
        }
        .lesson-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.85em;
            color: #666;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .status.completed {
            background: #d4edda;
            color: #155724;
        }
        .status.in-progress {
            background: #fff3cd;
            color: #856404;
        }
        .status.not-started {
            background: #f8f9fa;
            color: #666;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        @media (max-width: 900px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .learner-list {
                max-height: 300px;
                overflow-y: auto;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>📊 Learner Progress</h1>
        </div>
        <nav>
            <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
            <a href="{{ route('teacher.reports') }}">Reports</a>
            <form method="POST" action="{{ route('teacher.logout') }}" style="display: inline;">
                @csrf
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </nav>
    </div>

    <div class="container">
        <a href="{{ route('teacher.dashboard') }}" class="back-btn">← Back to Dashboard</a>

        <div class="content-grid">
            <div class="learner-list">
                <div class="learner-list-header">Learners ({{ $learners->count() }})</div>
                @foreach($learners as $learner)
                    <a href="{{ route('teacher.learner-progress', ['learner_id' => $learner->id]) }}" style="text-decoration: none; color: inherit;">
                        <div class="learner-item @if($selectedLearner && $selectedLearner->id === $learner->id) active @endif">
                            <div class="learner-name">{{ $learner->name }}</div>
                            <div class="learner-username">@{{ $learner->username }}</div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="progress-panel">
                @if($selectedLearner)
                    <div class="progress-header">
                        <h2>{{ $selectedLearner->name }}</h2>
                        <p>@{{ $selectedLearner->username }}</p>
                    </div>

                    @if($learnerLessons->count() > 0)
                        <p style="margin-bottom: 20px; color: #666;">Total lessons accessed: <strong>{{ $learnerLessons->count() }}</strong></p>
                        @foreach($learnerLessons as $lesson)
                            <div class="lesson-item">
                                <div class="lesson-name">
                                    {{ $lesson->subStrand->name ?? 'Unknown' }}
                                    <span class="status @if($lesson->status === 'completed') completed @elseif($lesson->status === 'in_progress') in-progress @else not-started @endif">
                                        {{ ucfirst(str_replace('_', ' ', $lesson->status)) }}
                                    </span>
                                </div>
                                <div class="lesson-subject">
                                    {{ $lesson->subStrand->learningArea->name ?? 'Unknown Subject' }}
                                </div>
                                <div class="lesson-meta">
                                    <span>Progress: {{ $lesson->progress_percentage ?? 0 }}%</span>
                                    <span>{{ $lesson->last_accessed_at?->diffForHumans() ?? 'Not accessed' }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <p>No lessons accessed yet</p>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <p>Select a learner to view their progress</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
