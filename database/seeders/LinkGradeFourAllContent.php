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

class LinkGradeFourAllContent extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING ALL GRADE FOUR CONTENT ===');
        $this->command->info('');

        $basePath = '/home/tele/cbe-platform/storage/app/media/grade-four';

        if (!is_dir($basePath)) {
            $this->command->error("Grade Four directory not found");
            return;
        }

        // Link English videos
        $this->linkVideos('English Language', $basePath . '/Grade Four English');

        // Link Math videos
        $this->linkVideos('Mathematics', $basePath . '/Grade Four Math');

        // Link Math interactive files
        $this->linkInteractives('Mathematics', $basePath . '/Grade Four Math/Interactives');

        // Link Science HTML files
        $this->linkInteractives('Science and Technology', $basePath . '/Science and Technology');

        // Link root-level interactive files
        $this->linkRootLevelFiles($basePath);

        $this->command->info('');
        $this->command->info('✅ Grade Four content linked successfully!');
    }

    private function linkVideos($subjectName, $contentPath)
    {
        if (!is_dir($contentPath)) {
            return;
        }

        $subject = LearningArea::where('grade_level', 'Grade Four')
            ->where('name', $subjectName)
            ->first();

        if (!$subject) {
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

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($contentPath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            $ext = strtolower($file->getExtension());
            if (!in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) continue;

            $filePath = $file->getRealPath();
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

            $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $videoCount++;

            $subStrand = SubStrand::create([
                'strand_id' => $strand->id,
                'code' => $strand->code . 'V' . str_pad($videoCount, 3, '0', STR_PAD_LEFT),
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

        if ($videoCount > 0) {
            $this->command->line("  ✓ Linked {$videoCount} {$subjectName} videos");
        }
    }

    private function linkInteractives($subjectName, $contentPath)
    {
        if (!is_dir($contentPath)) {
            return;
        }

        $subject = LearningArea::where('grade_level', 'Grade Four')
            ->where('name', $subjectName)
            ->first();

        if (!$subject) {
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

        $contentType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $interactiveCount = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($contentPath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            $ext = strtolower($file->getExtension());
            if ($ext !== 'html') continue;

            $filePath = $file->getRealPath();
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

            $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
            $interactiveCount++;

            $existingCount = $strand->subStrands()->count();
            $subStrand = SubStrand::create([
                'strand_id' => $strand->id,
                'code' => $strand->code . 'I' . str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT),
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
        }

        if ($interactiveCount > 0) {
            $this->command->line("  ✓ Linked {$interactiveCount} {$subjectName} interactive files");
        }
    }

    private function linkRootLevelFiles($basePath)
    {
        $this->command->line("");
        $this->command->info('Linking root-level files...');

        $contentType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $htmlCount = 0;

        $files = scandir($basePath);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || is_dir("$basePath/$file")) continue;

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($ext !== 'html') continue;

            $filePath = "$basePath/$file";
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

            $fileName = pathinfo($file, PATHINFO_FILENAME);
            $subject = LearningArea::where('grade_level', 'Grade Four')
                ->where('name', 'Science and Technology')
                ->first();

            if (!$subject) continue;

            $strand = $subject->strands()->where('name', 'Lessons')->first();
            if (!$strand) continue;

            $existingCount = $strand->subStrands()->count();
            $subStrand = SubStrand::create([
                'strand_id' => $strand->id,
                'code' => $strand->code . 'I' . str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT),
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

            $htmlCount++;
        }

        if ($htmlCount > 0) {
            $this->command->line("  ✓ Linked {$htmlCount} root-level interactive files");
        }
    }
}
