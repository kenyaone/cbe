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

class LinkAllRemainingContent extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING REMAINING GRADES CONTENT ===');
        $this->command->info('');

        $basePath = '/home/tele/cbe-platform/storage/app/media';
        $grades = ['Grade One Complete', 'Grade Two Complete', 'Grade Three Complete', 'grade-four',
                   'Grade Five Complete', 'Grade Six Complete', 'Grade Seven Complete', 'Grade Eight Complete',
                   'grade-nine', 'grade-ten', 'form-three', 'form-four'];

        foreach ($grades as $gradeFolder) {
            $fullPath = "$basePath/$gradeFolder";
            if (!is_dir($fullPath)) continue;

            // Determine grade level from folder name
            $gradeLevel = $this->getGradeLevel($gradeFolder);
            if (!$gradeLevel) continue;

            $this->command->info("Processing: {$gradeLevel}");
            $this->linkContentForGrade($gradeLevel, $fullPath);
            $this->command->line("");
        }

        $this->command->info('✅ All remaining content linked!');
    }

    private function getGradeLevel($folderName)
    {
        $mappings = [
            'Grade One Complete' => 'Grade One',
            'Grade Two Complete' => 'Grade Two',
            'Grade Three Complete' => 'Grade Three',
            'grade-four' => 'Grade Four',
            'Grade Five Complete' => 'Grade Five',
            'Grade Six Complete' => 'Grade Six',
            'Grade Seven Complete' => 'Grade Seven',
            'Grade Eight Complete' => 'Grade Eight',
            'grade-nine' => 'Grade Nine',
            'grade-ten' => 'Grade Ten',
            'form-three' => 'Form Three',
            'form-four' => 'Form Four',
        ];

        return $mappings[$folderName] ?? null;
    }

    private function linkContentForGrade($gradeLevel, $basePath)
    {
        $subjects = LearningArea::where('grade_level', $gradeLevel)->get();
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        $totalHTML = 0;
        $totalPDFs = 0;

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

            // Link HTML files as interactives
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $htmlCount = 0;
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'html') continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $htmlCount++;
                $totalHTML++;

                $existingCount = $lessonsStrand->subStrands()->count();
                $subStrand = SubStrand::create([
                    'strand_id' => $lessonsStrand->id,
                    'code' => $lessonsStrand->code . 'I' . str_pad($existingCount + 1, 3, '0', STR_PAD_LEFT),
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

            if ($htmlCount > 0) {
                $this->command->line("  ✓ {$subject->name}: {$htmlCount} interactives");
            }

            // Link PDFs as study materials
            $pdfStrand = $subject->strands()->where('name', 'Study Materials')->first();
            if (!$pdfStrand) {
                $pdfStrand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'PDF',
                    'name' => 'Study Materials',
                    'order' => 2,
                ]);
            }

            $pdfSubStrand = $pdfStrand->subStrands()->where('name', 'Reference Documents')->first();
            if (!$pdfSubStrand) {
                $pdfSubStrand = SubStrand::create([
                    'strand_id' => $pdfStrand->id,
                    'code' => $pdfStrand->code . 'SS01',
                    'name' => 'Reference Documents',
                    'order' => 1,
                ]);
            }

            // Scan for PDFs
            $pdfCount = 0;
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
                $pdfCount++;
                $totalPDFs++;

                ContentFile::create([
                    'title' => $fileName,
                    'file_path' => $filePath,
                    'content_type_id' => $pdfType->id,
                    'contentable_id' => $pdfSubStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);
            }

            if ($pdfCount > 0) {
                $this->command->line("    └ {$subject->name}: {$pdfCount} PDFs");
            }
        }

        $this->command->line("  Summary: {$totalHTML} interactives, {$totalPDFs} PDFs");
    }
}
