<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateFormThreeSimplifiedSeeder extends Seeder
{
    public function run()
    {
        $curriculumType = CurriculumType::firstOrCreate(['name' => 'CBE']);

        // Form Three subjects - KCSE subjects
        $subjects = [
            ['name' => 'Physics', 'code' => 'F3PHY'],
            ['name' => 'Chemistry', 'code' => 'F3CHM'],
            ['name' => 'Biology', 'code' => 'F3BIO'],
            ['name' => 'Mathematics', 'code' => 'F3MAT'],
            ['name' => 'Geography', 'code' => 'F3GEO'],
            ['name' => 'English', 'code' => 'F3ENG'],
            ['name' => 'Kiswahili', 'code' => 'F3KIS'],
            ['name' => 'History and Government', 'code' => 'F3HST'],
            ['name' => 'CRE - Christian Religious Education', 'code' => 'F3CRE'],
            ['name' => 'Business Studies', 'code' => 'F3BUS'],
            ['name' => 'Computer Studies', 'code' => 'F3CMP'],
            ['name' => 'IRE - Hindu/Indian Religious Education', 'code' => 'F3IRE'],
        ];

        $order = 0;
        foreach ($subjects as $subjectData) {
            $order++;

            $subject = LearningArea::create([
                'curriculum_type_id' => $curriculumType->id,
                'grade_level' => 'Form Three',
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'order' => $order,
            ]);

            // Create a single "Lessons" strand to hold video lessons
            $strand = Strand::create([
                'learning_area_id' => $subject->id,
                'code' => $subjectData['code'] . 'S01',
                'name' => 'Video Lessons',
                'order' => 1,
            ]);

            // Videos will be added as sub-strands during content linking
        }

        $this->command->info('Form Three simplified curriculum created with ' . count($subjects) . ' subjects');
    }
}
