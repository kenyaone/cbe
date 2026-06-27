<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RebuildGradeNineSimplified extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING GRADE NINE (SIMPLIFIED) ===');
        $this->command->info('');

        $curriculumType = CurriculumType::firstOrCreate(['name' => 'CBE']);

        // Grade Nine subjects with proper structure
        $subjects = [
            'Mathematics' => 'G9MAT',
            'Integrated Science' => 'G9SCI',
            'English' => 'G9ENG',
            'Kiswahili' => 'G9KIS',
            'Social Studies' => 'G9SOC',
            'Agriculture' => 'G9AGR',
            'CRE - Christian Religious Education' => 'G9CRE',
            'IRE - Hindu/Indian Religious Education' => 'G9IRE',
            'Creative Arts and Sports' => 'G9CAS',
            'Pre-Technical Studies' => 'G9PTS',
        ];

        // Step 1: Create subjects with simplified structure
        $this->command->info('Step 1: Creating subjects with Lessons structure');
        $subjectModels = [];
        $order = 0;

        foreach ($subjects as $name => $code) {
            $order++;
            $subject = LearningArea::create([
                'curriculum_type_id' => $curriculumType->id,
                'grade_level' => 'Grade Nine',
                'name' => $name,
                'code' => $code,
                'order' => $order,
            ]);

            // Create Lessons strand immediately
            Strand::create([
                'learning_area_id' => $subject->id,
                'code' => $code . 'LESS',
                'name' => 'Lessons',
                'order' => 1,
            ]);

            $subjectModels[$name] = $subject;
            $this->command->line("  ✓ Created {$name}");
        }

        $this->command->info('');
        $this->command->info('Step 2: Linking Math videos');
        $this->linkMathVideos($subjectModels['Mathematics']);

        $this->command->info('');
        $this->command->info('Step 3: Linking Science videos');
        $this->linkScienceVideos($subjectModels['Integrated Science']);

        $this->command->info('');
        $this->command->info('✅ Grade Nine rebuilt successfully!');
    }

    private function linkMathVideos($mathSubject)
    {
        $basePath = '/home/tele/cbe-platform/storage/app/media/grade-nine/Math';

        if (!is_dir($basePath)) {
            $this->command->error("Math directory not found");
            return;
        }

        $strand = $mathSubject->strands()->where('name', 'Lessons')->first();
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
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

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

        $this->command->line("  ✓ Linked {$videoCount} Math videos");
    }

    private function linkScienceVideos($scienceSubject)
    {
        $basePath = '/home/tele/cbe-platform/storage/app/media/grade-nine/INTEGRATED SCIENCE';

        if (!is_dir($basePath)) {
            $this->command->error("Science directory not found");
            return;
        }

        $strand = $scienceSubject->strands()->where('name', 'Lessons')->first();
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
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

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

        $this->command->line("  ✓ Linked {$videoCount} Science videos");
    }
}
