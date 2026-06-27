<?php

namespace App\Http\Controllers;

use App\Models\CloudDevice;
use App\Models\CloudLearnerProgress;
use App\Models\CloudDeviceStats;
use Illuminate\Http\Request;

class CloudDashboardController extends Controller
{
    public function index()
    {
        $devices = CloudDevice::with('stats')->get();

        $totalDevices = $devices->count();
        $onlineDevices = $devices->where('is_online', true)->count();
        $totalStudents = $devices->sum('total_students');
        $totalLessons = $devices->sum('total_lessons');
        $avgScore = $devices->avg('avg_score');

        $deviceData = $devices->map(function ($device) {
            return [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'name' => $device->device_name,
                'region' => $device->region,
                'county' => $device->county,
                'latitude' => $device->latitude,
                'longitude' => $device->longitude,
                'students' => $device->total_students,
                'lessons' => $device->total_lessons,
                'certs' => $device->total_certs,
                'avg_score' => $device->avg_score,
                'last_sync' => $device->last_sync_at?->diffForHumans(),
                'is_online' => $device->is_online,
            ];
        });

        $regionStats = $this->getRegionStats();

        return view('cloud.dashboard', [
            'totalDevices' => $totalDevices,
            'onlineDevices' => $onlineDevices,
            'totalStudents' => $totalStudents,
            'totalLessons' => $totalLessons,
            'avgScore' => round($avgScore ?? 0, 1),
            'devices' => $deviceData,
            'regionStats' => $regionStats,
        ]);
    }

    public function devices()
    {
        $devices = CloudDevice::orderBy('device_name')->get();

        return view('cloud.devices', [
            'devices' => $devices,
        ]);
    }

    public function deviceDetail($deviceId)
    {
        $device = CloudDevice::where('device_id', $deviceId)->firstOrFail();
        $progress = CloudLearnerProgress::where('device_id', $deviceId)
            ->orderBy('synced_at', 'desc')
            ->get();

        $stats = [
            'total_learners' => $progress->pluck('learner_username')->unique()->count(),
            'completed' => $progress->where('status', 'completed')->count(),
            'in_progress' => $progress->where('status', 'in_progress')->count(),
            'avg_progress' => round($progress->avg('progress_percentage') ?? 0, 1),
        ];

        return view('cloud.device-detail', [
            'device' => $device,
            'progress' => $progress,
            'stats' => $stats,
        ]);
    }

    public function regions()
    {
        $regionStats = $this->getRegionStats();

        return view('cloud.regions', [
            'regions' => $regionStats,
        ]);
    }

    public function reports(Request $request)
    {
        $period = $request->get('period', '7'); // days
        $fromDate = now()->subDays($period)->toDateString();

        $dailyStats = CloudDeviceStats::where('date', '>=', $fromDate)
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $chartData = $dailyStats->map(function ($dayStats) {
            return [
                'date' => $dayStats->first()->date,
                'total_learners' => $dayStats->sum('active_learners'),
                'lessons_completed' => $dayStats->sum('lessons_completed'),
                'avg_score' => round($dayStats->avg('avg_score') ?? 0, 1),
                'certs' => $dayStats->sum('certs_issued'),
            ];
        });

        $deviceStats = CloudDevice::all()->map(function ($device) {
            $stats = $device->stats()
                ->where('date', '>=', now()->subDays(7)->toDateString())
                ->get();

            return [
                'device_name' => $device->device_name,
                'region' => $device->region,
                'total_lessons' => $stats->sum('lessons_completed'),
                'avg_score' => round($stats->avg('avg_score') ?? 0, 1),
                'active_days' => $stats->count(),
            ];
        });

        return view('cloud.reports', [
            'chartData' => $chartData,
            'deviceStats' => $deviceStats,
            'period' => $period,
        ]);
    }

    public function api()
    {
        return response()->json([
            'devices' => CloudDevice::all(),
            'total_students' => CloudDevice::sum('total_students'),
            'total_lessons' => CloudDevice::sum('total_lessons'),
        ]);
    }

    private function getRegionStats()
    {
        $devices = CloudDevice::all();

        return $devices->groupBy('region')
            ->map(function ($regionDevices, $region) {
                return [
                    'region' => $region ?: 'Unassigned',
                    'device_count' => $regionDevices->count(),
                    'online' => $regionDevices->where('is_online', true)->count(),
                    'total_students' => $regionDevices->sum('total_students'),
                    'total_lessons' => $regionDevices->sum('total_lessons'),
                    'avg_score' => round($regionDevices->avg('avg_score') ?? 0, 1),
                ];
            })
            ->sortByDesc('total_students')
            ->values();
    }
}
