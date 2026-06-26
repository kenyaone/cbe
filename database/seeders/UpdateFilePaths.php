<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateFilePaths extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map USB paths to local storage paths
        $pathMappings = [
            '/media/tele/ARISE1/PP1' => '/home/tele/cbe-platform/storage/app/media/PP1',
            '/media/tele/ARISE1/PP2' => '/home/tele/cbe-platform/storage/app/media/PP2',
            '/media/tele/ARISE1/Grade One Complete' => '/home/tele/cbe-platform/storage/app/media/Grade One Complete',
            '/media/tele/ARISE1/Grade Two Complete' => '/home/tele/cbe-platform/storage/app/media/Grade Two Complete',
            '/media/tele/ARISE1/Grade Three Complete' => '/home/tele/cbe-platform/storage/app/media/Grade Three Complete',
            '/media/tele/ARISE1/Grade Four Complete' => '/home/tele/cbe-platform/storage/app/media/Grade Four Complete',
            '/media/tele/ARISE1/Grade Five Complete' => '/home/tele/cbe-platform/storage/app/media/Grade Five Complete',
            '/media/tele/ARISE1/Grade Six Complete' => '/home/tele/cbe-platform/storage/app/media/Grade Six Complete',
        ];

        $totalUpdated = 0;

        foreach ($pathMappings as $oldPath => $newPath) {
            $updated = \DB::table('content_files')
                ->where('file_path', 'LIKE', $oldPath . '%')
                ->update([
                    'file_path' => \DB::raw("REPLACE(file_path, '$oldPath', '$newPath')")
                ]);

            if ($updated > 0) {
                $this->command->info("Updated $updated files from $oldPath");
                $totalUpdated += $updated;
            }
        }

        $this->command->info("\n✓ Total files updated: $totalUpdated");
        $this->command->info("✓ Files now stored locally in /home/tele/cbe-platform/storage/app/media/");
        $this->command->info("✓ USB removal will no longer affect content availability!");
    }
}
