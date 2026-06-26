<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateGradeSevenCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Get or create CBE curriculum
        $cbe = CurriculumType::where('name', 'CBE')->first();
        if (!$cbe) {
            $cbe = CurriculumType::create([
                'name' => 'CBE',
                'description' => 'Competency-Based Education Curriculum'
            ]);
        }

        // Define Grade Seven curriculum (7-3-2-2 lower secondary system)
        $curriculum = [
            [
                'name' => 'English',
                'code' => 'G7-EL',
                'order' => 1,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly', '1.3 Oral presentation', '1.4 Discussion']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Decoding', '2.2 Comprehension', '2.3 Fluency', '2.4 Critical reading', '2.5 Skimming and scanning']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Sentence writing', '3.2 Paragraph writing', '3.3 Essay writing', '3.4 Creative writing', '3.5 Report writing']],
                    ['code' => '4.0', 'name' => 'Grammar and Vocabulary', 'subs' => ['4.1 Parts of speech', '4.2 Tenses and moods', '4.3 Sentence structure', '4.4 Vocabulary building']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G7-KL',
                'order' => 2,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening', '1.2 Speaking', '1.3 Conversation', '1.4 Presentation']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Word recognition', '2.2 Text comprehension', '2.3 Reading fluency', '2.4 Literature appreciation']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Writing basics', '3.2 Creative writing', '3.3 Composition', '3.4 Letter writing']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G7-MA',
                'order' => 3,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers and Operations', 'subs' => ['1.1 Whole numbers', '1.2 Fractions and decimals', '1.3 Percentages', '1.4 Integers', '1.5 Operations']],
                    ['code' => '2.0', 'name' => 'Measurement', 'subs' => ['2.1 Length and distance', '2.2 Area and perimeter', '2.3 Volume and capacity', '2.4 Mass and weight', '2.5 Time']],
                    ['code' => '3.0', 'name' => 'Geometry', 'subs' => ['3.1 2D Shapes', '3.2 3D Shapes', '3.3 Angles', '3.4 Coordinates', '3.5 Transformations', '3.6 Symmetry']],
                    ['code' => '4.0', 'name' => 'Algebra', 'subs' => ['4.1 Patterns', '4.2 Variables and expressions', '4.3 Equations', '4.4 Functions', '4.5 Sequences']],
                    ['code' => '5.0', 'name' => 'Data Handling', 'subs' => ['5.1 Data collection', '5.2 Organization', '5.3 Representation', '5.4 Interpretation', '5.5 Probability']],
                ]
            ],
            [
                'name' => 'Integrated Science',
                'code' => 'G7-SC',
                'order' => 4,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Scientific Investigation', 'subs' => ['1.1 Observation', '1.2 Experimentation', '1.3 Data collection', '1.4 Analysis']],
                    ['code' => '2.0', 'name' => 'Mixtures and Compounds', 'subs' => ['2.1 Matter properties', '2.2 Mixtures', '2.3 Elements and compounds', '2.4 Chemical reactions']],
                    ['code' => '3.0', 'name' => 'Living Things and Environment', 'subs' => ['3.1 Cell structure', '3.2 Organisms', '3.3 Ecosystems', '3.4 Interdependence', '3.5 Adaptation']],
                    ['code' => '4.0', 'name' => 'Force and Energy', 'subs' => ['4.1 Forces and motion', '4.2 Simple machines', '4.3 Energy types', '4.4 Energy transformation', '4.5 Electricity and magnetism']],
                ]
            ],
            [
                'name' => 'Social Studies',
                'code' => 'G7-SS',
                'order' => 5,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Geography', 'subs' => ['1.1 Maps and location', '1.2 Physical features', '1.3 Human geography', '1.4 Resources and economy']],
                    ['code' => '2.0', 'name' => 'History', 'subs' => ['2.1 Ancient civilizations', '2.2 Medieval period', '2.3 Modern history', '2.4 Cultural heritage']],
                    ['code' => '3.0', 'name' => 'Civics', 'subs' => ['3.1 Government structures', '3.2 Rights and responsibilities', '3.3 Democracy', '3.4 Social issues']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G7-CRE',
                'order' => 6,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Bible and Jesus', 'subs' => ['1.1 Jesus teachings', '1.2 Parables', '1.3 Miracles', '1.4 Church history']],
                    ['code' => '2.0', 'name' => 'Christian Living', 'subs' => ['2.1 Faith and belief', '2.2 Morality and ethics', '2.3 Worship and prayer', '2.4 Service and community']],
                ]
            ],
            [
                'name' => 'Islamic Religious Education',
                'code' => 'G7-IRE',
                'order' => 7,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Quran and Islamic Teaching', 'subs' => ['1.1 Quranic verses', '1.2 Prophetic teachings', '1.3 Islamic history', '1.4 Islamic scholars']],
                    ['code' => '2.0', 'name' => 'Islamic Living', 'subs' => ['2.1 Five pillars', '2.2 Islamic practices', '2.3 Family and community', '2.4 Ethics and values']],
                ]
            ],
            [
                'name' => 'Agriculture and Nutrition',
                'code' => 'G7-AN',
                'order' => 8,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Crop Production', 'subs' => ['1.1 Soil management', '1.2 Crop types', '1.3 Planting and harvesting', '1.4 Pest management']],
                    ['code' => '2.0', 'name' => 'Animal Husbandry', 'subs' => ['2.1 Livestock types', '2.2 Animal care', '2.3 Animal products', '2.4 Breeding']],
                    ['code' => '3.0', 'name' => 'Nutrition', 'subs' => ['3.1 Nutrients', '3.2 Balanced diet', '3.3 Food hygiene', '3.4 Food preparation']],
                ]
            ],
            [
                'name' => 'Creative Arts and Sports',
                'code' => 'G7-CA',
                'order' => 9,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Drawing', '1.2 Painting', '1.3 Sculpture', '1.4 Crafts']],
                    ['code' => '2.0', 'name' => 'Music', 'subs' => ['2.1 Instruments', '2.2 Singing', '2.3 Rhythm and melody', '2.4 Composition']],
                    ['code' => '3.0', 'name' => 'Physical Education', 'subs' => ['3.1 Team sports', '3.2 Athletics', '3.3 Games', '3.4 Fitness and health']],
                ]
            ],
            [
                'name' => 'Pre-Technical Studies',
                'code' => 'G7-PT',
                'order' => 10,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Technology Basics', 'subs' => ['1.1 Tools and equipment', '1.2 Safety', '1.3 Materials', '1.4 Measurement']],
                    ['code' => '2.0', 'name' => 'Practical Skills', 'subs' => ['2.1 Woodwork', '2.2 Metalwork', '2.3 Welding', '2.4 Electrical basics']],
                    ['code' => '3.0', 'name' => 'Design and Innovation', 'subs' => ['3.1 Problem solving', '3.2 Design process', '3.3 Prototyping', '3.4 Evaluation']],
                ]
            ],
        ];

        // Create curriculum structure
        foreach ($curriculum as $subject) {
            $learningArea = LearningArea::firstOrCreate(
                [
                    'curriculum_type_id' => $cbe->id,
                    'grade_level' => 'Grade Seven',
                    'code' => $subject['code'],
                ],
                [
                    'name' => $subject['name'],
                    'order' => $subject['order'],
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
        }

        $this->command->info('✓ Grade Seven curriculum structure created successfully!');
        $this->command->info('  - 10 subjects');
        $this->command->info('  - 35 strands');
        $this->command->info('  - 143 sub-strands');
        $this->command->info('');
        $this->command->info('Next: Run LinkGradeSevenContentSeeder to link content files');
    }
}
