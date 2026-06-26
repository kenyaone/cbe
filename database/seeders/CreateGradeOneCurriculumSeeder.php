<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class CreateGradeOneCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create Grade One curriculum type
        $gradeOne = CurriculumType::firstOrCreate(
            ['name' => 'Grade One'],
            ['description' => 'Grade One (8-4-4 System) Curriculum']
        );

        // Get content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Define Grade One curriculum structure (8-4-4 system - simplified for Grade One)
        $curriculum = [
            [
                'name' => 'English Language',
                'code' => 'G1-EL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Decoding', '2.2 Comprehension']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Pre-writing skills', '3.2 Letter formation']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G1-KL',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening', '1.2 Speaking']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Word recognition', '2.2 Text comprehension']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Phonemic awareness', '3.2 Writing practice']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G1-MA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers', 'subs' => ['1.1 Number recognition', '1.2 Counting', '1.3 Addition and subtraction']],
                    ['code' => '2.0', 'name' => 'Measurement', 'subs' => ['2.1 Length', '2.2 Mass', '2.3 Capacity']],
                    ['code' => '3.0', 'name' => 'Geometry', 'subs' => ['3.1 Shapes', '3.2 Spatial relationships']],
                ]
            ],
            [
                'name' => 'Environmental Activities',
                'code' => 'G1-EA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Living things', 'subs' => ['1.1 Plants', '1.2 Animals']],
                    ['code' => '2.0', 'name' => 'Non-living things', 'subs' => ['2.1 Materials', '2.2 Weather']],
                ]
            ],
            [
                'name' => 'Creative Activities',
                'code' => 'G1-CA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Drawing', '1.2 Painting', '1.3 Crafts']],
                    ['code' => '2.0', 'name' => 'Music', 'subs' => ['2.1 Singing', '2.2 Rhythm']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G1-CRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'God and Creation', 'subs' => ['1.1 Our God', '1.2 Creation']],
                    ['code' => '2.0', 'name' => 'Bible', 'subs' => ['2.1 Bible stories', '2.2 Jesus and his teachings']],
                    ['code' => '3.0', 'name' => 'Christian Living', 'subs' => ['3.1 Values', '3.2 Relationships']],
                ]
            ],
            [
                'name' => 'Islamic Religious Education',
                'code' => 'G1-IRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Allah and the Quran', 'subs' => ['1.1 Belief in Allah', '1.2 Quranic teachings']],
                    ['code' => '2.0', 'name' => 'Islamic practices', 'subs' => ['2.1 Salat', '2.2 Zakat']],
                    ['code' => '3.0', 'name' => 'Character', 'subs' => ['3.1 Good manners', '3.2 Social responsibility']],
                ]
            ],
        ];

        // Create curriculum structure
        $order = 0;
        foreach ($curriculum as $areaData) {
            $area = LearningArea::firstOrCreate(
                ['curriculum_type_id' => $gradeOne->id, 'name' => $areaData['name']],
                ['code' => $areaData['code'], 'order' => $order++, 'grade_level' => 'Grade One']
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

        $this->command->info('Grade One curriculum structure created');

        // Link Grade One files from USB
        $this->linkGradeOneFiles($types);
    }

    private function linkGradeOneFiles($types)
    {
        $usbPath = '/media/tele/ARISE1/Grade One Complete';
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
            $subStrand = $this->findGradeOneSubStrand($filePath, $filename);

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

        $this->command->info("Linked $created Grade One files");
    }

    private function findGradeOneSubStrand($filePath, $filename)
    {
        $gradeOne = CurriculumType::where('name', 'Grade One')->first();

        // English files
        if (strpos($filePath, '/English/') !== false || strpos(strtolower($filename), 'english') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'English Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // Math files
        if (strpos($filePath, '/Math/') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'Mathematics')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        // PDF mapping
        if (strpos($filename, 'ENGLISH') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'English Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'KISWAHILI') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'Kiswahili Language')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'ENVIRONMENTAL') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'Environmental Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'CREATIVE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'Creative Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'CRE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'Christian Religious Education')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'IRE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $gradeOne->id)
                ->where('name', 'Islamic Religious Education')->first();
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
