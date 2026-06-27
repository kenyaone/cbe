<?php

namespace App\Http\Controllers;

use App\Models\Learner;
use App\Models\ContentFile;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use Carbon\Carbon;

class AdminReportsController extends Controller
{
    public function index()
    {
        $learner_activity = $this->getLearnerActivityReport();
        $content_stats = $this->getContentStatsReport();
        $platform_stats = $this->getPlatformStatsReport();

        return view('admin.reports.index', compact('learner_activity', 'content_stats', 'platform_stats'));
    }

    public function learnerActivity()
    {
        $learners = Learner::select('id', 'name', 'username', 'grade_level', 'created_at', 'last_login_at')
            ->orderBy('last_login_at', 'desc')
            ->get();

        $stats = [
            'total' => Learner::count(),
            'active_today' => Learner::whereDate('last_login_at', today())->count(),
            'active_this_week' => Learner::where('last_login_at', '>=', now()->subWeek())->count(),
            'new_this_week' => Learner::where('created_at', '>=', now()->subWeek())->count(),
            'by_grade' => Learner::selectRaw('grade_level, count(*) as count')
                ->groupBy('grade_level')
                ->get(),
        ];

        return view('admin.reports.learner-activity', compact('learners', 'stats'));
    }

    public function contentStats()
    {
        $stats = [
            'total_files' => ContentFile::count(),
            'by_type' => ContentFile::join('content_types', 'content_files.content_type_id', '=', 'content_types.id')
                ->selectRaw('content_types.name, count(*) as count')
                ->groupBy('content_types.name')
                ->get(),
            'by_grade' => ContentFile::join('sub_strands', 'content_files.contentable_id', '=', 'sub_strands.id')
                ->join('strands', 'sub_strands.strand_id', '=', 'strands.id')
                ->join('learning_areas', 'strands.learning_area_id', '=', 'learning_areas.id')
                ->selectRaw('learning_areas.grade_level, count(*) as count')
                ->where('content_files.contentable_type', 'App\\Models\\SubStrand')
                ->groupBy('learning_areas.grade_level')
                ->get(),
            'by_subject' => ContentFile::join('sub_strands', 'content_files.contentable_id', '=', 'sub_strands.id')
                ->join('strands', 'sub_strands.strand_id', '=', 'strands.id')
                ->join('learning_areas', 'strands.learning_area_id', '=', 'learning_areas.id')
                ->selectRaw('learning_areas.name, count(*) as count')
                ->where('content_files.contentable_type', 'App\\Models\\SubStrand')
                ->groupBy('learning_areas.name')
                ->orderBy('count', 'desc')
                ->get(),
        ];

        return view('admin.reports.content-stats', $stats);
    }

    public function platformStats()
    {
        $stats = [
            'grades' => LearningArea::select('grade_level')->distinct()->count(),
            'subjects' => LearningArea::count(),
            'strands' => \App\Models\Strand::count(),
            'sub_strands' => \App\Models\SubStrand::count(),
        ];

        $subjects_by_grade = LearningArea::selectRaw('grade_level, count(*) as count')
            ->groupBy('grade_level')
            ->orderByRaw("CASE
                WHEN grade_level = 'PP1' THEN 1
                WHEN grade_level = 'PP2' THEN 2
                WHEN grade_level LIKE 'Grade%' THEN CAST(SUBSTR(grade_level, 7) AS INTEGER) + 2
                ELSE 100
            END")
            ->get();

        $registrations = Learner::selectRaw('DATE(created_at) as date, count(*) as registrations')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('registrations', 'date');

        $logins = Learner::selectRaw('DATE(last_login_at) as date, count(*) as active')
            ->whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('active', 'date');

        $allDates = array_unique(array_merge($registrations->keys()->toArray(), $logins->keys()->toArray()));
        sort($allDates);

        $trend_data = collect();
        foreach ($allDates as $date) {
            $trend_data->push((object)[
                'date' => $date,
                'registrations' => $registrations->get($date, 0),
                'active' => $logins->get($date, 0),
            ]);
        }

        return view('admin.reports.platform-stats', compact('stats', 'subjects_by_grade', 'trend_data'));
    }

    private function getLearnerActivityReport()
    {
        return [
            'total' => Learner::count(),
            'active_today' => Learner::whereDate('last_login_at', today())->count(),
            'active_this_week' => Learner::where('last_login_at', '>=', now()->subWeek())->count(),
        ];
    }

    private function getContentStatsReport()
    {
        return [
            'total_files' => ContentFile::count(),
            'videos' => ContentFile::whereHas('contentType', fn($q) => $q->where('name', 'Video'))->count(),
            'pdfs' => ContentFile::whereHas('contentType', fn($q) => $q->where('name', 'PDF'))->count(),
            'interactives' => ContentFile::whereHas('contentType', fn($q) => $q->where('name', 'Interactive'))->count(),
        ];
    }

    private function getPlatformStatsReport()
    {
        return [
            'grades' => LearningArea::select('grade_level')->distinct()->count(),
            'subjects' => LearningArea::count(),
            'strands' => Strand::count(),
        ];
    }
}
