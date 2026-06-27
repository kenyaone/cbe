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

class RebuildGradeSevenWithCorrectDistribution extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING GRADE SEVEN WITH CORRECT DISTRIBUTION ===');
        $this->command->info('Videos, Interactive HTML, and PDFs in Lessons\n');

        // Fix curriculum
        LearningArea::where('grade_level', 'Grade Seven')->update(['curriculum_type_id' => 1]);

        $basePath = '/home/tele/cbe-platform/storage/app/media/Grade Seven Complete';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Clear existing content
        $subjects = LearningArea::where('grade_level', 'Grade Seven')->get();
        foreach ($subjects as $s) {
            $s->strands()->delete();
        }

        // Create missing subjects
        $newSubjects = [
            'Islamic Religious Education' => 'G7IRE',
            'Pre-Technical Studies' => 'G7PTS',
        ];

        $maxOrder = LearningArea::where('grade_level', 'Grade Seven')->max('order') ?? 0;
        $orderCounter = 1;

        foreach ($newSubjects as $name => $code) {
            $exists = LearningArea::where('grade_level', 'Grade Seven')->where('name', $name)->exists();
            if (!$exists) {
                LearningArea::create([
                    'grade_level' => 'Grade Seven',
                    'curriculum_type_id' => 1,
                    'name' => $name,
                    'code' => $code,
                    'order' => $maxOrder + $orderCounter++,
                ]);
            }
        }

        // Configuration
        $config = [
            'English' => [
                'video_folder' => 'English',
                'html_folder' => null,
                'root_html' => ['English Grade Seven'],
                'pdf_patterns' => ['ENGLISH'],
            ],
            'Kiswahili Language' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => [],
                'pdf_patterns' => ['KISWAHILI'],
            ],
            'Mathematics' => [
                'video_folder' => 'Math',
                'html_folder' => 'Math/Interactives',
                'root_html' => [],
                'pdf_patterns' => ['Mathematics\.pdf'],
            ],
            'Integrated Science' => [
                'video_folder' => 'Integrated Science',
                'html_folder' => 'Integrated Science/Interactives',
                'root_html' => ['Grade Seven Agriculture', 'INTEGRATED SCIENCE'],
                'pdf_patterns' => ['GRADE SEVEN AGRICULTURE', 'INTEGRATED SCIENCE BOOK'],
            ],
            'Agriculture and Nutrition' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => [],
                'pdf_patterns' => [],
            ],
            'Social Studies' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Social Studies'],
                'pdf_patterns' => ['GRADE SEVEN SOCIAL STUDEIS', 'SOCIAL STUDIES'],
            ],
            'Creative Arts and Sports' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Creative Arts and Sports'],
                'pdf_patterns' => ['GRADE SEVEN CREATIVE ARTS', 'CREATIVE ARTS AND SPORTS'],
            ],
            'Christian Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['CRE\.html'],
                'pdf_patterns' => ['GRADE SEVEN CRE', 'CRE\.pdf'],
            ],
            'Islamic Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => [],
                'pdf_patterns' => ['IRE\.pdf'],
            ],
            'Pre-Technical Studies' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Pre Technical'],
                'pdf_patterns' => ['PRE TECHNICAL'],
            ],
        ];

        foreach ($config as $subjectName => $settings) {
            $subject = LearningArea::where('grade_level', 'Grade Seven')
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

            // Link Folder HTML
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

            // Link Root HTML files
            if (count($settings['root_html']) > 0) {
                $files = glob("$basePath/*.html");
                foreach ($files as $file) {
                    $fileName = pathinfo($file, PATHINFO_FILENAME);

                    // Check if matches patterns
                    $matches = false;
                    foreach ($settings['root_html'] as $pattern) {
                        if (preg_match("/$pattern/i", $fileName)) {
                            $matches = true;
                            break;
                        }
                    }

                    if (!$matches) continue;

                    // Check if already added
                    $existing = ContentFile::where('file_path', $file)->exists();
                    if ($existing) continue;

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
                        'file_path' => $file,
                        'content_type_id' => $htmlType->id,
                        'contentable_id' => $subStrand->id,
                        'contentable_type' => SubStrand::class,
                        'is_published' => true,
                    ]);
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

                    // Check if already added
                    $existing = ContentFile::where('file_path', $file->getRealPath())->exists();
                    if ($existing) continue;

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

            if ($videoCount > 0 || $htmlCount > 0 || $pdfCount > 0) {
                $this->command->line("  ✓ V:{$videoCount} | I:{$htmlCount} | P:{$pdfCount}");
            }
        }

        $this->command->info('');
        $this->command->info('✅ Grade Seven rebuilt with correct distribution!');
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
