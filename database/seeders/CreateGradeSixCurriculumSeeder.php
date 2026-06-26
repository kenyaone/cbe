<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use App\Models\ContentFile;
use App\Models\ContentType;

class CreateGradeSixCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create Grade Six curriculum type
        $gradeSix = CurriculumType::firstOrCreate(
            ['name' => 'Grade Six'],
            ['description' => 'Grade Six (8-4-4 System) Curriculum']
        );

        // Get content types
        $types = [
            'Video' => ContentType::firstOrCreate(['name' => 'Video']),
            'Interactive' => ContentType::firstOrCreate(['name' => 'Interactive']),
            'PDF' => ContentType::firstOrCreate(['name' => 'PDF']),
        ];

        // Define Grade Six curriculum structure (8-4-4 system)
        $curriculum = [
            [
                'name' => 'English Language',
                'code' => 'G6-EL',
                'order' => 1,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly', '1.3 Oral expression']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Decoding', '2.2 Comprehension', '2.3 Fluency', '2.4 Critical reading']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Sentence formation', '3.2 Paragraph writing', '3.3 Essay writing', '3.4 Creative writing']],
                    ['code' => '4.0', 'name' => 'Grammar and Vocabulary', 'subs' => ['4.1 Parts of speech', '4.2 Tenses', '4.3 Vocabulary building', '4.4 Punctuation']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G6-KL',
                'order' => 2,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening', '1.2 Speaking', '1.3 Dialogue']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Word recognition', '2.2 Text comprehension', '2.3 Reading fluency']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Phonemic awareness', '3.2 Writing practice', '3.3 Composition']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G6-MA',
                'order' => 3,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers', 'subs' => ['1.1 Number recognition', '1.2 Place values', '1.3 Whole numbers', '1.4 Integers', '1.5 Operations']],
                    ['code' => '2.0', 'name' => 'Measurement', 'subs' => ['2.1 Length', '2.2 Mass', '2.3 Capacity', '2.4 Time', '2.5 Money', '2.6 Area and Perimeter']],
                    ['code' => '3.0', 'name' => 'Geometry', 'subs' => ['3.1 2D Shapes', '3.2 3D Shapes', '3.3 Angles', '3.4 Spatial relationships', '3.5 Transformations']],
                    ['code' => '4.0', 'name' => 'Data Handling', 'subs' => ['4.1 Collecting data', '4.2 Organizing data', '4.3 Representing data', '4.4 Interpreting data', '4.5 Probability']],
                    ['code' => '5.0', 'name' => 'Algebra', 'subs' => ['5.1 Patterns', '5.2 Variables', '5.3 Expressions', '5.4 Equations']],
                ]
            ],
            [
                'name' => 'Integrated Science',
                'code' => 'G6-SC',
                'order' => 4,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Life Sciences', 'subs' => ['1.1 Plants', '1.2 Animals', '1.3 Ecosystems', '1.4 Human body', '1.5 Nutrition']],
                    ['code' => '2.0', 'name' => 'Physical Sciences', 'subs' => ['2.1 Matter', '2.2 Energy', '2.3 Forces', '2.4 Motion', '2.5 Heat and temperature']],
                    ['code' => '3.0', 'name' => 'Earth Sciences', 'subs' => ['3.1 Weather and climate', '3.2 Rocks and soil', '3.3 Water cycle', '3.4 Atmosphere']],
                    ['code' => '4.0', 'name' => 'Technology', 'subs' => ['4.1 Design', '4.2 Materials', '4.3 Innovation', '4.4 Problem solving']],
                ]
            ],
            [
                'name' => 'Social Studies',
                'code' => 'G6-SS',
                'order' => 5,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Geography', 'subs' => ['1.1 Places and spaces', '1.2 Weather and climate', '1.3 Maps and directions', '1.4 Resources']],
                    ['code' => '2.0', 'name' => 'History', 'subs' => ['2.1 Past events', '2.2 Cultures and traditions', '2.3 Change over time', '2.4 Heritage']],
                    ['code' => '3.0', 'name' => 'Civics', 'subs' => ['3.1 Rights and responsibilities', '3.2 Community', '3.3 Government', '3.4 Citizenship']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G6-CRE',
                'order' => 6,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Bible and Jesus', 'subs' => ['1.1 Jesus teachings', '1.2 Biblical stories', '1.3 Parables']],
                    ['code' => '2.0', 'name' => 'Christian Living', 'subs' => ['2.1 Morality', '2.2 Values', '2.3 Community service', '2.4 Worship']],
                ]
            ],
            [
                'name' => 'Islamic Religious Education',
                'code' => 'G6-IRE',
                'order' => 7,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Quran and Hadith', 'subs' => ['1.1 Quranic verses', '1.2 Prophetic teachings', '1.3 Islamic history']],
                    ['code' => '2.0', 'name' => 'Islamic Living', 'subs' => ['2.1 Five pillars', '2.2 Islamic practices', '2.3 Community', '2.4 Ethics and values']],
                ]
            ],
        ];

        // Create curriculum structure
        $order = 1;
        foreach ($curriculum as $subject) {
            $learningArea = LearningArea::firstOrCreate(
                [
                    'curriculum_type_id' => $gradeSix->id,
                    'grade_level' => 'Grade Six',
                    'code' => $subject['code'],
                ],
                [
                    'name' => $subject['name'],
                    'order' => $subject['order'] ?? $order,
                ]
            );

            $strandOrder = 1;
            foreach ($subject['strands'] as $strandData) {
                $strand = Strand::firstOrCreate(
                    [
                        'learning_area_id' => $learningArea->id,
                        'code' => $strandData['code'],
                    ],
                    [
                        'name' => $strandData['name'],
                        'order' => $strandOrder,
                    ]
                );

                $substrandOrder = 1;
                foreach ($strandData['subs'] as $substrandName) {
                    // Generate code from name (e.g., "1.1 Listening" -> "1.1")
                    $code = explode(' ', $substrandName)[0];

                    SubStrand::firstOrCreate(
                        [
                            'strand_id' => $strand->id,
                            'code' => $code,
                            'name' => $substrandName,
                        ],
                        [
                            'order' => $substrandOrder,
                        ]
                    );
                    $substrandOrder++;
                }

                $strandOrder++;
            }

            $order++;
        }

        $this->command->info('✓ Grade Six curriculum structure created successfully!');
        $this->command->info('  - 7 subjects');
        $this->command->info('  - 23 strands');
        $this->command->info('  - 92 sub-strands');
        $this->command->info('');
        $this->command->info('Next: Run RemapContentFilesSeeder to map Grade Six content files');
    }
}
