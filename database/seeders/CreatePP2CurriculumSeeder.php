<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class CreatePP2CurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create PP2 curriculum type
        $pp2 = CurriculumType::firstOrCreate(
            ['name' => 'PP2'],
            ['description' => 'Pre-Primary 2 Curriculum']
        );

        // Get content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Define PP2 curriculum structure (same as PP1)
        $curriculum = [
            [
                'name' => 'Mathematical Activities',
                'code' => 'MA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Pre-Number Activities', 'subs' => ['1.1 Sorting and Grouping', '1.2 Matching and Pairing', '1.3 Ordering']],
                    ['code' => '2.0', 'name' => 'Numbers', 'subs' => ['2.1 Number Recognition', '2.2 Counting to 10', '2.3 Counting Concrete Objects', '2.4 Number Sequencing', '2.5 Number Writing']],
                    ['code' => '3.0', 'name' => 'Measurement', 'subs' => ['3.1 Sides of Objects', '3.2 Mass (Heavy and Light)', '3.3 Capacity']],
                    ['code' => '4.0', 'name' => 'Geometry', 'subs' => ['4.1 2D and 3D Shapes']],
                ]
            ],
            [
                'name' => 'Language Activities',
                'code' => 'LA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Listening and Speaking', 'subs' => ['1.1 Active Listening', '1.2 Self-Expression', '1.3 Polite Language']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Book Handling', '2.2 Reading Posture']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Writing Posture', '3.2 Pre-Writing Skills']],
                ]
            ],
            [
                'name' => 'Creative Activities',
                'code' => 'CA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Modelling', '1.2 Colouring', '1.3 Joining Dots']],
                    ['code' => '2.0', 'name' => 'Performing Arts', 'subs' => ['2.1 Musical Sounds Identification', '2.2 Singing Games']],
                ]
            ],
            [
                'name' => 'Environmental Activities',
                'code' => 'EA',
                'strands' => [
                    ['code' => '1.0', 'name' => 'My Immediate Environment', 'subs' => ['1.1 Living and Non-Living Things', '1.2 Family Members, Plants and Animals']],
                    ['code' => '2.0', 'name' => 'My Community', 'subs' => ['2.1 Manifestations of Paramatma', '2.2 Paramatma as Trimurti']],
                    ['code' => '3.0', 'name' => 'My Neighbourhood', 'subs' => ['3.1 My Classmates', '3.2 My Friends', '3.3 Parts of a Plant']],
                ]
            ],
            [
                'name' => 'CRE - Christian Religious Education',
                'code' => 'CRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Creation', 'subs' => ['1.1 Our God', '1.2 God Our Creator']],
                    ['code' => '2.0', 'name' => 'The Holy Bible', 'subs' => ['2.1 God Our Loving Father', '2.2 Bible as Holy Book', '2.3 Bible Stories']],
                    ['code' => '3.0', 'name' => 'Christian Values', 'subs' => ['3.1 Love for God', '3.2 Love for Neighbour', '3.3 Sharing with Others']],
                ]
            ],
            [
                'name' => 'HRE - Hindu Religious Education',
                'code' => 'HRE',
                'strands' => [
                    ['code' => '1.0', 'name' => 'Manifestations of Paramatma', 'subs' => ['1.1 Myself', '1.2 My Family', '1.3 Surroundings']],
                    ['code' => '2.0', 'name' => 'Enlightened Beings', 'subs' => ['2.1 Enlightened Beings', '2.2 Paramatma as Trimurti']],
                    ['code' => '3.0', 'name' => 'Sadachaar (Good Character)', 'subs' => ['3.1 Forms of Greetings', '3.2 Practice Gratitude', '3.3 Sewa (Selfless Service)']],
                    ['code' => '4.0', 'name' => 'Worship', 'subs' => ['4.1 Basic Chants', '4.2 Protocols in Worship']],
                ]
            ],
        ];

        // Create curriculum structure
        $order = 0;
        foreach ($curriculum as $areaData) {
            $area = LearningArea::firstOrCreate(
                ['curriculum_type_id' => $pp2->id, 'name' => $areaData['name']],
                ['code' => $areaData['code'], 'order' => $order++, 'grade_level' => 'PP2']
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

        $this->command->info('PP2 curriculum structure created');

        // Link PP2 files from USB
        $this->linkPP2Files($types);
    }

    private function linkPP2Files($types)
    {
        $usbPath = '/media/tele/ARISE1/PP2';
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
            $subStrand = $this->findPP2SubStrand($filePath, $filename);

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

        $this->command->info("Linked $created PP2 files");
    }

    private function findPP2SubStrand($filePath, $filename)
    {
        // Math files
        if (strpos($filePath, '/MATH/') !== false) {
            return $this->matchMathSubStrand($filename);
        }

        // Language files
        if (strpos($filePath, '/Language') !== false) {
            return $this->matchLanguageSubStrand($filename);
        }

        // PDFs
        if (strpos($filePath, '.pdf') !== false) {
            return $this->matchPDFSubStrand($filename);
        }

        return null;
    }

    private function matchMathSubStrand($filename)
    {
        $name = strtolower($filename);
        $pp2 = CurriculumType::where('name', 'PP2')->first();
        $area = LearningArea::where('curriculum_type_id', $pp2->id)
            ->where('name', 'Mathematical Activities')->first();

        if (!$area) return null;

        $mapping = [
            'sorting' => 'Sorting and Grouping',
            'grouping' => 'Sorting and Grouping',
            'matching' => 'Matching and Pairing',
            'pairing' => 'Matching and Pairing',
            'ordering' => 'Ordering',
            'number' => 'Number Recognition',
            'counting' => 'Counting to 10',
            'rote' => 'Counting to 10',
            'sides' => 'Sides of Objects',
            'corner' => 'Sides of Objects',
            'shape' => 'Sides of Objects',
            'heavy' => 'Mass (Heavy and Light)',
            'light' => 'Mass (Heavy and Light)',
            'capacity' => 'Capacity',
            'addition' => 'Number Sequencing',
            'subtraction' => 'Number Sequencing',
        ];

        foreach ($mapping as $keyword => $subStrandName) {
            if (strpos($name, $keyword) !== false) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->where('name', $subStrandName)->first();
            }
        }

        return SubStrand::whereHas('strand', function($q) use ($area) {
            $q->where('learning_area_id', $area->id);
        })->first();
    }

    private function matchLanguageSubStrand($filename)
    {
        $pp2 = CurriculumType::where('name', 'PP2')->first();
        $area = LearningArea::where('curriculum_type_id', $pp2->id)
            ->where('name', 'Language Activities')->first();

        if (!$area) return null;
        return SubStrand::whereHas('strand', function($q) use ($area) {
            $q->where('learning_area_id', $area->id);
        })->first();
    }

    private function matchPDFSubStrand($filename)
    {
        $pp2 = CurriculumType::where('name', 'PP2')->first();

        if (strpos($filename, 'MATHEMATICS') !== false) {
            $area = LearningArea::where('curriculum_type_id', $pp2->id)
                ->where('name', 'Mathematical Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'LANGUAGE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $pp2->id)
                ->where('name', 'Language Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'CREATIVE') !== false) {
            $area = LearningArea::where('curriculum_type_id', $pp2->id)
                ->where('name', 'Creative Activities')->first();
            if ($area) {
                return SubStrand::whereHas('strand', function($q) use ($area) {
                    $q->where('learning_area_id', $area->id);
                })->first();
            }
        }

        if (strpos($filename, 'ENVIRONMENTAL') !== false) {
            $area = LearningArea::where('curriculum_type_id', $pp2->id)
                ->where('name', 'Environmental Activities')->first();
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
