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

class RebuildFormThreeWithCorrectDistribution extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING FORM THREE WITH CORRECT DISTRIBUTION ===');
        $this->command->info('Videos and 2 PDFs in Lessons\n');

        // Fix curriculum
        LearningArea::where('grade_level', 'Form Three')->update(['curriculum_type_id' => 1]);

        $basePath = '/home/tele/cbe-platform/storage/app/media/form-three';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Clear existing content
        $subjects = LearningArea::where('grade_level', 'Form Three')->get();
        foreach ($subjects as $s) {
            $s->strands()->delete();
        }

        // Configuration
        $config = [
            'Biology' => [
                'video_folder' => 'Biology',
                'pdf_patterns' => ['FORM 3 BIOLOGY'],
            ],
            'Chemistry' => [
                'video_folder' => 'Chemistry',
                'pdf_patterns' => ['KCSE FORM 3 CHEMISTRY'],
            ],
            'Physics' => [
                'video_folder' => 'Physics',
                'pdf_patterns' => [],
            ],
            'Mathematics' => [
                'video_folder' => 'Math',
                'pdf_patterns' => [],
            ],
            'Geography' => [
                'video_folder' => 'Geography',
                'pdf_patterns' => [],
            ],
        ];

        foreach ($config as $subjectName => $settings) {
            $subject = LearningArea::where('grade_level', 'Form Three')
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
                $files = glob("$basePath/*.pdf");
                foreach ($files as $file) {
                    $fileName = pathinfo($file, PATHINFO_FILENAME);

                    // Check if matches patterns
                    $matches = false;
                    foreach ($settings['pdf_patterns'] as $pattern) {
                        if (preg_match("/$pattern/i", $fileName)) {
                            $matches = true;
                            break;
                        }
                    }

                    if (!$matches) continue;

                    // Check if already added
                    if (ContentFile::where('file_path', $file)->exists()) continue;

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
                        'file_path' => $file,
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

        $this->command->info('');
        $this->command->info('✅ Form Three rebuilt with correct distribution!');
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
