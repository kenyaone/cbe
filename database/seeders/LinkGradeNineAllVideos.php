<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LinkGradeNineAllVideos extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING ALL GRADE NINE VIDEOS ===');

        $this->linkSubjectVideos('Mathematics', '/home/tele/cbe-platform/storage/app/media/grade-nine/Math');
        $this->linkSubjectVideos('Integrated Science', '/home/tele/cbe-platform/storage/app/media/grade-nine/INTEGRATED SCIENCE');

        $this->command->info('');
        $this->command->info('✅ All Grade Nine videos linked!');
    }

    private function linkSubjectVideos($subjectName, $basePath)
    {
        if (!is_dir($basePath)) {
            $this->command->error("{$subjectName} directory not found");
            return;
        }

        $subject = LearningArea::where('grade_level', 'Grade Nine')
            ->where('name', $subjectName)
            ->first();

        if (!$subject) {
            $this->command->error("{$subjectName} subject not found");
            return;
        }

        $strand = $subject->strands()->where('name', 'Lessons')->first();
        if (!$strand) {
            $this->command->error("Lessons strand not found for {$subjectName}");
            return;
        }

        $contentType = ContentType::firstOrCreate(['name' => 'Video']);
        $videoCount = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) continue;

            $filePath = $file->getRealPath();
            $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $videoCount++;

            $subStrand = SubStrand::create([
                'strand_id' => $strand->id,
                'code' => $strand->code . 'V' . str_pad($videoCount, 2, '0', STR_PAD_LEFT),
                'name' => $fileName,
                'order' => $videoCount,
            ]);

            ContentFile::create([
                'title' => $fileName,
                'file_path' => $filePath,
                'content_type_id' => $contentType->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
                'is_published' => true,
            ]);
        }

        $this->command->line("  ✓ Linked {$videoCount} {$subjectName} videos");
    }
}
