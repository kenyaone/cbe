<?php

namespace App\Http\Controllers;

use App\Models\CloudDevice;

class PublicDeviceMapController extends Controller
{
    public function map()
    {
        $devices = CloudDevice::all();
        $stats = [
            'total_devices' => $devices->count(),
            'online_devices' => $devices->where('is_online', true)->count(),
            'total_students' => $devices->sum('total_students'),
            'total_lessons' => $devices->sum('total_lessons'),
            'avg_score' => round($devices->avg('avg_score') ?? 0, 1),
        ];

        return view('public.device-map', [
            'devices' => $devices,
            'stats' => $stats,
        ]);
    }

    public function api()
    {
        $devices = CloudDevice::all()->map(function ($device) {
            return [
                'id' => $device->id,
                'device_id' => $device->device_id,
                'name' => $device->device_name,
                'region' => $device->region,
                'county' => $device->county,
                'latitude' => (float)$device->latitude,
                'longitude' => (float)$device->longitude,
                'students' => $device->total_students,
                'lessons' => $device->total_lessons,
                'certs' => $device->total_certs,
                'avg_score' => (float)($device->avg_score ?? 0),
                'is_online' => $device->is_online,
                'last_sync' => $device->last_sync_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'devices' => $devices,
            'timestamp' => now()->toIso8601String(),
            'total_devices' => $devices->count(),
            'online_count' => $devices->where('is_online', true)->count(),
        ]);
    }

    public function embed()
    {
        return view('public.device-map-embed');
    }

    public function status()
    {
        $devices = CloudDevice::all();
        $regions = $devices->groupBy('region')->map(function ($regionDevices, $region) {
            return [
                'region' => $region ?: 'Unassigned',
                'device_count' => $regionDevices->count(),
                'online' => $regionDevices->where('is_online', true)->count(),
                'students' => $regionDevices->sum('total_students'),
                'lessons' => $regionDevices->sum('total_lessons'),
                'avg_score' => round($regionDevices->avg('avg_score') ?? 0, 1),
            ];
        });

        return view('public.device-status', [
            'regions' => $regions,
            'last_updated' => now(),
        ]);
    }
}
