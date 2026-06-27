<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateGradeNineCurriculumSeeder extends Seeder
{
    public function run()
    {
        $curriculumType = CurriculumType::firstOrCreate(['name' => '8-4-4']);

        $subjects = [
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'strands' => [
                    ['name' => 'Number System', 'substrands' => ['Integers', 'Fractions and Decimals', 'Rational Numbers', 'Surds', 'Sets']],
                    ['name' => 'Algebra', 'substrands' => ['Linear Equations', 'Quadratic Equations', 'Inequalities', 'Functions', 'Matrices']],
                    ['name' => 'Geometry', 'substrands' => ['Lines and Angles', 'Triangles', 'Quadrilaterals', 'Circles', 'Solid Shapes']],
                    ['name' => 'Trigonometry', 'substrands' => ['Trigonometric Ratios', 'Angles of Elevation and Depression', 'Bearings', 'Sine and Cosine Rules']],
                    ['name' => 'Calculus Basics', 'substrands' => ['Limits', 'Differentiation Basics', 'Integration Basics']],
                    ['name' => 'Statistics and Probability', 'substrands' => ['Data Collection', 'Data Representation', 'Measures of Central Tendency', 'Probability']],
                ]
            ],
            [
                'name' => 'Integrated Science',
                'code' => 'SCI',
                'strands' => [
                    ['name' => 'Mixtures, Elements and Compounds', 'substrands' => ['Atomic Structure', 'Elements and Compounds', 'Periodic Table', 'Chemical Reactions', 'Bonding']],
                    ['name' => 'Living Things and Their Environment', 'substrands' => ['Cell Biology', 'Photosynthesis', 'Nutrition', 'Reproduction', 'Ecology']],
                    ['name' => 'Force and Energy', 'substrands' => ['Types of Forces', 'Motion', 'Energy Forms', 'Work and Power', 'Machines', 'Waves']],
                    ['name' => 'Heat and Temperature', 'substrands' => ['Heat Transfer', 'Temperature', 'States of Matter', 'Thermal Properties']],
                    ['name' => 'Electricity and Magnetism', 'substrands' => ['Electric Current', 'Circuits', 'Electromagnetism', 'Magnetic Fields']],
                ]
            ],
            [
                'name' => 'English',
                'code' => 'ENG',
                'strands' => [
                    ['name' => 'Listening and Speaking', 'substrands' => ['Oral Communication', 'Presentation Skills', 'Debate', 'Discussion']],
                    ['name' => 'Reading and Comprehension', 'substrands' => ['Reading Strategies', 'Text Analysis', 'Vocabulary', 'Critical Reading']],
                    ['name' => 'Writing', 'substrands' => ['Essay Writing', 'Creative Writing', 'Formal Writing', 'Letter Writing']],
                    ['name' => 'Literature', 'substrands' => ['Poetry', 'Prose', 'Drama', 'Literary Devices']],
                    ['name' => 'Grammar and Language Use', 'substrands' => ['Parts of Speech', 'Sentence Structure', 'Verb Tenses', 'Punctuation']],
                ]
            ],
            [
                'name' => 'Kiswahili',
                'code' => 'KIS',
                'strands' => [
                    ['name' => 'Listening and Speaking', 'substrands' => ['Oral Communication', 'Pronunciation', 'Conversation', 'Presentation']],
                    ['name' => 'Reading and Comprehension', 'substrands' => ['Reading Fluency', 'Text Comprehension', 'Vocabulary', 'Cultural Context']],
                    ['name' => 'Writing', 'substrands' => ['Letter Writing', 'Essay Writing', 'Creative Writing', 'Grammar Application']],
                    ['name' => 'Literature', 'substrands' => ['Poetry', 'Prose', 'Drama', 'Cultural Literature']],
                    ['name' => 'Language Structure', 'substrands' => ['Noun Classes', 'Verb Conjugation', 'Adjectives', 'Adverbs']],
                ]
            ],
            [
                'name' => 'Social Studies',
                'code' => 'SS',
                'strands' => [
                    ['name' => 'Geography', 'substrands' => ['Map Skills', 'Climate and Weather', 'Physical Features', 'Human Features', 'Population']],
                    ['name' => 'History', 'substrands' => ['Ancient Civilizations', 'Medieval Period', 'Modern History', 'Kenyan History', 'World History']],
                    ['name' => 'Civics and Government', 'substrands' => ['Citizenship', 'Government Systems', 'Rights and Responsibilities', 'Law and Justice']],
                    ['name' => 'Economics', 'substrands' => ['Economic Systems', 'Trade', 'Money and Banking', 'Employment', 'Entrepreneurship']],
                ]
            ],
            [
                'name' => 'Agriculture',
                'code' => 'AGR',
                'strands' => [
                    ['name' => 'Crop Production', 'substrands' => ['Soil Management', 'Planting', 'Crop Care', 'Harvesting', 'Storage']],
                    ['name' => 'Animal Production', 'substrands' => ['Animal Husbandry', 'Breeds', 'Feeding', 'Health Management', 'Reproduction']],
                    ['name' => 'Agricultural Skills', 'substrands' => ['Tool Use', 'Farm Layout', 'Record Keeping', 'Enterprise Selection']],
                ]
            ],
            [
                'name' => 'CRE - Christian Religious Education',
                'code' => 'CRE',
                'strands' => [
                    ['name' => 'Bible Knowledge', 'substrands' => ['Old Testament', 'New Testament', 'Biblical Themes', 'Bible Stories']],
                    ['name' => 'Christian Doctrine', 'substrands' => ['God and Creation', 'Jesus Christ', 'Holy Spirit', 'Salvation', 'Church']],
                    ['name' => 'Christian Living', 'substrands' => ['Morality', 'Ethics', 'Social Responsibility', 'Family Life']],
                    ['name' => 'Christian Practices', 'substrands' => ['Worship', 'Prayer', 'Sacraments', 'Christian Symbols']],
                ]
            ],
            [
                'name' => 'IRE - Hindu/Indian Religious Education',
                'code' => 'IRE',
                'strands' => [
                    ['name' => 'Sacred Texts', 'substrands' => ['Vedas', 'Upanishads', 'Bhagavad Gita', 'Puranas']],
                    ['name' => 'Hindu Philosophy', 'substrands' => ['Beliefs and Concepts', 'Karma and Dharma', 'Goals of Life', 'Paths to Liberation']],
                    ['name' => 'Hindu Practices', 'substrands' => ['Worship', 'Rituals', 'Festivals', 'Sacred Symbols']],
                    ['name' => 'Hindu Ethics and Values', 'substrands' => ['Morality', 'Family Life', 'Social Duties', 'Spirituality']],
                ]
            ],
            [
                'name' => 'Creative Arts and Sports',
                'code' => 'CAS',
                'strands' => [
                    ['name' => 'Visual Arts', 'substrands' => ['Drawing and Painting', 'Sculpture', 'Design', 'Crafts', 'Digital Art']],
                    ['name' => 'Music', 'substrands' => ['Music Theory', 'Instruments', 'Singing', 'Composition', 'Performance']],
                    ['name' => 'Dance and Drama', 'substrands' => ['Dance Forms', 'Movement', 'Acting', 'Theatre Production', 'Improvisation']],
                    ['name' => 'Sports and Physical Education', 'substrands' => ['Team Sports', 'Individual Sports', 'Athletics', 'Fitness', 'Sports Skills']],
                ]
            ],
            [
                'name' => 'Pre-Technical Studies',
                'code' => 'PTS',
                'strands' => [
                    ['name' => 'Technical Drawing', 'substrands' => ['Orthographic Projection', 'Isometric Drawing', 'Scale Drawing', 'Engineering Drawing']],
                    ['name' => 'Basic Engineering', 'substrands' => ['Materials', 'Fasteners', 'Welding', 'Machining', 'Assembly']],
                    ['name' => 'Technology', 'substrands' => ['Electronics Basics', 'Simple Machines', 'Renewable Energy', 'Automation']],
                    ['name' => 'Workshop Practice', 'substrands' => ['Tool Safety', 'Wood Work', 'Metal Work', 'Finishing Techniques']],
                ]
            ],
        ];

        $order = 0;
        foreach ($subjects as $subjectData) {
            $order++;
            $code = 'G9' . str_pad($order, 2, '0', STR_PAD_LEFT);

            // Check if code already exists
            while (LearningArea::where('code', $code)->exists()) {
                $order++;
                $code = 'G9' . str_pad($order, 2, '0', STR_PAD_LEFT);
            }

            $subject = LearningArea::create([
                'curriculum_type_id' => $curriculumType->id,
                'grade_level' => 'Grade Nine',
                'name' => $subjectData['name'],
                'code' => $code,
                'order' => $order,
            ]);

            $strandOrder = 0;
            foreach ($subjectData['strands'] as $strandData) {
                $strandOrder++;
                $strandCode = $code . 'S' . str_pad($strandOrder, 2, '0', STR_PAD_LEFT);

                $strand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $strandCode,
                    'name' => $strandData['name'],
                    'order' => $strandOrder,
                ]);

                $substrandOrder = 0;
                foreach ($strandData['substrands'] as $substrandName) {
                    $substrandOrder++;
                    $substrandCode = $strandCode . 'SS' . str_pad($substrandOrder, 2, '0', STR_PAD_LEFT);

                    SubStrand::create([
                        'strand_id' => $strand->id,
                        'code' => $substrandCode,
                        'name' => $substrandName,
                        'order' => $substrandOrder,
                    ]);
                }
            }
        }

        $this->command->info('Grade Nine curriculum created successfully with ' . count($subjects) . ' subjects');
    }
}
