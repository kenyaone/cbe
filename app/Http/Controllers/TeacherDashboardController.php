<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LearnerProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

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
