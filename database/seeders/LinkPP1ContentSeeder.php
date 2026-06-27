<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;

class LinkPP1ContentSeeder extends Seeder
{
    public function run()
    {
        $storagePath = '/home/tele/cbe-platform/storage/app/media/PP1';

        if (!is_dir($storagePath)) {
            $this->command->error("PP1 content not found at {$storagePath}");
            return;
        }

        $created = 0;
        $skipped = 0;

        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        $subjectMap = [
            'Mathematical' => 'Mathematical Activities',
            'Language' => 'Language Activities',
            'Creative' => 'Creative Activities',
            'Environmental' => 'Environmental Activities',
        ];

        // Get first sub-strand for each subject
        $defaultSubStrands = [];
        foreach ($subjectMap as $folder => $subjectName) {
            $defaultSubStrands[$folder] = SubStrand::whereHas('strand', function($q) {
                $q->whereHas('learningArea', function($qa) {
                    $qa->where('grade_level', 'PP1');
                });
            })->whereHas('strand.learningArea', function($q) use ($subjectName) {
                $q->where('name', $subjectName);
            })->first();
        }

        $files = $this->scanDirectory($storagePath);
        $this->command->info("Found " . count($files) . " content files");

        foreach ($files as $filePath => $fileType) {
            $filename = basename($filePath);

            if (ContentFile::where('file_path', $filePath)->exists()) {
                $skipped++;
                continue;
            }

            $contentTypeKey = $fileType;
            if (!isset($types[$contentTypeKey])) {
                continue;
            }

            // Match to subject folder
            $subjectFolder = 'Mathematical'; // Default
            foreach (array_keys($subjectMap) as $folder) {
                if (stripos($filename, $folder) !== false) {
                    $subjectFolder = $folder;
                    break;
                }
            }

            if (!isset($defaultSubStrands[$subjectFolder]) || !$defaultSubStrands[$subjectFolder]) {
                continue;
            }

            ContentFile::create([
                'title' => $this->cleanFileName($filename),
                'file_path' => $filePath,
                'content_type_id' => $types[$contentTypeKey]->id,
                'contentable_id' => $defaultSubStrands[$subjectFolder]->id,
                'contentable_type' => SubStrand::class,
            ]);

            $created++;
        }

        $this->command->info("✓ Created: $created PP1 content files");
        $this->command->info("✓ Skipped: $skipped");
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

            if (in_array($ext, ['mp4', 'avi', 'mov', 'mkv'])) $type = 'Video';
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
