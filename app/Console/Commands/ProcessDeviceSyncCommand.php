<?php

namespace App\Console\Commands;

use App\Models\CloudDevice;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('device:sync')]
#[Description('Process device sync: update offline status, attempt syncs')]
class ProcessDeviceSyncCommand extends Command
{
    public function handle()
    {
        $this->updateOfflineStatus();
        $this->checkDeviceHealth();
    }

    private function updateOfflineStatus(): void
    {
        $staleTime = now()->subHours(1);

        $updated = CloudDevice::where('is_online', true)
            ->where('last_sync_at', '<', $staleTime)
            ->update(['is_online' => false]);

        if ($updated > 0) {
            $this->info("✓ Marked {$updated} devices as offline");
        }
    }

    private function checkDeviceHealth(): void
    {
        $online = CloudDevice::where('is_online', true)->count();
        $offline = CloudDevice::where('is_online', false)->count();
        $total = CloudDevice::count();

        $this->table(
            ['Status', 'Count', 'Percentage'],
            [
                ['🟢 Online', $online, $total > 0 ? round(($online / $total) * 100, 1) . '%' : '0%'],
                ['⚫ Offline', $offline, $total > 0 ? round(($offline / $total) * 100, 1) . '%' : '0%'],
                ['📊 Total', $total, '100%'],
            ]
        );
    }
}
