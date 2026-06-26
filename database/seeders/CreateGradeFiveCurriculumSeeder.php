<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class CreateGradeFiveCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create Grade Five curriculum type
        $gradeFive = CurriculumType::firstOrCreate(
            ['name' => 'Grade Five'],
            ['description' => 'Grade Five (8-4-4 System) Curriculum']
        );

        // Get content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Define Grade Five curriculum structure (8-4-4 system)
        $curriculum = [
            [
                'name' => 'English Language',
                'code' => 'G5-EL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly', '1.3 Oral expression']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Decoding', '2.2 Comprehension', '2.3 Fluency', '2.4 Critical reading']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Sentence formation', '3.2 Paragraph writing', '3.3 Essay writing', '3.4 Creative writing']],
                    ['code' => '4.0', 'name' => 'Grammar and Vocabulary', 'subs' => ['4.1 Parts of speech', '4.2 Tenses', '4.3 Vocabulary building', '4.4 Punctuation']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G5-KL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening', '1.2 Speaking', '1.3 Dialogue']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Word recognition', '2.2 Text comprehension', '2.3 Reading fluency']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Phonemic awareness', '3.2 Writing practice', '3.3 Composition']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G5-MA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers and Operations', 'subs' => ['1.1 Number recognition', '1.2 Place values', '1.3 Addition', '1.4 Subtraction', '1.5 Multiplication', '1.6 Division']],
                    ['code' => '2.0', 'name' => 'Fractions and Decimals', 'subs' => ['2.1 Fractions', '2.2 Decimals', '2.3 Comparing fractions', '2.4 Operations with fractions']],
                    ['code' => '3.0', 'name' => 'Measurement', 'subs' => ['3.1 Length', '3.2 Mass', '3.3 Capacity', '3.4 Time', '3.5 Money', '3.6 Area and Perimeter']],
                    ['code' => '4.0', 'name' => 'Geometry', 'subs' => ['4.1 2D Shapes', '4.2 3D Shapes', '4.3 Angles', '4.4 Spatial relationships']],
                    ['code' => '5.0', 'name' => 'Data handling', 'subs' => ['5.1 Collecting data', '5.2 Representing data', '5.3 Interpreting data']],
                ]
            ],
            [
                'name' => 'Science and Technology',
                'code' => 'G5-ST',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Life sciences', 'subs' => ['1.1 Plants', '1.2 Animals', '1.3 Ecosystems', '1.4 Human body']],
                    ['code' => '2.0', 'name' => 'Physical sciences', 'subs' => ['2.1 Matter', '2.2 Energy', '2.3 Forces', '2.4 Heat and temperature']],
                    ['code' => '3.0', 'name' => 'Earth sciences', 'subs' => ['3.1 Weather and climate', '3.2 Rocks and soil', '3.3 Water cycle']],
                    ['code' => '4.0', 'name' => 'Technology', 'subs' => ['4.1 Design', '4.2 Materials', '4.3 Innovation']],
                ]
            ],
            [
                'name' => 'Social Studies',
                'code' => 'G5-SS',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Geography', 'subs' => ['1.1 Places and spaces', '1.2 Weather and climate', '1.3 Maps and directions']],
                    ['code' => '2.0', 'name' => 'History', 'subs' => ['2.1 Past events', '2.2 Cultures and traditions', '2.3 Change over time']],
                    ['code' => '3.0', 'name' => 'Civics', 'subs' => ['3.1 Rights and responsibilities', '3.2 Community', '3.3 Government']],
                ]
            ],
            [
                'name' => 'Creative Activities',
                'code' => 'G5-CA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Drawing', '1.2 Painting', '1.3 Crafts', '1.4 Sculpture']],
                    ['code' => '2.0', 'name' => 'Music', 'subs' => ['2.1 Singing', '2.2 Rhythm', '2.3 Instruments', '2.4 Composition']],
                    ['code' => '3.0', 'name' => 'Dance and Drama', 'subs' => ['3.1 Movement', '3.2 Drama', '3.3 Performance']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G5-CRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'God and Creation', 'subs' => ['1.1 Our God', '1.2 Creation', '1.3 God\'s attributes']],
                    ['code' => '2.0', 'name' => 'Bible', 'subs' => ['2.1 Bible stories', '2.2 Jesus and his teachings', '2.3 Parables', '2.4 Biblical principles']],
                    ['code' => '3.0', 'name' => 'Christian Living', 'subs' => ['3.1 Values', '3.2 Relationships', '3.3 Service', '3.4 Morality']],
                ]
            ],
        ];

        // Create curriculum structure
        $order = 0;
        foreach ($curriculum as $areaData) {
            $area = LearningArea::firstOrCreate(
                ['curriculum_type_id' => $gradeFive->id, 'name' => $areaData['name']],
                ['code' => $areaData['code'], 'order' => $order++, 'grade_level' => 'Grade Five']
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

        $this->command->info('Grade Five curriculum structure created');

        // Link Grade Five files from USB
        $this->linkGradeFiveFiles($types);
    }

    private function linkGradeFiveFiles($types)
    {
        $usbPath = '/media/tele/ARISE1/Grade Five Complete';
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
            $subStrand = $this->findGradeFiveSubStrand($filePath, $filename);

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

        $this->command->info("Linked $created Grade Five files");
    }

    private function findGradeFiveSubStrand($filePath, $filename)
    {
        $gradeFive = CurriculumType::where('name', 'Grade Five')->first();

        // English files
        if (strpos($filePath, '/English Grade 5/') !== false || strpos(strtolower($filename), 'english') !== false) {
            return $this->matchEnglishSubStrand($filename, $gradeFive);
        }

        // Math files
        if (strpos($filePath, '/Grade Five Math/') !== false || strpos(strtolower($filename), 'math') !== false) {
            return $this->matchMathSubStrand($filename, $gradeFive);
        }

        // Science files
        if (strpos($filePath, '/Science Interactives/') !== false || strpos(strtolower($filename), 'science') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFive->id)
                ->where('name', 'Science and Technology')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // PDFs - assign to first sub-strand of respective area
        if (strpos($filename, '.pdf') !== false) {
            if (strpos($filename, 'KISWAHILI') !== false) {
                $area = LearningArea::where('curriculum_type_id', $gradeFive->id)
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

    private function matchEnglishSubStrand($filename, $gradeFive)
    {
        $area = LearningArea::where('curriculum_type_id', $gradeFive->id)
            ->where('name', 'English Language')->first();

        if (!$area) return null;

        $name = strtolower($filename);

        $mapping = [
            'tense, continuous, past' => 'Tenses',
            'noun, nouns, singular, plural' => 'Parts of speech',
            'verb, verbs, action' => 'Parts of speech',
            'adjective, adverb' => 'Parts of speech',
            'preposition, pronoun' => 'Parts of speech',
            'punctuation, comma, period' => 'Punctuation',
            'comprehension, reading' => 'Comprehension',
            'vocabulary, word' => 'Vocabulary building',
            'writing, essay, paragraph' => 'Essay writing',
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

    private function matchMathSubStrand($filename, $gradeFive)
    {
        $area = LearningArea::where('curriculum_type_id', $gradeFive->id)
            ->where('name', 'Mathematics')->first();

        if (!$area) return null;

        $name = strtolower($filename);

        // MOST SPECIFIC PATTERNS FIRST
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

        if (strpos($name, 'angle') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Geometry');
            })->where('name', 'Angles')->first();
            if ($sub) return $sub;
        }

        if (strpos($name, 'place value') !== false || strpos($name, 'digit') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Numbers and Operations');
            })->where('name', 'Place values')->first();
            if ($sub) return $sub;
        }

        if (strpos($name, 'area') !== false || strpos($name, 'perimeter') !== false) {
            $sub = SubStrand::whereHas('strand', function($q) use ($area) {
                $q->where('learning_area_id', $area->id)->where('name', 'Measurement');
            })->where('name', 'Area and Perimeter')->first();
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
