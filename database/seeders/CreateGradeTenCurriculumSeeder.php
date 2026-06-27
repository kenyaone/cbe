<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateGradeTenCurriculumSeeder extends Seeder
{
    public function run()
    {
        $curriculumType = CurriculumType::firstOrCreate(['name' => '8-4-4']);

        $subjects = [
            ['name' => 'Mathematics', 'strands' => ['Algebra', 'Geometry', 'Trigonometry', 'Calculus', 'Statistics']],
            ['name' => 'Physics', 'strands' => ['Mechanics', 'Heat', 'Waves', 'Electricity', 'Modern Physics']],
            ['name' => 'Chemistry', 'strands' => ['Inorganic Chemistry', 'Organic Chemistry', 'Physical Chemistry', 'Analytical Chemistry']],
            ['name' => 'Biology', 'strands' => ['Cell Biology', 'Genetics', 'Ecology', 'Evolution', 'Physiology']],
            ['name' => 'English', 'strands' => ['Language Skills', 'Literature', 'Communication', 'Writing', 'Reading']],
            ['name' => 'Kiswahili', 'strands' => ['Language Structures', 'Communication', 'Literature', 'Grammar', 'Composition']],
            ['name' => 'Geography', 'strands' => ['Physical Geography', 'Human Geography', 'Regional Geography', 'GIS & Mapping', 'Environmental Studies']],
            ['name' => 'History and Citizenship', 'strands' => ['World History', 'African History', 'Kenyan History', 'Civics', 'Current Affairs']],
            ['name' => 'Christian Religious Education', 'strands' => ['Bible Studies', 'Christian Doctrine', 'Ethics', 'Comparative Religion', 'Church History']],
            ['name' => 'Computer Science', 'strands' => ['Programming', 'Databases', 'Networks', 'Systems Design', 'IT Concepts']],
            ['name' => 'Business Studies', 'strands' => ['Management', 'Finance', 'Marketing', 'Entrepreneurship', 'Economics']],
            ['name' => 'Economics', 'strands' => ['Microeconomics', 'Macroeconomics', 'Development Economics', 'International Trade', 'Economic Systems']],
            ['name' => 'Agriculture', 'strands' => ['Crop Production', 'Animal Husbandry', 'Farm Management', 'Agricultural Technology', 'Horticulture']],
            ['name' => 'Electrical Technology', 'strands' => ['Circuits', 'Power Systems', 'Electronics', 'Telecommunications', 'Safety']],
            ['name' => 'Building and Construction', 'strands' => ['Design', 'Materials', 'Techniques', 'Safety', 'Sustainability']],
            ['name' => 'Metal Work', 'strands' => ['Fabrication', 'Joining', 'Finishing', 'Design', 'Tools and Equipment']],
            ['name' => 'Wood Work', 'strands' => ['Wood Selection', 'Joinery', 'Finishing', 'Design', 'Machines']],
            ['name' => 'Fine Arts', 'strands' => ['Drawing', 'Painting', 'Sculpture', 'Design', 'Art History']],
            ['name' => 'Visual Arts', 'strands' => ['Design Principles', 'Composition', 'Color Theory', 'Media', 'Art Appreciation']],
            ['name' => 'Music and Dance', 'strands' => ['Music Theory', 'Instruments', 'Dance Forms', 'Composition', 'Performance']],
            ['name' => 'Performing Arts', 'strands' => ['Theatre', 'Acting', 'Production', 'Scriptwriting', 'Performance']],
            ['name' => 'Physical Education', 'strands' => ['Sports', 'Fitness', 'Techniques', 'Rules and Regulations', 'Health']],
            ['name' => 'Sports Science', 'strands' => ['Biomechanics', 'Physiology', 'Sports Psychology', 'Training', 'Nutrition']],
            ['name' => 'Life Skills Education', 'strands' => ['Decision Making', 'Relationships', 'Health', 'Career Planning', 'Coping']],
            ['name' => 'Community Service Learning', 'strands' => ['Community Engagement', 'Social Issues', 'Leadership', 'Projects', 'Citizenship']],
            ['name' => 'Aviation Technology', 'strands' => ['Aircraft Systems', 'Navigation', 'Aviation Safety', 'Aerodynamics', 'Operations']],
            ['name' => 'Maritime and Fisheries', 'strands' => ['Fishing Technology', 'Marine Biology', 'Navigation', 'Boat Building', 'Sustainability']],
            ['name' => 'Technical Applied Technology', 'strands' => ['Engineering', 'Manufacturing', 'Innovation', 'Problem Solving', 'Project Management']],
        ];

        $order = 0;
        foreach ($subjects as $subjectData) {
            $order++;
            $code = 'G10' . str_pad($order, 2, '0', STR_PAD_LEFT);

            $subject = LearningArea::create([
                'curriculum_type_id' => $curriculumType->id,
                'grade_level' => 'Grade Ten',
                'name' => $subjectData['name'],
                'code' => $code,
                'order' => $order,
            ]);

            $strandOrder = 0;
            foreach ($subjectData['strands'] as $strandName) {
                $strandOrder++;
                $strandCode = $code . 'S' . str_pad($strandOrder, 2, '0', STR_PAD_LEFT);

                $strand = Strand::create([
                    'learning_area_id' => $subject->id,
                    'code' => $strandCode,
                    'name' => $strandName,
                    'order' => $strandOrder,
                ]);

                // Create basic sub-strands
                SubStrand::create([
                    'strand_id' => $strand->id,
                    'code' => $strandCode . 'SS01',
                    'name' => $strandName,
                    'order' => 1,
                ]);
            }
        }

        $this->command->info('Grade Ten curriculum created successfully with ' . count($subjects) . ' subjects');
    }
}
