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

class RebuildPP2WithLessons extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING PP2 (PP1 PATTERN) ===');
        $this->command->info('Videos + Interactive HTML in Lessons');
        $this->command->info('PDFs directly in Lessons (no Topics)\n');

        // Fix subject names first
        $this->fixSubjectNames();

        $basePath = '/home/tele/cbe-platform/storage/app/media/PP2';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Clear existing content
        $subjects = LearningArea::where('grade_level', 'PP2')->get();
        foreach ($subjects as $s) {
            $s->strands()->delete();
        }

        // Configuration
        $config = [
            'Mathematical Activities' => [
                'video_folder' => 'MATH',
                'html_folder' => null,
                'pdf_patterns' => ['MATHEMATICS'],
            ],
            'Language Activities' => [
                'video_folder' => null,
                'html_folder' => 'Language activities/Interactives',
                'pdf_patterns' => ['LANGUAGE'],
            ],
            'Creative Activities' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['CREATIVE'],
            ],
            'Environmental Activities' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['ENVIRONMENTAL'],
            ],
            'CRE - Christian Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['CRE\.pdf'],
            ],
            'IRE - Islamic Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['IRE\.pdf'],
            ],
        ];

        foreach ($config as $subjectName => $settings) {
            $subject = LearningArea::where('grade_level', 'PP2')
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
            $htmlCount = 0;
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

            // Link HTML
            if ($settings['html_folder']) {
                $htmlPath = "$basePath/{$settings['html_folder']}";
                if (is_dir($htmlPath)) {
                    $iterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($htmlPath),
                        RecursiveIteratorIterator::SELF_FIRST
                    );

                    foreach ($iterator as $file) {
                        if ($file->isDir()) continue;

                        if (strtolower($file->getExtension()) !== 'html') continue;

                        $filePath = $file->getRealPath();
                        $fileName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $htmlCount++;

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
                            'content_type_id' => $htmlType->id,
                            'contentable_id' => $subStrand->id,
                            'contentable_type' => SubStrand::class,
                            'is_published' => true,
                        ]);
                    }
                }
            }

            // Link PDFs
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

            if ($videoCount > 0 || $htmlCount > 0 || $pdfCount > 0) {
                $this->command->line("  ✓ V:{$videoCount} | I:{$htmlCount} | P:{$pdfCount}");
            }
        }

        $this->command->info('');
        $this->command->info('✅ PP2 rebuilt successfully!');
    }

    private function fixSubjectNames()
    {
        $hre = LearningArea::where('grade_level', 'PP2')->where('name', 'HRE - Hindu Religious Education')->first();
        if ($hre) {
            $hre->update(['name' => 'IRE - Islamic Religious Education']);
        }
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
