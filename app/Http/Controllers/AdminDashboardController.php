<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Learner;
use App\Models\ContentFile;
use App\Models\LearningArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    public function createTeacher()
    {
        return view('admin.users.create-teacher');
    }

    public function storeTeacher(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => 'teacher_' . time() . '@cbe.local',
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
        ]);

        return redirect()->route('admin.teachers')->with('success', 'Teacher account created successfully');
    }

    public function resetPassword(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $newPassword = str_random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        return response()->json([
            'success' => true,
            'message' => "Password reset successfully for {$user->name}",
            'temp_password' => $newPassword,
            'instructions' => "Share this temporary password with the user: $newPassword"
        ]);
    }

    public function resetPasswordForm($userId)
    {
        $user = User::findOrFail($userId);
        return view('admin.users.reset-password', compact('user'));
    }

    public function confirmReset(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $tempPassword = str_random(10);
        $user->update(['password' => Hash::make($tempPassword)]);

        return redirect()->back()->with('reset_password', [
            'user_name' => $user->name,
            'temp_password' => $tempPassword,
            'role' => $user->role,
        ]);
    }

    public function teachers()
    {
        $teachers = User::where('role', 'teacher')->paginate(15);
        return view('admin.users.teachers', compact('teachers'));
    }

    public function admins()
    {
        $admins = User::where('role', 'admin')->paginate(15);
        return view('admin.users.admins', compact('admins'));
    }
}
