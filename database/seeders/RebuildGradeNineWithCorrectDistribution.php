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

class RebuildGradeNineWithCorrectDistribution extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING GRADE NINE WITH CORRECT DISTRIBUTION ===');
        $this->command->info('Videos, Interactive HTML, and PDFs in Lessons\n');

        // Fix curriculum
        LearningArea::where('grade_level', 'Grade Nine')->update(['curriculum_type_id' => 1]);

        $basePath = '/home/tele/cbe-platform/storage/app/media/grade-nine';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Clear existing content
        $subjects = LearningArea::where('grade_level', 'Grade Nine')->get();
        foreach ($subjects as $s) {
            $s->strands()->delete();
        }

        // Create missing subjects
        $newSubjects = [
            'Pre-Technical Studies' => 'G9PTS',
            'Agriculture and Nutrition' => 'G9AN',
        ];

        $maxOrder = LearningArea::where('grade_level', 'Grade Nine')->max('order') ?? 0;
        $orderCounter = 1;

        foreach ($newSubjects as $name => $code) {
            $exists = LearningArea::where('grade_level', 'Grade Nine')->where('name', $name)->exists();
            if (!$exists) {
                LearningArea::create([
                    'grade_level' => 'Grade Nine',
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
                'root_html' => ['English Complete'],
                'pdf_patterns' => ['ENGLISH'],
            ],
            'Kiswahili' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Kiswahili'],
                'pdf_patterns' => [],
            ],
            'Mathematics' => [
                'video_folder' => 'Math',
                'html_folder' => 'Math',
                'root_html' => ['Math Nine Complete'],
                'pdf_patterns' => [],
            ],
            'Integrated Science' => [
                'video_folder' => 'INTEGRATED SCIENCE',
                'html_folder' => 'INTEGRATED SCIENCE',
                'root_html' => [],
                'pdf_patterns' => ['INTEGRATED SCIENCE'],
            ],
            'Social Studies' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Social Studies Complete'],
                'pdf_patterns' => ['SOCIAL STUDIES'],
            ],
            'Creative Arts and Sports' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Creative Arts'],
                'pdf_patterns' => ['CREATIVE ARTS'],
            ],
            'CRE - Christian Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['CRE'],
                'pdf_patterns' => ['GRADE NINE CRE'],
            ],
            'IRE - Hindu/Indian Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['IRE'],
                'pdf_patterns' => ['GRADE NINE IRE'],
            ],
            'Agriculture and Nutrition' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['Agriculture Complete'],
                'pdf_patterns' => ['GRADE NINE AGRICULTURE'],
            ],
            'Pre-Technical Studies' => [
                'video_folder' => null,
                'html_folder' => null,
                'root_html' => ['PRE TECHNICAL'],
                'pdf_patterns' => [],
            ],
        ];

        foreach ($config as $subjectName => $settings) {
            $subject = LearningArea::where('grade_level', 'Grade Nine')
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

            if ($videoCount > 0 || $htmlCount > 0 || $pdfCount > 0) {
                $this->command->line("  ✓ V:{$videoCount} | I:{$htmlCount} | P:{$pdfCount}");
            }
        }

        $this->command->info('');
        $this->command->info('✅ Grade Nine rebuilt with correct distribution!');
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
