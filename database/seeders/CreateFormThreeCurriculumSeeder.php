<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateFormThreeCurriculumSeeder extends Seeder
{
    public function run()
    {
        // Create 8-4-4 curriculum type if it doesn't exist
        $curriculumType = CurriculumType::firstOrCreate(['name' => '8-4-4']);

        $subjects = [
            [
                'name' => 'Physics',
                'code' => 'PHY',
                'strands' => [
                    ['name' => 'Mechanics', 'substrands' => ['Motion', 'Forces', 'Energy', 'Work and Power', 'Machines']],
                    ['name' => 'Heat', 'substrands' => ['Temperature', 'Expansion', 'Heat Transfer', 'Gas Laws']],
                    ['name' => 'Waves and Optics', 'substrands' => ['Wave Motion', 'Sound', 'Light', 'Refraction', 'Lenses']],
                    ['name' => 'Electricity and Magnetism', 'substrands' => ['Current', 'Circuits', 'Electrostatics', 'Magnetism', 'Electromagnetic Induction']],
                ]
            ],
            [
                'name' => 'Chemistry',
                'code' => 'CHM',
                'strands' => [
                    ['name' => 'Inorganic Chemistry', 'substrands' => ['Atomic Structure', 'Periodicity', 'Bonding', 'Elements and Compounds', 'Reactions']],
                    ['name' => 'Organic Chemistry', 'substrands' => ['Hydrocarbons', 'Functional Groups', 'Isomerism', 'Reactions', 'Polymers']],
                    ['name' => 'Physical Chemistry', 'substrands' => ['States of Matter', 'Solutions', 'Equilibrium', 'Kinetics', 'Thermochemistry']],
                    ['name' => 'Analytical Chemistry', 'substrands' => ['Acid-Base Chemistry', 'Redox Reactions', 'Titration', 'Gravimetric Analysis']],
                ]
            ],
            [
                'name' => 'Biology',
                'code' => 'BIO',
                'strands' => [
                    ['name' => 'Cell Biology', 'substrands' => ['Cell Structure', 'Cell Division', 'Organelles', 'Transport']],
                    ['name' => 'Genetics', 'substrands' => ['Inheritance', 'DNA', 'Genes', 'Variation', 'Evolution']],
                    ['name' => 'Ecology', 'substrands' => ['Ecosystems', 'Population', 'Community', 'Succession', 'Conservation']],
                    ['name' => 'Physiology', 'substrands' => ['Nutrition', 'Respiration', 'Circulation', 'Coordination', 'Excretion']],
                ]
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MAT',
                'strands' => [
                    ['name' => 'Algebra', 'substrands' => ['Expressions', 'Equations', 'Inequalities', 'Sequences', 'Functions']],
                    ['name' => 'Geometry', 'substrands' => ['Angles', 'Shapes', 'Area and Volume', 'Coordinates', 'Transformations']],
                    ['name' => 'Trigonometry', 'substrands' => ['Trigonometric Ratios', 'Sine Rule', 'Cosine Rule', 'Bearings', 'Heights and Distances']],
                    ['name' => 'Statistics and Probability', 'substrands' => ['Data Handling', 'Measures of Central Tendency', 'Probability', 'Distributions']],
                ]
            ],
            [
                'name' => 'Geography',
                'code' => 'GEO',
                'strands' => [
                    ['name' => 'Physical Geography', 'substrands' => ['Landforms', 'Climate', 'Vegetation', 'Soils', 'Water Bodies']],
                    ['name' => 'Human Geography', 'substrands' => ['Population', 'Settlement', 'Economic Activities', 'Development', 'Interactions']],
                    ['name' => 'Regional Geography', 'substrands' => ['East Africa', 'Africa', 'Global Regions', 'Case Studies']],
                    ['name' => 'Environmental Studies', 'substrands' => ['Climate Change', 'Deforestation', 'Conservation', 'Sustainability', 'Natural Disasters']],
                ]
            ],
            [
                'name' => 'Business Studies',
                'code' => 'BUS',
                'strands' => [
                    ['name' => 'Business Organization', 'substrands' => ['Types of Business', 'Management', 'Organization', 'Structures']],
                    ['name' => 'Financial Management', 'substrands' => ['Accounting', 'Budgeting', 'Financial Statements', 'Cash Flow']],
                    ['name' => 'Marketing', 'substrands' => ['Market Research', 'Product', 'Price', 'Promotion', 'Distribution']],
                    ['name' => 'Human Resources', 'substrands' => ['Recruitment', 'Training', 'Motivation', 'Compensation']],
                ]
            ],
            [
                'name' => 'Computer Studies',
                'code' => 'CMP',
                'strands' => [
                    ['name' => 'Hardware and Systems', 'substrands' => ['Computer Architecture', 'Components', 'Networks', 'Operating Systems']],
                    ['name' => 'Programming', 'substrands' => ['Programming Concepts', 'Languages', 'Data Structures', 'Algorithms']],
                    ['name' => 'Database Management', 'substrands' => ['Database Concepts', 'Design', 'SQL', 'Data Integrity']],
                    ['name' => 'Information Systems', 'substrands' => ['Systems Analysis', 'Design', 'Implementation', 'Security']],
                ]
            ],
            [
                'name' => 'English',
                'code' => 'ENG',
                'strands' => [
                    ['name' => 'Language Skills', 'substrands' => ['Reading', 'Writing', 'Speaking', 'Listening', 'Grammar']],
                    ['name' => 'Literature', 'substrands' => ['Prose', 'Poetry', 'Drama', 'Literary Devices', 'Analysis']],
                    ['name' => 'Communication', 'substrands' => ['Comprehension', 'Expression', 'Composition', 'Mechanics']],
                ]
            ],
            [
                'name' => 'Kiswahili',
                'code' => 'KIS',
                'strands' => [
                    ['name' => 'Language Skills', 'substrands' => ['Listening', 'Speaking', 'Reading', 'Writing', 'Grammar']],
                    ['name' => 'Literature', 'substrands' => ['Poetry', 'Prose', 'Drama', 'Culture', 'Analysis']],
                    ['name' => 'Communication', 'substrands' => ['Comprehension', 'Expression', 'Interaction']],
                ]
            ],
            [
                'name' => 'CRE - Christian Religious Education',
                'code' => 'CRE',
                'strands' => [
                    ['name' => 'Biblical Knowledge', 'substrands' => ['Old Testament', 'New Testament', 'Bible Themes', 'Theology']],
                    ['name' => 'Christian Living', 'substrands' => ['Morality', 'Ethics', 'Values', 'Social Responsibility']],
                    ['name' => 'Church and Society', 'substrands' => ['Church History', 'Denominations', 'Social Issues', 'Interfaith']],
                ]
            ],
            [
                'name' => 'History and Government',
                'code' => 'HST',
                'strands' => [
                    ['name' => 'World History', 'substrands' => ['Ancient Civilizations', 'Medieval Period', 'Modern History', 'Contemporary Issues']],
                    ['name' => 'African History', 'substrands' => ['Pre-Colonial Africa', 'Colonial Period', 'Independence', 'Modern Africa']],
                    ['name' => 'Kenyan History', 'substrands' => ['Pre-Colonial Kenya', 'Colonial Rule', 'Independence', 'Post-Independence']],
                    ['name' => 'Government', 'substrands' => ['Political Systems', 'Governance', 'Democracy', 'Rights and Responsibilities']],
                ]
            ],
            [
                'name' => 'IRE - Hindu/Indian Religious Education',
                'code' => 'IRE',
                'strands' => [
                    ['name' => 'Sacred Texts and Beliefs', 'substrands' => ['Vedas', 'Philosophy', 'Concepts', 'Practices']],
                    ['name' => 'Hindu Living', 'substrands' => ['Rituals', 'Festivals', 'Ethics', 'Values']],
                    ['name' => 'Comparative Religion', 'substrands' => ['Beliefs', 'Practices', 'Similarities', 'Differences']],
                ]
            ],
        ];

        $order = 0;
        foreach ($subjects as $subjectData) {
            $order++;
            $code = 'F3' . str_pad($order, 2, '0', STR_PAD_LEFT);

            $subject = LearningArea::create([
                'curriculum_type_id' => $curriculumType->id,
                'grade_level' => 'Form Three',
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

        $this->command->info('Form Three curriculum created successfully with ' . count($subjects) . ' subjects');
    }
}
