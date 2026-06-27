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

class LinkPP1ContentAccurate extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING PP1 CONTENT (ACCURATE SUBJECT MAPPING) ===');
        $this->command->info('');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP1';
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Map folder/filename patterns to subjects
        $mappings = [
            'Mathematical Activities' => [
                'folders' => ['Math', 'MATHEMATICS'],
                'filenames' => ['MATHEMATICS'],
            ],
            'Language Activities' => [
                'folders' => ['English', 'ENGLISH LANGUAGE ACTIVITIES'],
                'filenames' => ['LANGUAGES'],
            ],
            'Creative Activities' => [
                'folders' => [],
                'filenames' => ['CREATIVE'],
            ],
            'Environmental Activities' => [
                'folders' => [],
                'filenames' => ['ENVIRONMENTAL'],
            ],
            'CRE - Christian Religious Education' => [
                'folders' => [],
                'filenames' => ['CRE\.pdf'],
            ],
            'HRE - Hindu Religious Education' => [
                'folders' => [],
                'filenames' => ['IRE\.pdf'],
            ],
        ];

        foreach ($mappings as $subjectName => $patterns) {
            $subject = LearningArea::where('grade_level', 'PP1')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                $this->command->line("⚠ Subject not found: {$subjectName}");
                continue;
            }

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

            $htmlCount = 0;
            $pdfCount = 0;

            // Link HTML files
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'html') continue;

                $filePath = $file->getRealPath();
                $relativePath = str_replace($basePath, '', $filePath);

                // Check if file matches subject's folder patterns
                $matches = false;
                foreach ($patterns['folders'] as $folder) {
                    if (stripos($relativePath, $folder) !== false) {
                        $matches = true;
                        break;
                    }
                }

                if (!$matches) continue;
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $htmlCount++;

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

            // Link PDF files
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'pdf') continue;

                $filePath = $file->getRealPath();
                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                // Check if filename matches subject patterns
                $matches = false;
                foreach ($patterns['filenames'] as $pattern) {
                    if (preg_match("/$pattern/i", $fileName)) {
                        $matches = true;
                        break;
                    }
                }

                if (!$matches) continue;
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $pdfCount++;

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

            if ($htmlCount > 0 || $pdfCount > 0) {
                $this->command->line("  ✓ {$subject->name}: +{$htmlCount} HTML, +{$pdfCount} PDFs");
            }
        }

        $this->command->info('');
        $this->command->info('✅ PP1 content linked with accurate subject mapping!');
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
