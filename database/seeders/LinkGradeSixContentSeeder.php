<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use Illuminate\Support\Facades\File;

class LinkGradeSixContentSeeder extends Seeder
{
    public function run()
    {
        $storagePath = '/home/tele/cbe-platform/storage/app/media/Grade Six Complete';

        if (!is_dir($storagePath)) {
            $this->command->error("Grade Six content not found at {$storagePath}");
            return;
        }

        $created = 0;
        $skipped = 0;

        // Get or create content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Get first sub-strand for each subject (default for PDFs)
        $defaultSubStrands = [
            'English' => SubStrand::whereHas('strand', function($q) {
                $q->whereHas('learningArea', function($qa) {
                    $qa->where('name', 'English Language')->where('grade_level', 'Grade Six');
                });
            })->first(),
            'Grade Six Math' => SubStrand::whereHas('strand', function($q) {
                $q->whereHas('learningArea', function($qa) {
                    $qa->where('name', 'Mathematics')->where('grade_level', 'Grade Six');
                });
            })->first(),
            'Integrated Science' => SubStrand::whereHas('strand', function($q) {
                $q->whereHas('learningArea', function($qa) {
                    $qa->where('name', 'Integrated Science')->where('grade_level', 'Grade Six');
                });
            })->first(),
        ];

        // Scan and add all files
        $files = $this->scanDirectory($storagePath);

        foreach ($files as $filePath => $fileType) {
            $filename = basename($filePath);

            // Skip if already in database
            if (ContentFile::where('file_path', $filePath)->exists()) {
                $skipped++;
                continue;
            }

            // Determine content type
            $contentTypeKey = $fileType;
            if (!isset($types[$contentTypeKey])) {
                continue;
            }

            // Find subject folder
            $relativePath = str_replace($storagePath, '', $filePath);
            $subjectFolder = null;
            foreach (array_keys($defaultSubStrands) as $subject) {
                if (strpos($relativePath, $subject) !== false) {
                    $subjectFolder = $subject;
                    break;
                }
            }

            if (!$subjectFolder || !$defaultSubStrands[$subjectFolder]) {
                continue;
            }

            // Create content file
            ContentFile::create([
                'title' => $this->cleanFileName($filename),
                'file_path' => $filePath,
                'content_type_id' => $types[$contentTypeKey]->id,
                'contentable_id' => $defaultSubStrands[$subjectFolder]->id,
                'contentable_type' => SubStrand::class,
            ]);

            $created++;
        }

        $this->command->info("✓ Created: $created Grade Six content files");
        $this->command->info("✓ Skipped (already exists): $skipped");
        $this->command->info("");
        $this->command->info("Next: Run RemapContentFilesSeeder to map files to correct sub-strands");
    }

    private function scanDirectory($basePath)
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($basePath),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) continue;

            $ext = strtolower($file->getExtension());
            $type = null;

            if ($ext === 'mp4') $type = 'Video';
            elseif ($ext === 'html') $type = 'Interactive';
            elseif ($ext === 'pdf') $type = 'PDF';

            if ($type) {
                $files[$file->getRealPath()] = $type;
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
