<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class LinkGradeFourRootVideos extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING GRADE FOUR ROOT VIDEOS ===');

        $basePath = '/home/tele/cbe-platform/storage/app/media/grade-four';
        $subject = LearningArea::where('grade_level', 'Grade Four')
            ->where('name', 'Science and Technology')
            ->first();

        if (!$subject) {
            $this->command->error('Science and Technology subject not found');
            return;
        }

        $strand = $subject->strands()->where('name', 'Lessons')->first();
        if (!$strand) {
            $strand = Strand::create([
                'learning_area_id' => $subject->id,
                'code' => $subject->code . 'LESS',
                'name' => 'Lessons',
                'order' => 1,
            ]);
        }

        $contentType = ContentType::firstOrCreate(['name' => 'Video']);
        $videoCount = 0;

        // Scan root directory for videos
        $files = scandir($basePath);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || is_dir("$basePath/$file")) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) continue;

            $filePath = "$basePath/$file";
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

            $fileName = pathinfo($file, PATHINFO_FILENAME);
            $videoCount++;

            $existingCount = $strand->subStrands()->count();
            $subStrand = SubStrand::create([
                'strand_id' => $strand->id,
                'code' => $strand->code . 'V' . str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT),
                'name' => $fileName,
                'order' => $existingCount + 1,
            ]);

            ContentFile::create([
                'title' => $fileName,
                'file_path' => $filePath,
                'content_type_id' => $contentType->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
                'is_published' => true,
            ]);

            $this->command->line("  ✓ Linked: {$fileName}");
        }

        $this->command->info('');
        if ($videoCount > 0) {
            $this->command->info("✅ Linked {$videoCount} root-level video(s)");
        } else {
            $this->command->info("✅ No root-level videos to link");
        }
    }
}
