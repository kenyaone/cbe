<?php

namespace App\Jobs;

use App\Models\CloudDevice;
use App\Models\DeviceSettings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ProcessDeviceSyncJob implements ShouldQueue
{
    use Queueable;

    const STALE_SECS = 3600; // 1 hour

    public function handle(): void
    {
        $this->updateDeviceOfflineStatus();
        $this->attemptRemoteSync();
    }

    private function updateDeviceOfflineStatus(): void
    {
        $staleTime = now()->subSeconds(self::STALE_SECS);

        CloudDevice::where('is_online', true)
            ->where('last_sync_at', '<', $staleTime)
            ->update(['is_online' => false]);
    }

    private function attemptRemoteSync(): void
    {
        $localDevice = DeviceSettings::first();

        if (!$localDevice) {
            return;
        }

        // In a real scenario, this would detect internet connectivity
        // and attempt to upload pending sync data to a remote server
        // For now, this is a placeholder for the sync agent logic

        \Log::info('Device sync check', [
            'device_id' => $localDevice->device_id,
            'device_name' => $localDevice->device_name,
            'timestamp' => now(),
        ]);
    }
}
