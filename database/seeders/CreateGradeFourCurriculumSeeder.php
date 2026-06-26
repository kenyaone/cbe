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
                    ['code' => '1.0', 'name' => 'Numbers and Operations', 'subs' => ['1.1 Number recognition', '1.2 Counting', '1.3 Addition', '1.4 Subtraction', '1.5 Multiplication', '1.6 Division']],
                    ['code' => '2.0', 'name' => 'Measurement', 'subs' => ['2.1 Length', '2.2 Mass', '2.3 Capacity', '2.4 Time', '2.5 Money']],
                    ['code' => '3.0', 'name' => 'Geometry', 'subs' => ['3.1 2D Shapes', '3.2 3D Shapes', '3.3 Spatial relationships']],
                    ['code' => '4.0', 'name' => 'Data handling', 'subs' => ['4.1 Collecting data', '4.2 Representing data', '4.3 Interpreting data']],
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
        $usbPath = '/media/tele/ARISE1/Grade Four';
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
            $subStrand = $this->findGradeFourSubStrand($filename);

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

    private function findGradeFourSubStrand($filename)
    {
        $gradeFour = CurriculumType::where('name', 'Grade Four')->first();
        $name = strtolower($filename);

        // English/Learning games
        if (strpos($name, 'english') !== false || strpos($name, 'grammar') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                ->where('name', 'English Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // Kiswahili
        if (strpos($name, 'kiswahili') !== false || strpos($name, 'gredi') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                ->where('name', 'Kiswahili Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // Mathematics
        if (strpos($name, 'math') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                ->where('name', 'Mathematics')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // Science/Technology
        if (strpos($name, 'science') !== false || strpos($name, 'technology') !== false || strpos($name, 'agriculture') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                ->where('name', 'Science and Technology')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // Social Studies
        if (strpos($name, 'social') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                ->where('name', 'Social Studies')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // Christian Religious Education
        if (strpos($name, 'christian') !== false || strpos($name, 'cre') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeFour->id)
                ->where('name', 'Christian Religious Education')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        return null;
    }

    private function cleanFileName($filename)
    {
        $name = preg_replace('/\.(mp4|html|pdf)$/i', '', $filename);
        $name = str_replace(['_', '-', '.'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }
}
