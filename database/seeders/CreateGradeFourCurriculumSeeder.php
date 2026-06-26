<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class CreateGradeFourCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create Grade Four curriculum type
        $gradeFour = CurriculumType::firstOrCreate(
            ['name' => 'Grade Four'],
            ['description' => 'Grade Four (8-4-4 System) Curriculum']
        );

        // Get content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Define Grade Four curriculum structure (8-4-4 system)
        $curriculum = [
            [
                'name' => 'English Language',
                'code' => 'G4-EL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly', '1.3 Oral expression']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Decoding', '2.2 Comprehension', '2.3 Fluency', '2.4 Critical reading']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Sentence formation', '3.2 Paragraph writing', '3.3 Essay writing']],
                    ['code' => '4.0', 'name' => 'Grammar and Vocabulary', 'subs' => ['4.1 Parts of speech', '4.2 Tenses', '4.3 Vocabulary building']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G4-KL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening', '1.2 Speaking']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Word recognition', '2.2 Text comprehension']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Phonemic awareness', '3.2 Writing practice']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G4-MA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers and Operations', 'subs' => ['1.1 Number recognition', '1.2 Place values', '1.3 Addition', '1.4 Subtraction', '1.5 Multiplication', '1.6 Division']],
                    ['code' => '2.0', 'name' => 'Fractions and Decimals', 'subs' => ['2.1 Fractions', '2.2 Decimals', '2.3 Comparing fractions']],
                    ['code' => '3.0', 'name' => 'Measurement', 'subs' => ['3.1 Length', '3.2 Mass', '3.3 Capacity', '3.4 Time', '3.5 Money']],
                    ['code' => '4.0', 'name' => 'Geometry', 'subs' => ['4.1 2D Shapes', '4.2 3D Shapes', '4.3 Angles', '4.4 Spatial relationships']],
                    ['code' => '5.0', 'name' => 'Data handling', 'subs' => ['5.1 Collecting data', '5.2 Representing data', '5.3 Interpreting data']],
                ]
            ],
            [
                'name' => 'Science and Technology',
                'code' => 'G4-ST',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Life sciences', 'subs' => ['1.1 Plants', '1.2 Animals', '1.3 Ecosystems']],
                    ['code' => '2.0', 'name' => 'Physical sciences', 'subs' => ['2.1 Matter', '2.2 Energy', '2.3 Forces']],
                    ['code' => '3.0', 'name' => 'Technology', 'subs' => ['3.1 Design', '3.2 Materials', '3.3 Innovation']],
                ]
            ],
            [
                'name' => 'Social Studies',
                'code' => 'G4-SS',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Geography', 'subs' => ['1.1 Places and spaces', '1.2 Weather and climate']],
                    ['code' => '2.0', 'name' => 'History', 'subs' => ['2.1 Past events', '2.2 Cultures and traditions']],
                    ['code' => '3.0', 'name' => 'Civics', 'subs' => ['3.1 Rights and responsibilities', '3.2 Community']],
                ]
            ],
            [
                'name' => 'Creative Activities',
                'code' => 'G4-CA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Drawing', '1.2 Painting', '1.3 Crafts']],
                    ['code' => '2.0', 'name' => 'Music', 'subs' => ['2.1 Singing', '2.2 Rhythm', '2.3 Instruments']],
                    ['code' => '3.0', 'name' => 'Dance and Drama', 'subs' => ['3.1 Movement', '3.2 Drama']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G4-CRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'God and Creation', 'subs' => ['1.1 Our God', '1.2 Creation', '1.3 God\'s attributes']],
                    ['code' => '2.0', 'name' => 'Bible', 'subs' => ['2.1 Bible stories', '2.2 Jesus and his teachings', '2.3 Parables']],
                    ['code' => '3.0', 'name' => 'Christian Living', 'subs' => ['3.1 Values', '3.2 Relationships', '3.3 Service']],
                ]
            ],
        ];

        // Create curriculum structure
        $order = 0;
        foreach ($curriculum as $areaData) {
            $area = LearningArea::firstOrCreate(
                ['curriculum_type_id' => $gradeFour->id, 'name' => $areaData['name']],
                ['code' => $areaData['code'], 'order' => $order++, 'grade_level' => 'Grade Four']
            );

            $strandOrder = 0;
            foreach ($areaData['strands'] as $strandData) {
                $strand = Strand::firstOrCreate(
                    ['learning_area_id' => $area->id, 'code' => $strandData['code']],
                    ['name' => $strandData['name'], 'order' => $strandOrder++]
                );

                $subOrder = 0;
                foreach ($strandData['subs'] as $subName) {
                    list($code, $name) = explode(' ', $subName, 2);
                    SubStrand::firstOrCreate(
                        ['strand_id' => $strand->id, 'code' => $code],
                        ['name' => $name, 'order' => $subOrder++]
                    );
                }
            }
        }

        $this->command->info('Grade Four curriculum structure created');

        // Link Grade Four files from USB
        $this->linkGradeFourFiles($types);
    }

    private function linkGradeFourFiles($types)
    {
        $usbPath = '/media/tele/ARISE1/Grade Four Complete';
        $created = 0;

        // Scan all files
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($usbPath),
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

        // Link files to sub-strands
        foreach ($files as $filePath => $contentTypeKey) {
            if (ContentFile::where('file_path', $filePath)->exists()) {
                continue;
            }

            $filename = basename($filePath);
            $subStrand = $this->findGradeFourSubStrand($filePath, $filename);

            if (!$subStrand) {
                continue;
            }

            ContentFile::create([
                'title' => $this->cleanFileName($filename),
                'file_path' => $filePath,
                'content_type_id' => $types[$contentTypeKey]->id,
                'contentable_id' => $subStrand->id,
                'contentable_type' => SubStrand::class,
            ]);

            $created++;
        }

        $this->command->info("Linked $created Grade Four files");
    }

    private function findGradeFourSubStrand($filePath, $filename)
    {
        $gradeFour = CurriculumType::where('name', 'Grade Four')->first();
        $name = strtolower($filename);

        // English files - map to specific sub-strands
        if (strpos($filePath, '/Grade Four English/') !== false || strpos($name, 'english') !== false) {
            return $this->matchEnglishSubStrand($filename, $gradeFour);
        }

        // Math files - map to specific sub-strands
        if (strpos($filePath, '/Grade Four Math/') !== false || strpos($name, 'math') !== false) {
            return $this->matchMathSubStrand($filename, $gradeFour);
        }

        // PDFs - assign to first sub-strand of respective area
        if (strpos($name, '.pdf') !== false) {
            if (strpos($filename, 'KISWAHILI') !== false || strpos($filename, 'GREDI') !== false) {
                $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                    ->where('name', 'Kiswahili Language')->first();
                if ($area) {
                    return SubStrand::whereHas('strand', function($q) use ($area) {
                        $q->where('learning_area_id', $area->id);
                    })->first();
                }
            }
        }

        return null;
    }

    private function matchEnglishSubStrand($filename, $gradeFour)
    {
        $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
            ->where('name', 'English Language')->first();

        if (!$area) return null;

        $name = strtolower($filename);

        $mapping = [
            'tense, continuous, past' => 'Tenses',
            'noun, nouns, singular, plural' => 'Parts of speech',
            'verb, verbs, action' => 'Parts of speech',
            'adjective, adverb' => 'Parts of speech',
            'preposition, pronoun' => 'Parts of speech',
            'comprehension, reading' => 'Comprehension',
            'vocabulary, word' => 'Vocabulary building',
            'writing, essay, paragraph' => 'Writing',
        ];

        foreach ($mapping as $keywords => $subStrandName) {
            $keywordArray = explode(', ', $keywords);
            foreach ($keywordArray as $keyword) {
                if (strpos($name, strtolower($keyword)) !== false) {
                    $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                        $q->where('learning_area_id', $area->id);
                    })->where('name', $subStrandName)->first();
                    if ($sub) return $sub;
                }
            }
        }

        return SubStrand::whereHas('strand', function($q) use ($area) {
            $q->where('learning_area_id', $area->id);
        })->first();
    }

    private function matchMathSubStrand($filename, $gradeFour)
    {
        $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
            ->where('name', 'Mathematics')->first();

        if (!$area) return null;

        $name = strtolower($filename);

        // MOST SPECIFIC PATTERNS FIRST - Fractions and Decimals
        if (strpos($name, 'fraction') !== false || strpos($name, 'numerator') !== false ||
            strpos($name, 'denominator') !== false || strpos($name, 'improper') !== false ||
            strpos($name, 'mixed') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Fractions and Decimals');
            })->where('name', 'Fractions')->first();
            if ($sub) return $sub;
        }

        if (strpos($name, 'decimal') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Fractions and Decimals');
            })->where('name', 'Decimals')->first();
            if ($sub) return $sub;
        }

        // Angles and Geometry
        if (strpos($name, 'angle') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Geometry');
            })->where('name', 'Angles')->first();
            if ($sub) return $sub;
        }

        // Place values
        if (strpos($name, 'place value') !== false || strpos($name, 'digit') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Numbers and Operations');
            })->where('name', 'Place values')->first();
            if ($sub) return $sub;
        }

        // Standard operations
        $mapping = [
            'addition, adding, add' => 'Addition',
            'subtraction, subtract, subtracting' => 'Subtraction',
            'multiplication, multiply, times' => 'Multiplication',
            'division, divide' => 'Division',
            'number, counting, count' => 'Number recognition',
            'shape, 2d, 3d' => '2D Shapes',
            'length, metre, centimetre' => 'Length',
            'mass, weight, heavy, light' => 'Mass',
            'capacity, litre, volume' => 'Capacity',
            'time, clock, hour, minute' => 'Time',
            'money, shilling, coin' => 'Money',
            'data, graph, chart' => 'Collecting data',
        ];

        foreach ($mapping as $keywords => $subStrandName) {
            $keywordArray = explode(', ', $keywords);
            foreach ($keywordArray as $keyword) {
                if (strpos($name, strtolower($keyword)) !== false) {
                    $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                        $q->where('learning_area_id', $area->id);
                    })->where('name', $subStrandName)->first();
                    if ($sub) return $sub;
                }
            }
        }

        return SubStrand::whereHas('strand', function($q) use ($area) {
            $q->where('learning_area_id', $area->id);
        })->first();
    }

    private function cleanFileName($filename)
    {
        $name = preg_replace('/\.(mp4|html|pdf)$/i', '', $filename);
        $name = str_replace(['_', '-', '.'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }
}
