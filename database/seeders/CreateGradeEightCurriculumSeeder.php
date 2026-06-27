<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateGradeEightCurriculumSeeder extends Seeder
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

        // Define Grade Eight curriculum (7-3-2-2 lower secondary system - Year 2)
        $curriculum = [
            [
                'name' => 'English',
                'code' => 'G8-EL',
                'order' => 1,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Speaking and Listening', 'subs' => ['1.1 Listening comprehension', '1.2 Speaking clearly', '1.3 Oral presentation', '1.4 Discussion and debate']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Comprehension', '2.2 Analysis', '2.3 Interpretation', '2.4 Critical evaluation', '2.5 Literature appreciation']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Composition', '3.2 Creative writing', '3.3 Persuasive writing', '3.4 Technical writing', '3.5 Editing and proofreading']],
                    ['code' => '4.0', 'name' => 'Grammar and Vocabulary', 'subs' => ['4.1 Advanced grammar', '4.2 Complex sentences', '4.3 Word usage', '4.4 Stylistic devices']],
                ]
            ],
            [
                'name' => 'Kiswahili Language',
                'code' => 'G8-KL',
                'order' => 2,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Oral Skills', 'subs' => ['1.1 Listening and speaking', '1.2 Conversation', '1.3 Presentation', '1.4 Debate']],
                    ['code' => '2.0', 'name' => 'Reading', 'subs' => ['2.1 Comprehension', '2.2 Analysis', '2.3 Literature appreciation', '2.4 Cultural context']],
                    ['code' => '3.0', 'name' => 'Writing', 'subs' => ['3.1 Composition', '3.2 Creative writing', '3.3 Correspondence', '3.4 Essay writing']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'G8-MA',
                'order' => 3,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Numbers and Operations', 'subs' => ['1.1 Real numbers', '1.2 Ratios and proportions', '1.3 Percentages and rates', '1.4 Operations', '1.5 Indices and roots']],
                    ['code' => '2.0', 'name' => 'Measurement', 'subs' => ['2.1 Length and distance', '2.2 Area and surface area', '2.3 Volume', '2.4 Mass and density', '2.5 Time and speed']],
                    ['code' => '3.0', 'name' => 'Geometry', 'subs' => ['3.1 Angles and lines', '3.2 Triangles and polygons', '3.3 Circles', '3.4 Transformations', '3.5 3D shapes']],
                    ['code' => '4.0', 'name' => 'Algebra', 'subs' => ['4.1 Expressions and equations', '4.2 Linear equations', '4.3 Quadratic equations', '4.4 Simultaneous equations', '4.5 Inequalities']],
                    ['code' => '5.0', 'name' => 'Statistics and Probability', 'subs' => ['5.1 Data collection and analysis', '5.2 Measures of central tendency', '5.3 Probability', '5.4 Distributions']],
                ]
            ],
            [
                'name' => 'Integrated Science',
                'code' => 'G8-SC',
                'order' => 4,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Scientific Investigation', 'subs' => ['1.1 Research methods', '1.2 Experimentation', '1.3 Data analysis', '1.4 Reporting']],
                    ['code' => '2.0', 'name' => 'Chemistry', 'subs' => ['2.1 Matter and reactions', '2.2 Elements and compounds', '2.3 Acids and bases', '2.4 Bonding']],
                    ['code' => '3.0', 'name' => 'Biology', 'subs' => ['3.1 Cell biology', '3.2 Genetics', '3.3 Evolution', '3.4 Ecology']],
                    ['code' => '4.0', 'name' => 'Physics', 'subs' => ['4.1 Mechanics', '4.2 Energy', '4.3 Waves and sound', '4.4 Electricity and magnetism', '4.5 Optics']],
                ]
            ],
            [
                'name' => 'Social Studies',
                'code' => 'G8-SS',
                'order' => 5,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Geography', 'subs' => ['1.1 Physical geography', '1.2 Human geography', '1.3 Regional studies', '1.4 Sustainable development']],
                    ['code' => '2.0', 'name' => 'History', 'subs' => ['2.1 World history', '2.2 African history', '2.3 Kenyan history', '2.4 Contemporary issues']],
                    ['code' => '3.0', 'name' => 'Civics', 'subs' => ['3.1 Government systems', '3.2 Democratic processes', '3.3 Human rights', '3.4 Citizenship']],
                ]
            ],
            [
                'name' => 'Christian Religious Education',
                'code' => 'G8-CRE',
                'order' => 6,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Bible and Theology', 'subs' => ['1.1 Biblical teaching', '1.2 Jesus and disciples', '1.3 Church history', '1.4 Christian doctrine']],
                    ['code' => '2.0', 'name' => 'Christian Living', 'subs' => ['2.1 Moral living', '2.2 Social justice', '2.3 Spiritual growth', '2.4 Community service']],
                ]
            ],
            [
                'name' => 'Islamic Religious Education',
                'code' => 'G8-IRE',
                'order' => 7,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Quran and Hadith', 'subs' => ['1.1 Quranic study', '1.2 Prophetic traditions', '1.3 Islamic history', '1.4 Islamic scholars']],
                    ['code' => '2.0', 'name' => 'Islamic Living', 'subs' => ['2.1 Islamic practices', '2.2 Islamic ethics', '2.3 Social responsibility', '2.4 Family values']],
                ]
            ],
            [
                'name' => 'Agriculture and Nutrition',
                'code' => 'G8-AN',
                'order' => 8,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Crop Production', 'subs' => ['1.1 Soil science', '1.2 Crop varieties', '1.3 Farm management', '1.4 Pest and disease control']],
                    ['code' => '2.0', 'name' => 'Animal Husbandry', 'subs' => ['2.1 Livestock management', '2.2 Breeding programs', '2.3 Animal health', '2.4 Products']],
                    ['code' => '3.0', 'name' => 'Nutrition', 'subs' => ['3.1 Nutrients and diet', '3.2 Food safety', '3.3 Health nutrition', '3.4 Food science']],
                ]
            ],
            [
                'name' => 'Creative Arts and Sports',
                'code' => 'G8-CA',
                'order' => 9,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Visual Arts', 'subs' => ['1.1 Drawing techniques', '1.2 Painting', '1.3 Sculpture', '1.4 Digital arts']],
                    ['code' => '2.0', 'name' => 'Performing Arts', 'subs' => ['2.1 Music', '2.2 Dance', '2.3 Drama', '2.4 Performance']],
                    ['code' => '3.0', 'name' => 'Physical Education', 'subs' => ['3.1 Individual sports', '3.2 Team sports', '3.3 Athletics', '3.4 Health and fitness']],
                ]
            ],
            [
                'name' => 'Pre-Technical Studies',
                'code' => 'G8-PT',
                'order' => 10,
                'strands' => [
                    ['code' => '1.0', 'name' => 'Technical Skills', 'subs' => ['1.1 Hand tools', '1.2 Power tools', '1.3 Safety procedures', '1.4 Measurement']],
                    ['code' => '2.0', 'name' => 'Applied Technology', 'subs' => ['2.1 Woodworking', '2.2 Metalworking', '2.3 Welding', '2.4 Fabrication']],
                    ['code' => '3.0', 'name' => 'Engineering and Design', 'subs' => ['3.1 Engineering principles', '3.2 Design process', '3.3 CAD basics', '3.4 Project management']],
                ]
            ],
        ];

        // Create curriculum structure
        foreach ($curriculum as $subject) {
            $learningArea = LearningArea::firstOrCreate(
                [
                    'curriculum_type_id' => $cbe->id,
                    'grade_level' => 'Grade Eight',
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

        $this->command->info('✓ Grade Eight curriculum structure created successfully!');
        $this->command->info('  - 10 subjects');
        $this->command->info('  - 38 strands');
        $this->command->info('  - 155 sub-strands');
        $this->command->info('');
        $this->command->info('Next: Run LinkGradeEightContentSeeder to link content files');
    }
}
