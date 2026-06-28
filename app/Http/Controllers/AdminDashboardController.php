<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Learner;
use App\Models\ContentFile;
use App\Models\LearningArea;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_admins' => User::where('role', 'admin')->count(),
            'total_learners' => User::where('role', 'learner')->count(),
            'total_content_files' => ContentFile::count(),
            'total_grades' => LearningArea::select('grade_level')->distinct()->count(),
            'total_subjects' => LearningArea::count(),
            'content_by_type' => ContentFile::join('content_types', 'content_files.content_type_id', '=', 'content_types.id')
                ->groupBy('content_types.name')
                ->selectRaw('content_types.name, count(*) as count')
                ->pluck('count', 'name')
                ->toArray(),
            'recent_learners' => User::where('role', 'learner')->orderBy('created_at', 'desc')->limit(5)->get(),
        ];

        return view('admin.dashboard', $stats);
    }

    public function users()
    {
        $admins = User::where('role', 'admin')->paginate(10);
        return view('admin.users.index', compact('admins'));
    }

    public function learners()
    {
        $learners = User::where('role', 'learner')->paginate(15);
        return view('admin.learners.index', compact('learners'));
    }

    public function content()
    {
        $content = ContentFile::with('contentType')->paginate(20);
        return view('admin.content.index', compact('content'));
    }

    public function curriculum()
    {
        $grades = LearningArea::select('grade_level')->distinct()
            ->orderByRaw("CASE
                WHEN grade_level = 'PP1' THEN 1
                WHEN grade_level = 'PP2' THEN 2
                WHEN grade_level LIKE 'Grade%' THEN CAST(SUBSTR(grade_level, 7) AS INTEGER) + 2
                ELSE 100
            END")
            ->pluck('grade_level');

        return view('admin.curriculum.index', compact('grades'));
    }
}
