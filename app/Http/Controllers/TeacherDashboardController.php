<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LearnerProgress;
use App\Models\LearningArea;
use App\Models\ContentFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherDashboardController extends Controller
{

    public function showLoginForm()
    {
        return view('teacher.auth.login');
    }

    public function teacherLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|min:6',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'teacher') {
                Auth::logout();
                return back()->withErrors(['username' => 'Only teachers can access this area.']);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('teacher.dashboard'));
        }

        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->onlyInput('username');
    }

    protected function checkTeacherAuth()
    {
        if (!Auth::check() || Auth::user()->role !== 'teacher') {
            abort(403);
        }
    }

    public function dashboard()
    {
        $this->checkTeacherAuth();
        $totalLearners = User::where('role', 'learner')->count();
        $activeLearners = LearnerProgress::where('status', 'in_progress')
            ->select('user_id')
            ->distinct()
            ->count();

        $recentActivity = LearnerProgress::with(['user', 'subStrand.learningArea'])
            ->orderBy('last_accessed_at', 'desc')
            ->limit(10)
            ->get();

        return view('teacher.dashboard', compact('totalLearners', 'activeLearners', 'recentActivity'));
    }

    public function analytics(Request $request)
    {
        $this->checkTeacherAuth();

        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : Carbon::now()->subDays(30);
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : Carbon::now();

        // Engagement by Grade & Subject heatmap data
        $engagementMatrix = LearnerProgress::whereBetween('last_accessed_at', [$startDate, $endDate])
            ->with(['user', 'subStrand.learningArea'])
            ->get()
            ->groupBy(function ($item) {
                return $item->user?->grade_level ?? 'Unknown';
            })
            ->map(function ($gradeLevelItems) {
                return $gradeLevelItems->groupBy(function ($item) {
                    return $item->subStrand?->learningArea?->name ?? 'Other';
                })->map->count();
            });

        // Subject performance metrics
        $subjectMetrics = LearningArea::where('curriculum_type_id', 18)
            ->with(['contentFiles'])
            ->get()
            ->map(function ($subject) use ($startDate, $endDate) {
                $progress = LearnerProgress::whereHas('contentFile', function ($q) use ($subject) {
                    $q->where('contentable_type', 'App\Models\LearningArea')
                      ->where('contentable_id', $subject->id);
                })
                ->whereBetween('last_accessed_at', [$startDate, $endDate])
                ->get();

                return [
                    'name' => $subject->name,
                    'grade' => $subject->grade_level,
                    'total_access' => $progress->count(),
                    'completion_rate' => $progress->count() > 0
                        ? round($progress->where('status', 'completed')->count() / $progress->count() * 100, 1)
                        : 0,
                    'avg_progress' => $progress->count() > 0
                        ? round($progress->avg('progress_percentage'), 1)
                        : 0,
                ];
            })
            ->sortByDesc('total_access');

        // Learner engagement distribution
        $learnerEngagement = User::where('role', 'learner')
            ->with(['learnerProgress' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('last_accessed_at', [$startDate, $endDate]);
            }])
            ->get()
            ->map(function ($user) {
                $progress = $user->learnerProgress;
                $total = $progress->count();
                return [
                    'name' => $user->name,
                    'username' => $user->username,
                    'grade' => $user->grade_level,
                    'total_access' => $total,
                    'completed' => $progress->where('status', 'completed')->count(),
                    'in_progress' => $progress->where('status', 'in_progress')->count(),
                    'completion_rate' => $total > 0 ? round($progress->where('status', 'completed')->count() / $total * 100, 1) : 0,
                    'avg_progress' => $total > 0 ? round($progress->avg('progress_percentage'), 1) : 0,
                ];
            })
            ->sortByDesc('total_access');

        // Content type engagement
        $contentMetrics = ContentFile::whereHas('learnerProgress', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('last_accessed_at', [$startDate, $endDate]);
        })
        ->with(['learnerProgress' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('last_accessed_at', [$startDate, $endDate]);
        }])
        ->get()
        ->groupBy('content_type_id')
        ->map(function ($files, $typeId) {
            $types = [1 => 'PDF', 2 => 'Video', 3 => 'Text', 4 => 'HTML', 5 => 'Interactive'];
            return [
                'type' => $types[$typeId] ?? 'Unknown',
                'access_count' => $files->sum(fn($f) => $f->learnerProgress->count()),
                'completion_rate' => $files->isNotEmpty()
                    ? round($files->sum(fn($f) => $f->learnerProgress->where('status', 'completed')->count()) /
                             $files->sum(fn($f) => $f->learnerProgress->count()) * 100, 1)
                    : 0,
            ];
        });

        // Engagement trend (daily activity last 30 days)
        $dailyTrend = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = LearnerProgress::whereDate('last_accessed_at', $date)->count();
            $dailyTrend[$date] = $count;
        }

        return view('teacher.analytics', compact(
            'engagementMatrix',
            'subjectMetrics',
            'learnerEngagement',
            'contentMetrics',
            'dailyTrend',
            'startDate',
            'endDate'
        ));
    }

    public function learnerProfiles(Request $request)
    {
        $this->checkTeacherAuth();

        $gradeFilter = $request->get('grade');
        $sortBy = $request->get('sort', 'total_access');

        $learners = User::where('role', 'learner')
            ->when($gradeFilter, function ($q) use ($gradeFilter) {
                $q->where('grade_level', $gradeFilter);
            })
            ->with(['learnerProgress'])
            ->get()
            ->map(function ($user) {
                $progress = $user->learnerProgress;
                $total = $progress->count();
                $completed = $progress->where('status', 'completed')->count();
                $inProgress = $progress->where('status', 'in_progress')->count();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'grade' => $user->grade_level,
                    'total_access' => $total,
                    'completed' => $completed,
                    'in_progress' => $inProgress,
                    'completion_rate' => $total > 0 ? round($completed / $total * 100, 1) : 0,
                    'avg_progress' => $total > 0 ? round($progress->avg('progress_percentage'), 1) : 0,
                    'last_access' => $progress->max('last_accessed_at'),
                    'status' => $inProgress > 0 ? 'active' : ($completed > 0 ? 'progressing' : 'inactive'),
                ];
            });

        if ($sortBy === 'total_access') {
            $learners = $learners->sortByDesc('total_access');
        } elseif ($sortBy === 'completion_rate') {
            $learners = $learners->sortByDesc('completion_rate');
        } elseif ($sortBy === 'status') {
            $learners = $learners->sort(fn($a, $b) => strcmp($b['status'], $a['status']));
        }

        $grades = User::where('role', 'learner')
            ->select('grade_level')
            ->distinct()
            ->pluck('grade_level');

        return view('teacher.learner-profiles', compact('learners', 'grades', 'gradeFilter', 'sortBy'));
    }

    public function learnerDetail(Request $request, $learnerId)
    {
        $this->checkTeacherAuth();

        $learner = User::where('role', 'learner')->findOrFail($learnerId);
        $progress = $learner->learnerProgress()->with(['subStrand.learningArea', 'contentFile'])->get();

        $stats = [
            'total_access' => $progress->count(),
            'completed' => $progress->where('status', 'completed')->count(),
            'in_progress' => $progress->where('status', 'in_progress')->count(),
            'not_started' => $progress->where('status', 'not_started')->count(),
            'completion_rate' => $progress->count() > 0 ? round($progress->where('status', 'completed')->count() / $progress->count() * 100, 1) : 0,
            'avg_progress' => $progress->count() > 0 ? round($progress->avg('progress_percentage'), 1) : 0,
        ];

        // Group by subject
        $bySubject = $progress->groupBy(function ($item) {
            return $item->subStrand?->learningArea?->name ?? 'Other';
        })->map(function ($items) {
            return [
                'total' => $items->count(),
                'completed' => $items->where('status', 'completed')->count(),
                'completion_rate' => $items->count() > 0 ? round($items->where('status', 'completed')->count() / $items->count() * 100, 1) : 0,
            ];
        });

        // Recent activity
        $recentActivity = $progress->sortByDesc('last_accessed_at')->take(15);

        return view('teacher.learner-detail', compact('learner', 'stats', 'bySubject', 'recentActivity'));
    }

    public function contentAnalytics(Request $request)
    {
        $this->checkTeacherAuth();

        $gradeFilter = $request->get('grade');
        $contentTypeFilter = $request->get('content_type');

        // Most accessed content
        $mostAccessed = ContentFile::with(['learnerProgress'])
            ->when($contentTypeFilter, function ($q) use ($contentTypeFilter) {
                $q->where('content_type_id', $contentTypeFilter);
            })
            ->get()
            ->map(function ($file) {
                $progress = $file->learnerProgress;
                return [
                    'id' => $file->id,
                    'title' => $file->title,
                    'type' => ['', 'PDF', 'Video', 'Text', 'HTML', 'Interactive'][$file->content_type_id] ?? 'Unknown',
                    'access_count' => $progress->count(),
                    'completion_count' => $progress->where('status', 'completed')->count(),
                    'completion_rate' => $progress->count() > 0 ? round($progress->where('status', 'completed')->count() / $progress->count() * 100, 1) : 0,
                    'avg_progress' => $progress->count() > 0 ? round($progress->avg('progress_percentage'), 1) : 0,
                ];
            })
            ->sortByDesc('access_count')
            ->take(20);

        // Content by subject performance
        $subjectContent = LearningArea::where('curriculum_type_id', 18)
            ->when($gradeFilter, function ($q) use ($gradeFilter) {
                $q->where('grade_level', $gradeFilter);
            })
            ->with(['contentFiles'])
            ->get()
            ->map(function ($subject) {
                $allFiles = $subject->contentFiles;
                $allProgress = LearnerProgress::whereHas('contentFile', function ($q) use ($subject) {
                    $q->where('contentable_type', 'App\Models\LearningArea')
                      ->where('contentable_id', $subject->id);
                })->get();

                return [
                    'subject' => $subject->name,
                    'grade' => $subject->grade_level,
                    'file_count' => $allFiles->count(),
                    'access_count' => $allProgress->count(),
                    'completion_rate' => $allProgress->count() > 0 ? round($allProgress->where('status', 'completed')->count() / $allProgress->count() * 100, 1) : 0,
                ];
            });

        $grades = LearningArea::where('curriculum_type_id', 18)
            ->select('grade_level')
            ->distinct()
            ->pluck('grade_level');

        return view('teacher.content-analytics', compact('mostAccessed', 'subjectContent', 'grades', 'gradeFilter'));
    }

    public function learnerProgress(Request $request)
    {
        $this->checkTeacherAuth();
        $learners = User::where('role', 'learner')
            ->with(['learnerProgress' => function ($query) {
                $query->orderBy('last_accessed_at', 'desc');
            }])
            ->get();

        $selectedLearner = null;
        $learnerLessons = [];

        if ($request->has('learner_id')) {
            $selectedLearner = User::find($request->learner_id);
            if ($selectedLearner) {
                $learnerLessons = LearnerProgress::where('user_id', $selectedLearner->id)
                    ->with(['subStrand.learningArea', 'contentFile'])
                    ->orderBy('last_accessed_at', 'desc')
                    ->get();
            }
        }

        return view('teacher.learner-progress', compact('learners', 'selectedLearner', 'learnerLessons'));
    }

    public function reports(Request $request)
    {
        $this->checkTeacherAuth();
        $gradeFilter = $request->get('grade');

        // Overall statistics
        $totalLessonsAccessed = LearnerProgress::count();
        $completedLessons = LearnerProgress::where('status', 'completed')->count();
        $inProgressLessons = LearnerProgress::where('status', 'in_progress')->count();

        // By grade level
        $byGrade = User::where('role', 'learner')
            ->select('grade_level')
            ->selectRaw('count(*) as total_learners')
            ->groupBy('grade_level')
            ->get();

        // Most accessed content
        $mostAccessed = LearnerProgress::with(['contentFile', 'subStrand.learningArea'])
            ->select('content_file_id')
            ->selectRaw('count(*) as access_count')
            ->groupBy('content_file_id')
            ->orderBy('access_count', 'desc')
            ->limit(10)
            ->get();

        // Learner completion rates
        $learnerStats = User::where('role', 'learner')
            ->with(['learnerProgress'])
            ->get()
            ->map(function ($user) {
                $total = $user->learnerProgress->count();
                $completed = $user->learnerProgress->where('status', 'completed')->count();
                return [
                    'name' => $user->name,
                    'username' => $user->username,
                    'total_accessed' => $total,
                    'completed' => $completed,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                ];
            })
            ->sortByDesc('total_accessed')
            ->values();

        return view('teacher.reports', compact(
            'totalLessonsAccessed',
            'completedLessons',
            'inProgressLessons',
            'byGrade',
            'mostAccessed',
            'learnerStats'
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
