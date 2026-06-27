<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LearningArea;
use App\Models\SubStrand;
use App\Models\Strand;
use App\Models\ContentFile;
use App\Models\ContentType;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LinkGradeTwoEnglishVideos extends Seeder
{
    public function run()
    {
        $this->command->info('=== LINKING GRADE TWO ENGLISH VIDEOS ===');

        $basePath = '/home/tele/cbe-platform/storage/app/media/Grade Two Complete/English';

        if (!is_dir($basePath)) {
            $this->command->error("Grade Two English directory not found");
            return;
        }

        // Get English subject
        $englishSubject = LearningArea::where('grade_level', 'Grade Two')
            ->where('name', 'English Language')
            ->first();

        if (!$englishSubject) {
            $this->command->error('English Language subject not found');
            return;
        }

        // Ensure it has Lessons strand (created during simplification)
        $strand = $englishSubject->strands()->where('name', 'Lessons')->first();
        if (!$strand) {
            $strand = Strand::create([
                'learning_area_id' => $englishSubject->id,
                'code' => $englishSubject->code . 'LESS',
                'name' => 'Lessons',
                'order' => 1,
            ]);
            $this->command->line('Created Lessons strand');
        }

        $contentType = ContentType::firstOrCreate(['name' => 'Video']);
        $videoCount = 0;
        $pdfCount = 0;

        // Scan for videos
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            $ext = strtolower($file->getExtension());
            $filePath = $file->getRealPath();
            $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Skip if already exists
            if (ContentFile::where('file_path', $filePath)->exists()) {
                continue;
            }

            // Process videos
            if (in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) {
                $subStrand = SubStrand::create([
                    'strand_id' => $strand->id,
                    'code' => $strand->code . 'V' . str_pad($strand->subStrands()->count() + 1, 2, '0', STR_PAD_LEFT),
                    'name' => $fileName,
                    'order' => $strand->subStrands()->count() + 1,
                ]);

                ContentFile::create([
                    'title' => $fileName,
                    'file_path' => $filePath,
                    'content_type_id' => $contentType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);

                $this->command->line("  ✓ Linked: {$fileName}");
                $videoCount++;
            }
            // Process PDFs
            elseif ($ext === 'pdf') {
                // Create Study Materials strand if needed
                $pdfStrand = $englishSubject->strands()->where('name', 'Study Materials')->first();
                if (!$pdfStrand) {
                    $pdfStrand = Strand::create([
                        'learning_area_id' => $englishSubject->id,
                        'code' => $englishSubject->code . 'PDF',
                        'name' => 'Study Materials',
                        'order' => 2,
                    ]);

                    SubStrand::create([
                        'strand_id' => $pdfStrand->id,
                        'code' => $pdfStrand->code . 'SS01',
                        'name' => 'Reference Documents',
                        'order' => 1,
                    ]);
                }

                $pdfSubStrand = $pdfStrand->subStrands()->first();
                $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

                ContentFile::create([
                    'title' => $fileName,
                    'file_path' => $filePath,
                    'content_type_id' => $pdfType->id,
                    'contentable_id' => $pdfSubStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);

                $this->command->line("  ✓ Linked PDF: {$fileName}");
                $pdfCount++;
            }
        }

        $this->command->info('');
        $this->command->info("✅ Linked {$videoCount} English videos and {$pdfCount} PDFs");
    }
}
