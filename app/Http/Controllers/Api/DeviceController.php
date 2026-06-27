<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceSettings;
use App\Models\LearnerProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function initialize()
    {
        $device = DeviceSettings::first();

        if (!$device) {
            $device = DeviceSettings::create([
                'device_id' => Str::uuid()->toString(),
                'device_name' => gethostname() ?: 'CBE-Device-' . now()->timestamp,
                'last_checkin' => now(),
            ]);
        }

        return response()->json([
            'device_id' => $device->device_id,
            'device_name' => $device->device_name,
            'created_at' => $device->created_at,
        ]);
    }

    public function checkin(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'device_name' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $device = DeviceSettings::where('device_id', $validated['device_id'])->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device->update([
            'device_name' => $validated['device_name'] ?? $device->device_name,
            'latitude' => $validated['latitude'] ?? $device->latitude,
            'longitude' => $validated['longitude'] ?? $device->longitude,
            'last_checkin' => now(),
        ]);

        $pendingSync = LearnerProgress::where('synced', false)
            ->orWhereNull('synced_at')
            ->count();

        return response()->json([
            'status' => 'ok',
            'device' => $device,
            'pending_sync_count' => $pendingSync,
            'timestamp' => now(),
        ]);
    }

    public function status()
    {
        $device = DeviceSettings::first();

        if (!$device) {
            return response()->json(['error' => 'Device not initialized'], 400);
        }

        $totalLearners = \App\Models\User::where('role', 'learner')->count();
        $lessonsAccessed = LearnerProgress::whereNotNull('synced_at')->count();
        $lessonsPending = LearnerProgress::whereNull('synced_at')->count();

        return response()->json([
            'device_id' => $device->device_id,
            'device_name' => $device->device_name,
            'location' => [
                'latitude' => $device->latitude,
                'longitude' => $device->longitude,
            ],
            'last_checkin' => $device->last_checkin,
            'stats' => [
                'total_learners' => $totalLearners,
                'lessons_synced' => $lessonsAccessed,
                'lessons_pending' => $lessonsPending,
                'total_lessons' => $lessonsAccessed + $lessonsPending,
            ],
        ]);
    }

    public function getPendingSync()
    {
        $device = DeviceSettings::first();

        if (!$device) {
            return response()->json(['error' => 'Device not initialized'], 400);
        }

        $pending = LearnerProgress::with(['user', 'subStrand', 'contentFile'])
            ->where(function ($q) {
                $q->where('synced', false)
                  ->orWhereNull('synced_at');
            })
            ->limit(100)
            ->get()
            ->map(function ($progress) {
                return [
                    'id' => $progress->id,
                    'user_id' => $progress->user_id,
                    'user_name' => $progress->user?->name,
                    'content_file_id' => $progress->content_file_id,
                    'progress_percentage' => $progress->progress_percentage,
                    'status' => $progress->status,
                    'last_accessed_at' => $progress->last_accessed_at,
                    'completed_at' => $progress->completed_at,
                ];
            });

        return response()->json([
            'device_id' => $device->device_id,
            'pending_count' => $pending->count(),
            'data' => $pending,
        ]);
    }
}
