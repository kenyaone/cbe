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

class RebuildGradeFiveWithCorrectDistribution extends Seeder
{
    public function run()
    {
        $this->command->info('=== REBUILDING GRADE FIVE WITH CORRECT DISTRIBUTION ===');
        $this->command->info('Videos, Interactive HTML, and PDFs in Lessons\n');

        // Fix curriculum
        LearningArea::where('grade_level', 'Grade Five')->update(['curriculum_type_id' => 1]);

        $basePath = '/home/tele/cbe-platform/storage/app/media/Grade Five Complete';
        $videoType = ContentType::firstOrCreate(['name' => 'Video']);
        $htmlType = ContentType::firstOrCreate(['name' => 'Interactive']);
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Clear existing content
        $subjects = LearningArea::where('grade_level', 'Grade Five')->get();
        foreach ($subjects as $s) {
            $s->strands()->delete();
        }

        // Create or ensure Islamic Religious Education exists
        $ire = LearningArea::where('grade_level', 'Grade Five')
            ->where('name', 'Islamic Religious Education')->first();
        if (!$ire) {
            $ire = LearningArea::create([
                'grade_level' => 'Grade Five',
                'curriculum_type_id' => 1,
                'name' => 'Islamic Religious Education',
                'code' => 'G5IRE',
                'order' => 8,
            ]);
        }

        // Configuration
        $config = [
            'English Language' => [
                'video_folder' => 'English Grade 5',
                'html_folder' => null,
                'pdf_patterns' => ['ENGLISH'],
            ],
            'Kiswahili Language' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['KISWAHILI 5'],
            ],
            'Mathematics' => [
                'video_folder' => 'Grade Five Math',
                'html_folder' => 'Grade Five Math/Interactives',
                'pdf_patterns' => ['MATHEMATICS', 'GRADE 5 MATH', 'Math notes', 'Whole_Numbers'],
            ],
            'Science and Technology' => [
                'video_folder' => null,
                'html_folder' => 'Science Interactives',
                'pdf_patterns' => ['SCIENCE BOOKS', 'Living things', 'AGRICULTURE', 'ENVIRONMENTAL'],
            ],
            'Environmental Activities' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => [],
            ],
            'Creative Activities' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['CREATIVE'],
            ],
            'Christian Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['CRE\.pdf'],
            ],
            'Islamic Religious Education' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['IRE\.pdf'],
            ],
            'Social Studies' => [
                'video_folder' => null,
                'html_folder' => null,
                'pdf_patterns' => ['GRADE FIVE SOCIAL STUDIES', 'SOCIAL STUDIES'],
            ],
        ];

        foreach ($config as $subjectName => $settings) {
            $subject = LearningArea::where('grade_level', 'Grade Five')
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

        // Link root videos (The Respiratory System.mp4)
        $science = LearningArea::where('grade_level', 'Grade Five')
            ->where('name', 'Science and Technology')->first();
        $sciStrand = $science->strands()->where('name', 'Lessons')->first();

        $rootVideos = glob("$basePath/*.mp4");
        foreach ($rootVideos as $video) {
            if (ContentFile::where('file_path', $video)->exists()) continue;

            $fileName = pathinfo($video, PATHINFO_FILENAME);
            $existingCount = $sciStrand->subStrands()->count();
            $code = $this->generateCode($sciStrand->id, $sciStrand->code . 'L', $existingCount + 1);

            $subStrand = SubStrand::create([
                'strand_id' => $sciStrand->id,
                'code' => $code,
                'name' => $fileName,
                'order' => $existingCount + 1,
            ]);

            ContentFile::create([
                'title' => $fileName,
                'file_path' => $video,
                'content_type_id' => $videoType->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
                'is_published' => true,
            ]);

            $this->command->line("✓ {$fileName} → Science and Technology");
        }

        $this->command->info('');
        $this->command->info('✅ Grade Five rebuilt with correct distribution!');
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
