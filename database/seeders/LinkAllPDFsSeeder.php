<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use Illuminate\Support\Facades\File;

class LinkAllPDFsSeeder extends Seeder
{
    public function run()
    {
        $storagePath = '/home/tele/cbe-platform/storage/app/media';

        $created = 0;
        $skipped = 0;

        // Get or create PDF content type
        $pdfType = ContentType::firstOrCreate(['name' => 'PDF']);

        // Map PDF filenames to subject names
        $subjectMap = [
            'AGRICULTURE' => 'Agriculture and Nutrition',
            'SOCIAL STUD' => 'Social Studies',
            'CRE' => 'Christian Religious Education',
            'IRE' => 'Islamic Religious Education',
            'KISWAHILI' => 'Kiswahili Language',
            'CREATIVE' => 'Creative Arts and Sports',
            'PRE TECHNICAL' => 'Pre-Technical Studies',
            'ENGLISH' => 'English',
            'MATH' => 'Mathematics',
            'INTEGRATED SCIENCE' => 'Integrated Science',
            'SCIENCE' => 'Integrated Science',
            'STRAND' => 'Mathematics', // Strand PDFs default to Math
            'GREETINGS' => 'English',
            'LETTER SOUNDS' => 'English',
            'BOOK HANDLING' => 'English',
            'SELF-AWARENESS' => 'Environmental Activities',
            'READING' => 'English',
            'PHONIC' => 'English',
            'SELF-EXPRESSION' => 'English',
            'PRE-WRITING' => 'English',
            'AUDITORY' => 'English',
            'READING POSTURE' => 'English',
            'WRITING POSTURE' => 'English',
            'ACTIVE LISTENING' => 'English',
            'NAMING' => 'English',
            'AUDIENCE' => 'English',
            'EYE-HAND' => 'English',
            'SORTING' => 'Mathematics',
            'MATCHING' => 'Mathematics',
            'ORDERING' => 'Mathematics',
            'PATTERNS' => 'Mathematics',
            'TIME' => 'Mathematics',
            'MONEY' => 'Mathematics',
            'SIDES' => 'Mathematics',
            'CAPACITY' => 'Mathematics',
            'MASS' => 'Mathematics',
            'AREA' => 'Mathematics',
            'NUMBER' => 'Mathematics',
            'SHAPES' => 'Mathematics',
            'LINES' => 'Mathematics',
            'COUNTING' => 'Mathematics',
            'STORY' => 'English',
            'SYLLABLES' => 'English',
            'JESUS' => 'Christian Religious Education',
            'BIBLE' => 'Christian Religious Education',
        ];

        // Scan for all PDFs
        $files = $this->scanForPDFs($storagePath);
        $this->command->info("Found " . count($files) . " PDFs to process");

        foreach ($files as $filePath => $gradeLevel) {
            $filename = basename($filePath);

            // Skip if already in database
            if (ContentFile::where('file_path', $filePath)->exists()) {
                $skipped++;
                continue;
            }

            // Find matching subject
            $subjectName = null;
            $filenameUpper = strtoupper($filename);

            foreach ($subjectMap as $pattern => $subject) {
                if (strpos($filenameUpper, $pattern) !== false) {
                    $subjectName = $subject;
                    break;
                }
            }

            if (!$subjectName) {
                $this->command->warn("  - Skipping PDF (no subject match): $filename");
                continue;
            }

            // Get first sub-strand for this subject in this grade
            $subStrand = SubStrand::whereHas('strand', function($q) {
                $q->whereHas('learningArea', function($qa) {
                    $qa->where('curriculum_type_id', 1); // CBE curriculum
                });
            })->whereHas('strand.learningArea', function($q) use ($gradeLevel, $subjectName) {
                $q->where('grade_level', $gradeLevel)
                  ->where('name', $subjectName);
            })->orderBy('order')->first();

            if (!$subStrand) {
                $this->command->warn("  - No sub-strand found for $subjectName in $gradeLevel");
                continue;
            }

            // Create content file
            ContentFile::create([
                'title' => $this->cleanFileName($filename),
                'file_path' => $filePath,
                'content_type_id' => $pdfType->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
            ]);

            $created++;
        }

        $this->command->info("✓ Created: $created PDF content files");
        $this->command->info("✓ Skipped (already exists): $skipped");
    }

    private function scanForPDFs($basePath)
    {
        $files = [];

        // Get all grades
        $grades = ['PP1', 'PP2', 'Grade One', 'Grade Two', 'Grade Three', 'Grade Four', 'Grade Five', 'Grade Six', 'Grade Seven'];
        $dirMap = [
            'PP1' => 'PP1',
            'PP2' => 'PP2',
            'Grade One' => 'Grade One Complete',
            'Grade Two' => 'Grade Two Complete',
            'Grade Three' => 'Grade Three Complete',
            'Grade Four' => 'Grade Four Complete',
            'Grade Five' => 'Grade Five Complete',
            'Grade Six' => 'Grade Six Complete',
            'Grade Seven' => 'Grade Seven Complete',
        ];

        foreach ($grades as $grade) {
            $gradePath = $basePath . '/' . $dirMap[$grade];

            if (!is_dir($gradePath)) continue;

            // Scan all PDFs recursively in grade directory
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($gradePath),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'pdf') {
                    $files[$file->getRealPath()] = $grade;
                }
            }
        }

        return $files;
    }

    private function cleanFileName($filename)
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace(['_', '-'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return ucfirst(trim($name));
    }
}
