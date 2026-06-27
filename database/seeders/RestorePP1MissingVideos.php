<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RestorePP1MissingVideos extends Seeder
{
    public function run()
    {
        $this->command->info('=== RESTORING MISSING PP1 VIDEOS ===');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP1';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);

        // Map folders to subjects
        $mappings = [
            'Mathematical Activities' => 'Math',
            'Language Activities' => 'English',
        ];

        foreach ($mappings as $subjectName => $folder) {
            $subject = LearningArea::where('grade_level', 'PP1')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) continue;

            $lessonsStrand = $subject->strands()->where('name', 'Lessons')->first();
            if (!$lessonsStrand) continue;

            $folderPath = "$basePath/$folder";
            if (!is_dir($folderPath)) continue;

            $this->command->info("Scanning {$subjectName} ({$folder})...");

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($folderPath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $count = 0;
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if (!in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $count++;

                $existingCount = $lessonsStrand->subStrands()->count();
                $code = $this->generateUniqueCode($lessonsStrand->id, $lessonsStrand->code . 'L', $existingCount + 1);

                $subStrand = SubStrand::create([
                    'strand_id' => $lessonsStrand->id,
                    'code' => $code,
                    'name' => $fileName,
                    'order' => $existingCount + 1,
                ]);

                ContentFile::create([
                    'title' => $fileName,
                    'file_path' => $filePath,
                    'content_type_id' => $videoType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);
            }

            if ($count > 0) {
                $this->command->line("  ✓ Added {$count} missing videos");
            }
        }

        $this->command->info('');
        $this->command->info('✅ PP1 videos restored!');
    }

    private function generateUniqueCode($strandId, $prefix, $number)
    {
        $code = $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
        $counter = 1;

        while (SubStrand::where('strand_id', $strandId)->where('code', $code)->exists()) {
            $code = $prefix . str_pad($number + $counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }
}
