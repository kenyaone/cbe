<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CloudDevice;
use App\Models\CloudLearnerProgress;
use App\Models\CloudDeviceStats;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|size:36',
            'device_name' => 'required|string',
            'region' => 'nullable|string',
            'county' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'data' => 'required|array',
            'data.*.learner_username' => 'required|string',
            'data.*.learner_name' => 'required|string',
            'data.*.subject' => 'required|string',
            'data.*.content_title' => 'required|string',
            'data.*.progress_percentage' => 'required|integer|min:0|max:100',
            'data.*.status' => 'required|string',
            'data.*.last_accessed_at' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
            'data.*.completed_at' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
        ]);

        $deviceId = $validated['device_id'];

        $device = CloudDevice::updateOrCreate(
            ['device_id' => $deviceId],
            [
                'device_name' => $validated['device_name'],
                'region' => $validated['region'],
                'county' => $validated['county'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'last_sync_at' => now(),
                'is_online' => true,
            ]
        );

        $uploadedCount = 0;
        $skippedCount = 0;

        foreach ($validated['data'] as $progress) {
            $exists = CloudLearnerProgress::where('device_id', $deviceId)
                ->where('learner_username', $progress['learner_username'])
                ->where('subject', $progress['subject'])
                ->where('content_title', $progress['content_title'])
                ->first();

            if ($exists && $exists->synced_at > now()->subHours(1)) {
                $skippedCount++;
                continue;
            }

            CloudLearnerProgress::updateOrCreate(
                [
                    'device_id' => $deviceId,
                    'learner_username' => $progress['learner_username'],
                    'subject' => $progress['subject'],
                    'content_title' => $progress['content_title'],
                ],
                [
                    'learner_name' => $progress['learner_name'],
                    'progress_percentage' => $progress['progress_percentage'],
                    'status' => $progress['status'],
                    'last_accessed_at' => $progress['last_accessed_at'],
                    'completed_at' => $progress['completed_at'],
                    'synced_at' => now(),
                ]
            );
            $uploadedCount++;
        }

        $this->updateDeviceStats($device);

        return response()->json([
            'status' => 'ok',
            'device_id' => $deviceId,
            'uploaded' => $uploadedCount,
            'skipped' => $skippedCount,
            'timestamp' => now(),
        ]);
    }

    public function batchUpload(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string|size:36',
            'batches' => 'required|array|max:100',
            'batches.*.learner_username' => 'required|string',
            'batches.*.learner_name' => 'required|string',
            'batches.*.subject' => 'required|string',
            'batches.*.content_title' => 'required|string',
            'batches.*.progress_percentage' => 'required|integer|min:0|max:100',
            'batches.*.status' => 'required|string',
            'batches.*.last_accessed_at' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
            'batches.*.completed_at' => 'nullable|date_format:Y-m-d\TH:i:s\Z',
        ]);

        $deviceId = $validated['device_id'];
        $device = CloudDevice::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not registered'], 404);
        }

        $uploadedCount = 0;

        foreach ($validated['batches'] as $progress) {
            CloudLearnerProgress::updateOrCreate(
                [
                    'device_id' => $deviceId,
                    'learner_username' => $progress['learner_username'],
                    'subject' => $progress['subject'],
                    'content_title' => $progress['content_title'],
                ],
                [
                    'learner_name' => $progress['learner_name'],
                    'progress_percentage' => $progress['progress_percentage'],
                    'status' => $progress['status'],
                    'last_accessed_at' => $progress['last_accessed_at'],
                    'completed_at' => $progress['completed_at'],
                    'synced_at' => now(),
                ]
            );
            $uploadedCount++;
        }

        $device->update(['last_sync_at' => now()]);
        $this->updateDeviceStats($device);

        return response()->json([
            'status' => 'ok',
            'device_id' => $deviceId,
            'uploaded' => $uploadedCount,
            'timestamp' => now(),
        ]);
    }

    private function updateDeviceStats(CloudDevice $device)
    {
        $totalStudents = CloudLearnerProgress::where('device_id', $device->device_id)
            ->selectRaw('COUNT(DISTINCT learner_username) as count')
            ->first()
            ->count;

        $totalLessons = CloudLearnerProgress::where('device_id', $device->device_id)
            ->where('status', 'completed')
            ->count();

        $avgScore = CloudLearnerProgress::where('device_id', $device->device_id)
            ->whereNotNull('progress_percentage')
            ->avg('progress_percentage');

        $device->update([
            'total_students' => $totalStudents,
            'total_lessons' => $totalLessons,
            'avg_score' => round($avgScore ?? 0, 2),
        ]);

        CloudDeviceStats::updateOrCreate(
            [
                'device_id' => $device->device_id,
                'date' => now()->toDateString(),
            ],
            [
                'active_learners' => $totalStudents,
                'lessons_completed' => $totalLessons,
                'avg_score' => $avgScore,
            ]
        );
    }
}
