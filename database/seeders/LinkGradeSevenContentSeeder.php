<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use Illuminate\Support\Facades\File;

class LinkGradeSevenContentSeeder extends Seeder
{
    public function run()
    {
        $storagePath = '/home/tele/cbe-platform/storage/app/media/Grade Seven Complete';

        if (!is_dir($storagePath)) {
            $this->command->error("Grade Seven content not found at {$storagePath}");
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
        $subjectMap = [
            'English' => 'English',
            'Math' => 'Mathematics',
            'Integrated Science' => 'Integrated Science',
        ];

        $defaultSubStrands = [];
        foreach ($subjectMap as $folder => $subjectName) {
            $defaultSubStrands[$folder] = SubStrand::whereHas('strand', function($q) {
                $q->whereHas('learningArea', function($qa) {
                    $qa->where('grade_level', 'Grade Seven');
                });
            })->whereHas('strand.learningArea', function($q) use ($subjectName) {
                $q->where('name', $subjectName);
            })->first();
        }

        // Scan and add all files
        $files = $this->scanDirectory($storagePath);
        $this->command->info("Found " . count($files) . " content files to process");

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

            foreach (array_keys($defaultSubStrands) as $folder) {
                if (strpos($relativePath, "/$folder") !== false || strpos($relativePath, "/$folder/") !== false) {
                    $subjectFolder = $folder;
                    break;
                }
            }

            // If no subject folder match, use first available for this content type
            if (!$subjectFolder) {
                if ($fileType === 'PDF' || $fileType === 'Interactive') {
                    $subjectFolder = 'Math'; // Default to Math
                } else {
                    continue;
                }
            }

            if (!isset($defaultSubStrands[$subjectFolder]) || !$defaultSubStrands[$subjectFolder]) {
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

        $this->command->info("✓ Created: $created Grade Seven content files");
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
            else continue;

            $files[$file->getRealPath()] = $type;
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
