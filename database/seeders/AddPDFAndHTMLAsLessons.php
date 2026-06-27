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

class AddPDFAndHTMLAsLessons extends Seeder
{
    public function run()
    {
        $this->command->info('=== ADDING PDF & HTML AS LESSONS ===');
        $this->command->info('');

        $basePath = '/home/tele/cbe-platform/storage/app/media';

        // Define grade folders and their mappings
        $grades = [
            'PP1' => ['path' => 'PP1', 'grade' => 'PP1'],
            'PP2' => ['path' => 'PP2', 'grade' => 'PP2'],
            'Grade One' => ['path' => 'Grade One Complete', 'grade' => 'Grade One'],
            'Grade Two' => ['path' => 'Grade Two Complete', 'grade' => 'Grade Two'],
            'Grade Three' => ['path' => 'Grade Three Complete', 'grade' => 'Grade Three'],
            'Grade Four' => ['path' => 'grade-four', 'grade' => 'Grade Four'],
            'Grade Five' => ['path' => 'Grade Five Complete', 'grade' => 'Grade Five'],
            'Grade Six' => ['path' => 'Grade Six Complete', 'grade' => 'Grade Six'],
            'Grade Seven' => ['path' => 'Grade Seven Complete', 'grade' => 'Grade Seven'],
            'Grade Eight' => ['path' => 'Grade Eight Complete', 'grade' => 'Grade Eight'],
            'Grade Nine' => ['path' => 'grade-nine', 'grade' => 'Grade Nine'],
            'Grade Ten' => ['path' => 'grade-ten', 'grade' => 'Grade Ten'],
            'Form Three' => ['path' => 'form-three', 'grade' => 'Form Three'],
            'Form Four' => ['path' => 'form-four', 'grade' => 'Form Four'],
        ];

        foreach ($grades as $name => $config) {
            $fullPath = "$basePath/{$config['path']}";
            if (!is_dir($fullPath)) continue;

            $this->command->info("Processing: {$name}");
            $this->addContentAsLessons($config['grade'], $fullPath);
            $this->command->line("");
        }

        $this->command->info('✅ All PDF & HTML content added as lessons!');
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

    private function addContentAsLessons($gradeLevel, $basePath)
    {
        $subjects = LearningArea::where('grade_level', $gradeLevel)->get();
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        foreach ($subjects as $subject) {
            // Ensure Lessons strand exists
            $lessonsStrand = $subject->strands()->where('name', 'Lessons')->first();
            if (!$lessonsStrand) {
                $lessonsStrand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'LESS',
                    'name' => 'Lessons',
                    'order' => 1,
                ]);
            }

            $contentCount = 0;

            // Link HTML files as lessons
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'html') continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $contentCount++;

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
                    'content_type_id' => $htmlType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);
            }

            // Link PDF files as lessons
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'pdf') continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $contentCount++;

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
                    'content_type_id' => $pdfType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);
            }

            if ($contentCount > 0) {
                $this->command->line("  ✓ {$subject->name}: +{$contentCount} HTML/PDF lessons");
            }
        }
    }
}
