<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContentFile;
use App\Models\ContentType;
use App\Models\SubStrand;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class LinkGradeNineContentSeeder extends Seeder
{
    public function run()
    {
        $basePath = '/media/tele/CBE/Grade Nine Complete';

        if (!is_dir($basePath)) {
            $this->command->error("Grade Nine content directory not found: $basePath");
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

            // Intelligently assign to Grade Nine sub-strand
            $subStrand = $this->assignToSubStrand($filePath, $title);

            if (!$subStrand) {
                // Fallback: assign to first Grade Nine subject's first sub-strand
                $subStrand = SubStrand::whereHas('strand.learningArea', fn($q) =>
                    $q->where('grade_level', 'Grade Nine')
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

        $this->command->info("Linked $count Grade Nine content files");
    }

    private function assignToSubStrand($filePath, $title)
    {
        $lowerPath = strtolower($filePath);
        $lowerTitle = strtolower($title);

        // Pattern-based assignment for specific content
        $patterns = [
            // Mathematics patterns
            'matrices' => 'Matrices',
            'integer' => 'Integers',
            'fraction' => 'Fractions and Decimals',
            'decimal' => 'Fractions and Decimals',
            'equation' => 'Linear Equations',
            'quadratic' => 'Quadratic Equations',
            'algebra' => 'Algebra',
            'angle' => 'Angles of Elevation and Depression',
            'bearing' => 'Bearings',
            'cartesian' => 'Linear Equations',
            'logarithm' => 'Functions',
            'curve' => 'Functions',
            'data handling' => 'Data Representation',
            'frequency' => 'Data Representation',
            'circle' => 'Circles',
            'trigonometr' => 'Trigonometric Ratios',

            // Science patterns
            'atomic' => 'Atomic Structure',
            'element' => 'Elements and Compounds',
            'compound' => 'Elements and Compounds',
            'photosynthes' => 'Photosynthesis',
            'chlorophyll' => 'Photosynthesis',
            'chloroplast' => 'Photosynthesis',
            'cell' => 'Cell Biology',
            'nutrition' => 'Nutrition',
            'force' => 'Types of Forces',
            'motion' => 'Motion',
            'energy' => 'Energy Forms',
            'work' => 'Work and Power',
            'power' => 'Work and Power',
            'machine' => 'Machines',
            'wave' => 'Waves',
            'heat' => 'Heat Transfer',
            'temperature' => 'Temperature',
            'circuit' => 'Circuits',
            'current' => 'Electric Current',
            'magnet' => 'Magnetic Fields',
            'ecology' => 'Ecology',
            'biotic' => 'Ecology',
            'reproduction' => 'Reproduction',
            'fertilisation' => 'Reproduction',

            // Agriculture patterns
            'soil' => 'Soil Management',
            'plant' => 'Crop Production',
            'crop' => 'Crop Production',
            'harvest' => 'Harvesting',
            'storage' => 'Storage',
            'animal' => 'Animal Husbandry',
            'breed' => 'Breeds',
            'feed' => 'Feeding',
            'organic' => 'Crop Production',
            'graft' => 'Crop Production',

            // English patterns
            'essay' => 'Essay Writing',
            'writing' => 'Writing',
            'reading' => 'Reading Strategies',
            'comprehension' => 'Reading Strategies',
            'grammar' => 'Grammar Application',
            'vocabulary' => 'Vocabulary',

            // General patterns
            'interactive' => 'Interactive',
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
                    $q->where('grade_level', 'Grade Nine')
                )
                ->first();

            if ($subStrand) return $subStrand;
        }

        // Fallback: Try to match with subject from path
        if (strpos($lowerPath, 'math') !== false) {
            $subject = 'Mathematics';
        } elseif (strpos($lowerPath, 'science') !== false || strpos($lowerPath, 'integrated') !== false) {
            $subject = 'Integrated Science';
        } elseif (strpos($lowerPath, 'agric') !== false) {
            $subject = 'Agriculture';
        } elseif (strpos($lowerPath, 'cre') !== false) {
            $subject = 'CRE - Christian Religious Education';
        } elseif (strpos($lowerPath, 'ire') !== false) {
            $subject = 'IRE - Hindu/Indian Religious Education';
        } elseif (strpos($lowerPath, 'social') !== false) {
            $subject = 'Social Studies';
        } elseif (strpos($lowerPath, 'creative') !== false || strpos($lowerPath, 'sports') !== false) {
            $subject = 'Creative Arts and Sports';
        } elseif (strpos($lowerPath, 'english') !== false) {
            $subject = 'English';
        } elseif (strpos($lowerPath, 'kiswahili') !== false) {
            $subject = 'Kiswahili';
        } else {
            return null;
        }

        // Get first sub-strand of that subject
        $subStrand = SubStrand::whereHas('strand.learningArea', fn($q) =>
            $q->where('grade_level', 'Grade Nine')
              ->where('name', 'like', "%$subject%")
        )->first();

        return $subStrand;
    }
}
