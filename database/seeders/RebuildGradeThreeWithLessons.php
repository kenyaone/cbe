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

class RebuildGradeThreeWithLessons extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING GRADE THREE ===');
        $this->command->info('Videos + PDFs in Lessons\n');

        // Fix curriculum
        LearningArea::where('grade_level', 'Grade Three')->update(['curriculum_type_id' => 1]);

        $basePath = '/home/tele/cbe-platform/storage/app/media/Grade Three Complete';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Clear existing content
        $subjects = LearningArea::where('grade_level', 'Grade Three')->get();
        foreach ($subjects as $s) {
            $s->strands()->delete();
        }

        // Configuration
        $config = [
            'English Language' => [
                'video_folder' => 'English',
                'pdf_patterns' => ['ENGLISH'],
            ],
            'Kiswahili Language' => [
                'video_folder' => null,
                'pdf_patterns' => ['KISWAHILI'],
            ],
            'Mathematics' => [
                'video_folder' => 'Math',
                'pdf_patterns' => [],
            ],
            'Environmental Activities' => [
                'video_folder' => null,
                'pdf_patterns' => ['ENVIRONMENTAL'],
            ],
            'Creative Activities' => [
                'video_folder' => null,
                'pdf_patterns' => ['CREATIVE'],
            ],
            'Christian Religious Education' => [
                'video_folder' => null,
                'pdf_patterns' => ['CRE\.pdf'],
            ],
            'Islamic Religious Education' => [
                'video_folder' => null,
                'pdf_patterns' => ['IRE\.pdf'],
            ],
        ];

        foreach ($config as $subjectName => $settings) {
            $subject = LearningArea::where('grade_level', 'Grade Three')
                ->where('name', $subjectName)
                ->first();

            if (!$subject) {
                $this->command->line("⚠ {$subjectName} not found");
                continue;
            }

            // Create Lessons strand
            $lessonsStrand = Strand::create([
                'learning_area_id' => $subject->id,
                'code' => $subject->code . 'LESS',
                'name' => 'Lessons',
                'order' => 1,
            ]);

            $this->command->info("{$subject->name}");

            $videoCount = 0;
            $pdfCount = 0;

            // Link Videos
            if ($settings['video_folder']) {
                $videoPath = "$basePath/{$settings['video_folder']}";
                if (is_dir($videoPath)) {
                    $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($videoPath),
                        RecursiveIteratorIterator::SELF_FIRST
                    );

                    foreach ($iterator as $file) {
                        if ($file->isDir()) continue;

                        $ext = strtolower($file->getExtension());
                        if (!in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) continue;

                        $filePath = $file->getRealPath();
                        $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $videoCount++;

                        $code = $this->generateCode($lessonsStrand->id, $lessonsStrand->code . 'L', $videoCount);
                        $subStrand = SubStrand::create([
                            'strand_id' => $lessonsStrand->id,
                            'code' => $code,
                            'name' => $fileName,
                            'order' => $videoCount,
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
                }
            }

            // Link PDFs
            if (count($settings['pdf_patterns']) > 0) {
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($basePath),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isDir()) continue;

                    if (strtolower($file->getExtension()) !== 'pdf') continue;

                    $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);

                    // Check if matches patterns
                    $matches = false;
                    foreach ($settings['pdf_patterns'] as $pattern) {
                        if (preg_match("/$pattern/i", $fileName)) {
                            $matches = true;
                            break;
                        }
                    }

                    if (!$matches) continue;

                    $filePath = $file->getRealPath();
                    $pdfCount++;

                    $existingCount = $lessonsStrand->subStrands()->count();
                    $code = $this->generateCode($lessonsStrand->id, $lessonsStrand->code . 'L', $existingCount + 1);
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
            }

            if ($videoCount > 0 || $pdfCount > 0) {
                $this->command->line("  ✓ V:{$videoCount} | P:{$pdfCount}");
            }
        }

        // Manually link IRE if needed
        $ire = LearningArea::where('grade_level', 'Grade Three')->where('name', 'Islamic Religious Education')->first();
        $strand = $ire->strands()->where('name', 'Lessons')->first();
        if ($strand->subStrands()->count() === 0) {
            $filePath = "$basePath/IRE.pdf";
            if (file_exists($filePath) && !ContentFile::where('file_path', $filePath)->exists()) {
                $subStrand = SubStrand::create([
                    'strand_id' => $strand->id,
                    'code' => $strand->code . 'P01',
                    'name' => 'IRE',
                    'order' => 1,
                ]);

                ContentFile::create([
                    'title' => 'IRE',
                    'file_path' => $filePath,
                    'content_type_id' => $pdfType->id,
                    'contentable_id' => $subStrand->id,
                    'contentable_type' => SubStrand::class,
                    'is_published' => true,
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('✅ Grade Three rebuilt successfully!');
    }

    private function generateCode($strandId, $prefix, $number)
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
