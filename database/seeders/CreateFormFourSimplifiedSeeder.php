<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;

class CreateFormFourSimplifiedSeeder extends Seeder
{
    public function run()
    {
        $curriculumType = CurriculumType::firstOrCreate(['name' => 'CBE']);

        // Form Four subjects - same as Form Three
        $subjects = [
            ['name' => 'Physics', 'code' => 'F4PHY'],
            ['name' => 'Chemistry', 'code' => 'F4CHM'],
            ['name' => 'Biology', 'code' => 'F4BIO'],
            ['name' => 'Mathematics', 'code' => 'F4MAT'],
            ['name' => 'Geography', 'code' => 'F4GEO'],
            ['name' => 'English', 'code' => 'F4ENG'],
            ['name' => 'Kiswahili', 'code' => 'F4KIS'],
            ['name' => 'History and Government', 'code' => 'F4HST'],
            ['name' => 'CRE - Christian Religious Education', 'code' => 'F4CRE'],
            ['name' => 'Business Studies', 'code' => 'F4BUS'],
            ['name' => 'Computer Studies', 'code' => 'F4CMP'],
            ['name' => 'IRE - Hindu/Indian Religious Education', 'code' => 'F4IRE'],
        ];

        $order = 0;
        foreach ($subjects as $subjectData) {
            $order++;

            $subject = LearningArea::create([
                'curriculum_type_id' => $curriculumType->id,
                'grade_level' => 'Form Four',
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

        $this->command->info('Form Four simplified curriculum created with ' . count($subjects) . ' subjects');
    }
}
