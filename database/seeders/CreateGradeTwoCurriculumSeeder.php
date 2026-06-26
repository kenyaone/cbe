<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class CreateGradeTwoCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create Grade Two curriculum type
        $gradeTwo = CurriculumType::firstOrCreate(
            ['name' => 'Grade Two'],
            ['description' => 'Grade Two (8-4-4 System) Curriculum']
        );

        // Get content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Define Grade Two curriculum structure (8-4-4 system - expanded for Grade Two)
        $curriculum = [
            [
                'name' => 'English Language',
                'code' => 'G2-EL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly', '1.3 Oral expression']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Decoding', '2.2 Comprehension', '2.3 Fluency']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Sentence formation', '3.2 Letter formation', '3.3 Word building']],
                    ['code' => '4.0', 'name' => 'Grammar', 'subs' => ['4.1 Nouns', '4.2 Verbs', '4.3 Adjectives', '4.4 Parts of speech']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G2-KL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening', '1.2 Speaking']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Word recognition', '2.2 Text comprehension']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Phonemic awareness', '3.2 Writing practice']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G2-MA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers and Operations', 'subs' => ['1.1 Number recognition', '1.2 Counting', '1.3 Addition', '1.4 Subtraction', '1.5 Multiplication', '1.6 Division']],
                    ['code' => '2.0', 'name' => 'Measurement', 'subs' => ['2.1 Length', '2.2 Mass', '2.3 Capacity', '2.4 Time and Calendar']],
                    ['code' => '3.0', 'name' => 'Geometry', 'subs' => ['3.1 2D Shapes', '3.2 3D Shapes', '3.3 Spatial relationships']],
                    ['code' => '4.0', 'name' => 'Data handling', 'subs' => ['4.1 Collecting data', '4.2 Representing data']],
                ]
            ],
            [
                'name' => 'Environmental Activities',
                'code' => 'G2-EA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Living things', 'subs' => ['1.1 Plants', '1.2 Animals', '1.3 Habitats']],
                    ['code' => '2.0', 'name' => 'Non-living things', 'subs' => ['2.1 Materials', '2.2 Weather', '2.3 Earth and beyond']],
                ]
            ],
            [
                'name' => 'Creative Activities',
                'code' => 'G2-CA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Drawing', '1.2 Painting', '1.3 Crafts', '1.4 Collage']],
                    ['code' => '2.0', 'name' => 'Music', 'subs' => ['2.1 Singing', '2.2 Rhythm', '2.3 Instruments']],
                    ['code' => '3.0', 'name' => 'Dance', 'subs' => ['3.1 Movement', '3.2 Expression']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G2-CRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'God and Creation', 'subs' => ['1.1 Our God', '1.2 Creation', '1.3 God\'s care']],
                    ['code' => '2.0', 'name' => 'Bible', 'subs' => ['2.1 Bible stories', '2.2 Jesus and his teachings', '2.3 Bible lessons']],
                    ['code' => '3.0', 'name' => 'Christian Living', 'subs' => ['3.1 Values', '3.2 Relationships', '3.3 Discipline']],
                ]
            ],
            [
                'name' => 'Islamic Religious Education',
                'code' => 'G2-IRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Allah and the Quran', 'subs' => ['1.1 Belief in Allah', '1.2 Quranic teachings']],
                    ['code' => '2.0', 'name' => 'Islamic practices', 'subs' => ['2.1 Salat', '2.2 Zakat', '2.3 Hajj']],
                    ['code' => '3.0', 'name' => 'Character', 'subs' => ['3.1 Good manners', '3.2 Social responsibility']],
                ]
            ],
        ];

        // Create curriculum structure
        $order = 0;
        foreach ($curriculum as $areaData) {
            $area = LearningArea::firstOrCreate(
                ['curriculum_type_id' => $gradeTwo->id, 'name' => $areaData['name']],
                ['code' => $areaData['code'], 'order' => $order++, 'grade_level' => 'Grade Two']
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

        $this->command->info('Grade Two curriculum structure created');

        // Link Grade Two files from USB
        $this->linkGradeTwoFiles($types);
    }

    private function linkGradeTwoFiles($types)
    {
        $usbPath = '/media/tele/ARISE1/Grade Two Complete';
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
            $subStrand = $this->findGradeTwoSubStrand($filePath, $filename);

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

        $this->command->info("Linked $created Grade Two files");
    }

    private function findGradeTwoSubStrand($filePath, $filename)
    {
        $gradeTwo = CurriculumType::where('name', 'Grade Two')->first();

        // English files
        if (strpos($filePath, '/English/') !== false || strpos(strtolower($filename), 'english') !== false) {
            return $this->matchEnglishSubStrand($filename, $gradeTwo);
        }

        // Math files
        if (strpos($filePath, '/Math/') !== false) {
            return $this->matchMathSubStrand($filename, $gradeTwo);
        }

        // PDF mapping
        if (strpos($filename, 'ENGLISH') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
                ->where('name', 'English Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'KISWAHILI') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
                ->where('name', 'Kiswahili Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'ENVIRONMENTAL') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
                ->where('name', 'Environmental Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'CREATIVE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
                ->where('name', 'Creative Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'CRE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
                ->where('name', 'Christian Religious Education')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'IRE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
                ->where('name', 'Islamic Religious Education')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        return null;
    }

    private function matchEnglishSubStrand($filename, $gradeTwo)
    {
        $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
            ->where('name', 'English Language')->first();

        if (!$area) return null;

        $name = strtolower($filename);

        $mapping = [
            'noun, nouns, singular, plural' => 'Nouns',
            'verb, verbs, tense, continuous' => 'Verbs',
            'adverb, adverbs' => 'Adjectives',
            'preposition, pre position' => 'Parts of speech',
            'possessive, possessive pronoun' => 'Parts of speech',
            'rhyming, rhyme' => 'Comprehension',
            'ing, adding' => 'Verbs',
            'transport, modes' => 'Comprehension',
            'shape, shapes' => 'Comprehension',
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

    private function matchMathSubStrand($filename, $gradeTwo)
    {
        $area = LearningArea::where('curriculum_type_id', $gradeTwo->id)
            ->where('name', 'Mathematics')->first();

        if (!$area) return null;

        $name = strtolower($filename);

        $mapping = [
            'addition, adding, add' => 'Addition',
            'subtraction, subtract, subtracting' => 'Subtraction',
            'multiplication, multiply, multiplying' => 'Multiplication',
            'division, divide, dividing' => 'Division',
            'number, counting, count' => 'Number recognition',
            'shape, shapes, 2d, 3d' => '2D Shapes',
            'length, metre, centimetre, cm' => 'Length',
            'mass, weight, heavy, light' => 'Mass',
            'capacity, litre, volume' => 'Capacity',
            'calendar, time, month, day, week' => 'Time and Calendar',
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
