<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use Illuminate\Support\Facades\File;

class LinkUSBContentSeeder extends Seeder
{
    public function run()
    {
        $usbPath = '/media/tele/ARISE1';

        if (!is_dir($usbPath)) {
            $this->command->error("USB not found at {$usbPath}");
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

        // Map file paths to sub-strands
        $fileMapping = [
            // Math Videos
            '/PP1/Math/*.mp4' => ['learning_area' => 'Mathematical Activities', 'content_type' => 'Video'],
            '/PP1/Math/Interactives/Mobile friendly/*.html' => ['learning_area' => 'Mathematical Activities', 'content_type' => 'Interactive'],

            // English Videos & Interactives
            '/PP1/English/*.mp4' => ['learning_area' => 'Language Activities', 'content_type' => 'Video'],
            '/PP1/English/Interactives/*.html' => ['learning_area' => 'Language Activities', 'content_type' => 'Interactive'],

            // PDFs
            '/PP1/*.pdf' => ['content_type' => 'PDF'],
            '/PP1/PP1 and PP2 NOTES/*/PDF/**/*.pdf' => ['content_type' => 'PDF'],
        ];

        // Scan and add all files
        $files = $this->scanDirectory($usbPath);

        foreach ($files as $filePath => $fileType) {
            $filename = basename($filePath);

            // Skip if already in database
            if (ContentFile::where('file_path', $filePath)->exists()) {
                $skipped++;
                continue;
            }

            // Determine content type and sub-strand
            $contentTypeKey = $fileType;
            $subStrand = $this->findSubStrand($filePath, $filename);

            if (!$subStrand || !isset($types[$contentTypeKey])) {
                continue;
            }

            // Create content file
            ContentFile::create([
                'title' => $this->cleanFileName($filename),
                'file_path' => $filePath,
                'content_type_id' => $types[$contentTypeKey]->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
            ]);

            $created++;
        }

        $this->command->info("Created: $created content files");
        $this->command->info("Skipped (already exists): $skipped");
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

    private function findSubStrand($filePath, $filename)
    {
        // Math videos -> Numbers/Measurement strands
        if (strpos($filePath, '/Math/') !== false && strpos($filePath, 'Interactives') === false) {
            return $this->matchMathStrand($filename);
        }

        // Math interactives
        if (strpos($filePath, '/Math/Interactives') !== false) {
            return $this->matchMathInteractive($filename);
        }

        // English videos
        if (strpos($filePath, '/English/') !== false && strpos($filePath, 'Interactives') === false) {
            return $this->matchLanguageStrand($filename);
        }

        // English interactives
        if (strpos($filePath, '/English/Interactives') !== false) {
            return $this->matchLanguageInteractive($filename);
        }

        // Other PP1 videos
        if (strpos($filePath, '/PP1/') !== false && strpos($filename, '.mp4') !== false) {
            return $this->matchOtherStrand($filename);
        }

        // PDFs - map to learning areas
        if (strpos($filename, 'MATHEMATICS') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Mathematical Activities');
            })->first();
        }

        if (strpos($filename, 'LANGUAGE') !== false || strpos($filename, 'ENGLISH') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Language Activities');
            })->first();
        }

        if (strpos($filename, 'CREATIVE') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Creative Activities');
            })->first();
        }

        if (strpos($filename, 'ENVIRONMENTAL') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Environmental Activities');
            })->first();
        }

        return null;
    }

    private function matchMathStrand($filename)
    {
        $name = strtolower($filename);

        $mapping = [
            'sorting' => 'Sorting and Grouping',
            'grouping' => 'Sorting and Grouping',
            'matching' => 'Matching and Pairing',
            'pairing' => 'Matching and Pairing',
            'ordering' => 'Number Sequencing',
            'sequence' => 'Number Sequencing',
            'rote' => 'Counting to 10',
            'counting' => 'Counting to 10',
            'number recognition' => 'Number Recognition',
            'recognition' => 'Number Recognition',
            'sides' => 'Sides of Objects',
            'corner' => 'Sides of Objects',
            'lines' => 'Sides of Objects',
            'shape' => 'Sides of Objects',
            'heavy' => 'Mass (Heavy and Light)',
            'light' => 'Mass (Heavy and Light)',
            'mass' => 'Mass (Heavy and Light)',
            'capacity' => 'Capacity',
            'area' => 'Sides of Objects',
            'surface' => 'Sides of Objects',
            'time' => 'Counting Concrete Objects',
            'pattern' => 'Counting Concrete Objects',
        ];

        foreach ($mapping as $keyword => $subStrand) {
            if (strpos($name, $keyword) !== false) {
                return SubStrand::where('name', $subStrand)->first();
            }
        }

        // Default to first Math sub-strand
        return SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('name', 'Mathematical Activities');
        })->first();
    }

    private function matchMathInteractive($filename)
    {
        return $this->matchMathStrand($filename);
    }

    private function matchLanguageStrand($filename)
    {
        $name = strtolower($filename);

        $mapping = [
            'greeting' => 'Listening and Speaking',
            'farewell' => 'Listening and Speaking',
            'family' => 'Listening and Speaking',
            'vowel' => 'Listening and Speaking',
            'consonant' => 'Listening and Speaking',
            'letter' => 'Listening and Speaking',
            'sound' => 'Listening and Speaking',
            'abc' => 'Listening and Speaking',
            'polite' => 'Listening and Speaking',
            'magic' => 'Listening and Speaking',
        ];

        foreach ($mapping as $keyword => $subStrand) {
            if (strpos($name, $keyword) !== false) {
                return SubStrand::where('name', $subStrand)->first();
            }
        }

        return SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('name', 'Language Activities');
        })->first();
    }

    private function matchLanguageInteractive($filename)
    {
        return $this->matchLanguageStrand($filename);
    }

    private function matchOtherStrand($filename)
    {
        $name = strtolower($filename);

        if (strpos($name, 'animal') !== false || strpos($name, 'plant') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Environmental Activities');
            })->first();
        }

        if (strpos($name, 'transport') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Environmental Activities');
            })->first();
        }

        if (strpos($name, 'water') !== false) {
            return SubStrand::whereHas('strand.learningArea', function($q) {
                $q->where('name', 'Environmental Activities');
            })->first();
        }

        return SubStrand::whereHas('strand.learningArea', function($q) {
            $q->where('name', 'Environmental Activities');
        })->first();
    }

    private function cleanFileName($filename)
    {
        // Remove file extension
        $name = preg_replace('/\.(mp4|html|pdf)$/i', '', $filename);

        // Clean up common patterns
        $name = str_replace(['_', '-', '.'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        $name = trim($name);

        return $name;
    }
}
