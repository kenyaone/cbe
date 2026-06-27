<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class LinkPP2AllContent extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING PP2 CONTENT ===');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP2';

        if (!is_dir($basePath)) {
            $this->command->error("PP2 directory not found");
            return;
        }

        $this->linkHTMLAndPDFs('PP2', $basePath);

        $this->command->info('');
        $this->command->info('✅ PP2 content linked successfully!');
    }

    private function linkHTMLAndPDFs($gradeName, $basePath)
    {
        $subjects = LearningArea::where('grade_level', $gradeName)->get();
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

            // Link HTML files as interactives
            $files = scandir($basePath);
            $htmlCount = 0;
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || !str_ends_with(strtolower($file), '.html')) continue;

                $filePath = "$basePath/$file";
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $htmlCount++;

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

            $pdfCount = 0;
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || !str_ends_with(strtolower($file), '.pdf')) continue;

                $filePath = "$basePath/$file";
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $pdfCount++;

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
                $this->command->line("    └ {$pdfCount} PDFs linked");
            }
        }
    }
}
