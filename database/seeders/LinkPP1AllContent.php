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

class LinkPP1AllContent extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING ALL PP1 CONTENT ===');
        $this->command->info('');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP1';

        if (!is_dir($basePath)) {
            $this->command->error("PP1 directory not found");
            return;
        }

        // Link all HTML files as interactives
        $this->linkInteractives('PP1', $basePath);

        // Link all PDFs as study materials
        $this->linkPDFs('PP1', $basePath);

        $this->command->info('');
        $this->command->info('✅ PP1 content linked successfully!');
    }

    private function linkInteractives($gradeName, $basePath)
    {
        $this->command->info('Linking interactive files...');

        $subjects = LearningArea::where('grade_level', $gradeName)->get();
        $contentType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $totalLinked = 0;

        foreach ($subjects as $subject) {
            $strand = $subject->strands()->where('name', 'Lessons')->first();
            if (!$strand) {
                $strand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'LESS',
                    'name' => 'Lessons',
                    'order' => 1,
                ]);
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $subjectCount = 0;
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'html') continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $subjectCount++;
                $totalLinked++;

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

            if ($subjectCount > 0) {
                $this->command->line("  ✓ {$subject->name}: {$subjectCount} interactives");
            }
        }

        $this->command->line("Total interactives linked: {$totalLinked}");
    }

    private function linkPDFs($gradeName, $basePath)
    {
        $this->command->info('');
        $this->command->info('Linking study materials (PDFs)...');

        $subjects = LearningArea::where('grade_level', $gradeName)->get();
        $contentType = ContentType::firstOrCreate(['name' => 'PDF']);
        $totalLinked = 0;

        foreach ($subjects as $subject) {
            // Create or get Study Materials strand
            $pdfStrand = $subject->strands()->where('name', 'Study Materials')->first();
            if (!$pdfStrand) {
                $pdfStrand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'PDF',
                    'name' => 'Study Materials',
                    'order' => 2,
                ]);
            }

            // Create or get Reference Documents substrand
            $pdfSubStrand = $pdfStrand->subStrands()->where('name', 'Reference Documents')->first();
            if (!$pdfSubStrand) {
                $pdfSubStrand = SubStrand::create([
                    'strand_id' => $pdfStrand->id,
                    'code' => $pdfStrand->code . 'SS01',
                    'name' => 'Reference Documents',
                    'order' => 1,
                ]);
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $subjectCount = 0;
            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                $ext = strtolower($file->getExtension());
                if ($ext !== 'pdf') continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $subjectCount++;
                $totalLinked++;

                ContentFile::create([
                    'title' => $fileName,
                    'file_path' => $filePath,
                    'content_type_id' => $contentType->id,
                    'contentable_id' => $pdfSubStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);
            }

            if ($subjectCount > 0) {
                $this->command->line("  ✓ {$subject->name}: {$subjectCount} PDFs");
            }
        }

        $this->command->line("Total PDFs linked: {$totalLinked}");
    }
}
