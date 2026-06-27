<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LinkFormFourContentSeeder extends Seeder
{
    public function run()
    {
        $basePath = '/media/tele/CBE/Form 4';

        if (!is_dir($basePath)) {
            $this->command->error("Form Four content directory not found: $basePath");
            return;
        }

        $supportedExtensions = ['mp4', 'avi', 'mov', 'mkv', 'pdf', 'html', 'htm'];
        $count = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            $extension = strtolower($file->getExtension());
            if (!in_array($extension, $supportedExtensions)) continue;

            $filePath = $file->getRealPath();
            $title = pathinfo($file->getFilename(), PATHINFO_FILENAME);

            // Determine content type
            $contentTypeName = match($extension) {
                'mp4', 'avi', 'mov', 'mkv' => 'Video',
                'pdf' => 'PDF',
                'html', 'htm' => 'Interactive',
                default => null,
            };

            if (!$contentTypeName) continue;

            // Get or create content type
            $contentType = ContentType::firstOrCreate(['name' => $contentTypeName]);

            // Intelligently assign to Form Four sub-strand
            $subStrand = $this->assignToSubStrand($filePath, $title);

            if (!$subStrand) {
                // Fallback: assign to first Form Four subject's first sub-strand
                $subStrand = SubStrand::whereHas('strand.learningArea', fn($q) =>
                    $q->where('grade_level', 'Form Four')
                )->first();

                if (!$subStrand) continue;
            }

            // Check if file already exists
            if (ContentFile::where('file_path', $filePath)->exists()) {
                continue;
            }

            ContentFile::create([
                'title' => $title,
                'file_path' => $filePath,
                'content_type_id' => $contentType->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
                'is_published' => true,
            ]);

            $count++;
        }

        $this->command->info("Linked $count Form Four content files");
    }

    private function assignToSubStrand($filePath, $title)
    {
        $lowerPath = strtolower($filePath);
        $lowerTitle = strtolower($title);

        // Pattern-based assignment for specific content
        $patterns = [
            // Physics
            'motion' => 'Motion',
            'force' => 'Forces',
            'work' => 'Work and Power',
            'power' => 'Work and Power',
            'energy' => 'Energy',
            'electricity' => 'Current',
            'current' => 'Current',
            'circuit' => 'Circuits',
            'magnet' => 'Magnetism',
            'wave' => 'Wave Motion',
            'sound' => 'Sound',
            'light' => 'Light',
            'optic' => 'Light',
            'refraction' => 'Refraction',
            'heat' => 'Temperature',
            'temperature' => 'Temperature',

            // Chemistry
            'atom' => 'Atomic Structure',
            'element' => 'Elements and Compounds',
            'compound' => 'Elements and Compounds',
            'bond' => 'Bonding',
            'organic' => 'Hydrocarbons',
            'hydrocarbon' => 'Hydrocarbons',
            'acid' => 'Acid-Base Chemistry',
            'base' => 'Acid-Base Chemistry',
            'redox' => 'Redox Reactions',

            // Biology
            'cell' => 'Cell Structure',
            'genetics' => 'Inheritance',
            'inherit' => 'Inheritance',
            'dna' => 'DNA',
            'gene' => 'Genes',
            'evolution' => 'Evolution',
            'ecosystem' => 'Ecosystems',
            'ecology' => 'Ecosystems',
            'nutrition' => 'Nutrition',
            'respiration' => 'Respiration',
            'circulation' => 'Circulation',
            'digestion' => 'Nutrition',

            // Mathematics
            'algebra' => 'Expressions',
            'equation' => 'Equations',
            'trigonometry' => 'Trigonometric Ratios',
            'angle' => 'Angles',
            'geometry' => 'Angles',
            'shape' => 'Shapes',
            'area' => 'Area and Volume',
            'volume' => 'Area and Volume',
            'coordinate' => 'Coordinates',
            'statistics' => 'Data Handling',
            'data' => 'Data Handling',
            'probability' => 'Probability',

            // Geography
            'landform' => 'Landforms',
            'climate' => 'Climate',
            'weather' => 'Climate',
            'vegetation' => 'Vegetation',
            'soil' => 'Soils',
            'water' => 'Water Bodies',
            'population' => 'Population',
            'settlement' => 'Settlement',
            'development' => 'Development',

            // General
            'note' => 'note',
        ];

        $substrandName = null;
        $longestMatch = 0;

        foreach ($patterns as $pattern => $substrand) {
            if (strlen($pattern) > $longestMatch &&
                (strpos($lowerPath, $pattern) !== false || strpos($lowerTitle, $pattern) !== false)) {
                $substrandName = $substrand;
                $longestMatch = strlen($pattern);
            }
        }

        // If found a matching pattern, find the corresponding sub-strand
        if ($substrandName) {
            $subStrand = SubStrand::where('name', 'like', "%$substrandName%")
                ->whereHas('strand.learningArea', fn($q) =>
                    $q->where('grade_level', 'Form Four')
                )
                ->first();

            if ($subStrand) return $subStrand;
        }

        // Fallback: Try to match with subject from path
        if (strpos($lowerPath, 'physics') !== false) {
            $subject = 'Physics';
        } elseif (strpos($lowerPath, 'chemistry') !== false) {
            $subject = 'Chemistry';
        } elseif (strpos($lowerPath, 'biology') !== false) {
            $subject = 'Biology';
        } elseif (strpos($lowerPath, 'math') !== false) {
            $subject = 'Mathematics';
        } elseif (strpos($lowerPath, 'geography') !== false) {
            $subject = 'Geography';
        } elseif (strpos($lowerPath, 'business') !== false) {
            $subject = 'Business Studies';
        } elseif (strpos($lowerPath, 'computer') !== false) {
            $subject = 'Computer Studies';
        } elseif (strpos($lowerPath, 'english') !== false) {
            $subject = 'English';
        } elseif (strpos($lowerPath, 'kiswahili') !== false) {
            $subject = 'Kiswahili';
        } elseif (strpos($lowerPath, 'cre') !== false) {
            $subject = 'CRE - Christian Religious Education';
        } elseif (strpos($lowerPath, 'history') !== false) {
            $subject = 'History and Government';
        } elseif (strpos($lowerPath, 'ire') !== false) {
            $subject = 'IRE - Hindu/Indian Religious Education';
        } else {
            return null;
        }

        // Get first sub-strand of that subject
        $subStrand = SubStrand::whereHas('strand.learningArea', fn($q) =>
            $q->where('grade_level', 'Form Four')
              ->where('name', 'like', "%$subject%")
        )->first();

        return $subStrand;
    }
}
