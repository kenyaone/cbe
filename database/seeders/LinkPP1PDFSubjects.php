<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class LinkPP1PDFSubjects extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING PP1 PDF SUBJECTS ===');
        $this->command->info('');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP1';
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Map subjects to PDF filename patterns
        $mappings = [
            'Creative Activities' => ['PP1 CREATIVE'],
            'Environmental Activities' => ['ENVIRONMENTAL'],
            'CRE - Christian Religious Education' => ['CRE\.pdf'],
            'HRE - Hindu Religious Education' => ['IRE\.pdf'],
        ];

        foreach ($mappings as $subjectName => $patterns) {
            $subject = LearningArea::where('grade_level', 'PP1')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                $this->command->line("⚠ {$subjectName} not found");
                continue;
            }

            // Create Topics strand for PDFs
            $topicsStrand = Strand::create([
                'learning_area_id' => $subject->id,
                'code' => $subject->code . 'TOPICS',
                'name' => 'Topics',
                'order' => 1,
            ]);

            $this->command->info("{$subject->name}");

            $pdfCount = 0;

            // Scan for matching PDFs
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($basePath),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) continue;

                if (strtolower($file->getExtension()) !== 'pdf') continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                // Check if filename matches any pattern
                $matches = false;
                foreach ($patterns as $pattern) {
                    if (preg_match("/$pattern/i", $fileName)) {
                        $matches = true;
                        break;
                    }
                }

                if (!$matches) continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $pdfCount++;

                $subStrand = SubStrand::create([
                    'strand_id' => $topicsStrand->id,
                    'code' => $topicsStrand->code . 'T' . str_pad($pdfCount, 2, '0', STR_PAD_LEFT),
                    'name' => $fileName,
                    'order' => $pdfCount,
                ]);

                ContentFile::create([
                    'title' => $fileName,
                    'file_path' => $filePath,
                    'content_type_id' => $pdfType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);

                $this->command->line("  ✓ {$fileName}");
            }

            if ($pdfCount == 0) {
                $this->command->line("  (no PDFs found)");
            }
        }

        $this->command->info('');
        $this->command->info('✅ PP1 PDF subjects linked!');
    }
}
