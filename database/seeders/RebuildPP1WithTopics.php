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

class RebuildPP1WithTopics extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING PP1 WITH TOPIC STRUCTURE ===');
        $this->command->info('Topics (PDFs) + Lessons (HTMLs + Videos)\n');

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP1';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Subject configuration
        $subjectConfigs = [
            'Mathematical Activities' => [
                'html_folder' => 'Math/Interactives',
                'pdf_pattern' => 'MATHEMATICS',
            ],
            'Language Activities' => [
                'html_folder' => 'English/Interactives',
                'pdf_pattern' => 'LANGUAGES',
            ],
            'Creative Activities' => [
                'html_folder' => null,
                'pdf_patterns' => ['CREATIVE ACITIVITIES', 'CREATIVE'],
            ],
            'Environmental Activities' => [
                'html_folder' => null,
                'pdf_patterns' => ['ENVIRONMENTAL'],
            ],
            'CRE - Christian Religious Education' => [
                'html_folder' => null,
                'pdf_patterns' => ['CRE'],
            ],
            'HRE - Hindu Religious Education' => [
                'html_folder' => null,
                'pdf_patterns' => ['IRE'],
            ],
        ];

        foreach ($subjectConfigs as $subjectName => $config) {
            $subject = LearningArea::where('grade_level', 'PP1')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                $this->command->line("⚠ Subject not found: {$subjectName}");
                continue;
            }

            $this->command->info("Processing: {$subject->name}");

            // 1. LESSONS STRAND (Videos + HTML)
            $lessonsStrand = $subject->strands()->where('name', 'Lessons')->first();
            if (!$lessonsStrand) {
                $lessonsStrand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $subject->code . 'LESS',
                    'name' => 'Lessons',
                    'order' => 1,
                ]);
            }

            $lessonCount = 0;

            // Link HTML files as lessons (if folder specified)
            if ($config['html_folder']) {
                $htmlPath = "$basePath/{$config['html_folder']}";
                if (is_dir($htmlPath)) {
                    $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($htmlPath),
                        RecursiveIteratorIterator::SELF_FIRST
                    );

                    foreach ($iterator as $file) {
                        if ($file->isDir()) continue;

                        if (strtolower($file->getExtension()) !== 'html') continue;

                        $filePath = $file->getRealPath();
                        if (ContentFile::where('file_path', $filePath)->exists()) continue;

                        $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $lessonCount++;

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
                }
            }

            // Link Videos as lessons (if they exist)
            $videoPath = "$basePath/{$config['html_folder']}";
            if ($videoPath && is_dir(dirname($videoPath))) {
                $videoIterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator(dirname($videoPath)),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                $videoCount = 0;
                foreach ($videoIterator as $file) {
                    if ($file->isDir()) continue;

                    $ext = strtolower($file->getExtension());
                    if (!in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) continue;

                    $filePath = $file->getRealPath();
                    if (ContentFile::where('file_path', $filePath)->exists()) continue;

                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $lessonCount++;
                    $videoCount++;

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
                        'content_type_id' => $videoType->id,
                        'contentable_id' => $subStrand->id,
                        'contentable_type' => SubStrand::class,
                        'is_published' => true,
                    ]);
                }

                if ($videoCount > 0) {
                    $htmlCount = $lessonCount - $videoCount;
                    $this->command->line("  • Lessons: {$lessonCount} total ({$videoCount} videos + {$htmlCount} interactive)");
                } elseif ($lessonCount > 0) {
                    $this->command->line("  • Lessons: {$lessonCount} interactive files");
                }
            } elseif ($lessonCount > 0) {
                $this->command->line("  • Lessons: {$lessonCount} interactive files");
            }

            // 2. TOPICS STRAND (PDFs as complete topics/subjects)
            $pdfCount = 0;
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($basePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isDir()) continue;

                if (strtolower($file->getExtension()) !== 'pdf') continue;

                $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                // Check if filename matches any subject pattern
                $matches = false;
                foreach ($config['pdf_patterns'] ?? [] as $pattern) {
                    if (stripos($fileName, $pattern) !== false) {
                        $matches = true;
                        break;
                    }
                }
                if (!$matches) continue;

                $filePath = $file->getRealPath();
                if (ContentFile::where('file_path', $filePath)->exists()) continue;

                // Create Topics strand if needed
                $topicsStrand = $subject->strands()->where('name', 'Topics')->first();
                if (!$topicsStrand) {
                    $topicsStrand = Strand::create([
                        'learning_area_id' => $subject->id,
                        'code' => $subject->code . 'TOPICS',
                        'name' => 'Topics',
                        'order' => 2,
                    ]);
                }

                $pdfCount++;

                $existingCount = $topicsStrand->subStrands()->count();
                $code = $this->generateUniqueCode($topicsStrand->id, $topicsStrand->code . 'T', $existingCount + 1);

                $subStrand = SubStrand::create([
                    'strand_id' => $topicsStrand->id,
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

            if ($pdfCount > 0) {
                $this->command->line("  • Topics: {$pdfCount} complete subject PDFs");
            }

            if ($lessonCount == 0 && $pdfCount == 0) {
                $this->command->line("  (no content to link)");
            }
        }

        $this->command->info('');
        $this->command->info('✅ PP1 rebuilt with Topics (PDFs) + Lessons (HTMLs) structure!');
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
