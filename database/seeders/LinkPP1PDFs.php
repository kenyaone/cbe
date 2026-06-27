<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class LinkPP1PDFs extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING PP1 PDFS ===');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP1';
        $contentType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Map filenames to subjects
        $pdfMappings = [
            'MATHEMATICS' => 'Mathematical Activities',
            'LANGUAGES' => 'Language Activities',
            'ENVIRONMENTAL' => 'Environmental Activities',
            'CRE' => 'CRE - Christian Religious Education',
            'IRE' => 'HRE - Hindu Religious Education',
            'CREATIVE' => 'Creative Activities',
        ];

        $files = scandir($basePath);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with(strtolower($file), '.pdf')) continue;

            $filePath = "$basePath/$file";
            if (ContentFile::where('file_path', $filePath)->exists()) continue;

            $fileName = pathinfo($file, PATHINFO_FILENAME);

            // Find matching subject
            $subject = null;
            foreach ($pdfMappings as $pattern => $subjectName) {
                if (stripos($fileName, $pattern) !== false) {
                    $subject = LearningArea::where('grade_level', 'PP1')
                        ->where('name', $subjectName)
                        ->first();
                    break;
                }
            }

            if (!$subject) {
                $this->command->line("  ⚠ {$fileName}: No matching subject");
                continue;
            }

            // Get or create Study Materials strand
            $pdfStrand = $subject->strands()->where('name', 'Study Materials')->first();
            if (!$pdfStrand) {
                $pdfStrand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'PDF',
                    'name' => 'Study Materials',
                    'order' => 2,
                ]);
            }

            // Get or create Reference Documents substrand
            $pdfSubStrand = $pdfStrand->subStrands()->where('name', 'Reference Documents')->first();
            if (!$pdfSubStrand) {
                $pdfSubStrand = SubStrand::create([
                    'strand_id' => $pdfStrand->id,
                    'code' => $pdfStrand->code . 'SS01',
                    'name' => 'Reference Documents',
                    'order' => 1,
                ]);
            }

            ContentFile::create([
                'title' => $fileName,
                'file_path' => $filePath,
                'content_type_id' => $contentType->id,
                'contentable_id' => $pdfSubStrand->id,
                'contentable_type' => SubStrand::class,
                'is_published' => true,
            ]);

            $this->command->line("  ✓ {$fileName} → {$subject->name}");
        }

        $this->command->info('');
        $this->command->info('✅ PP1 PDFs linked successfully!');
    }
}
