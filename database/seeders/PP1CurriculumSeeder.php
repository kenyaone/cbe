<?php

namespace Database\Seeders;

use App\Models\CurriculumType;
use App\Models\LearningArea;
use App\Models\Strand;
use App\Models\SubStrand;
use Illuminate\Database\Seeder;

class PP1CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $cbe = CurriculumType::where('name', 'CBE')->first();

        // MATHEMATICAL ACTIVITIES
        $mathArea = LearningArea::create([
            'curriculum_type_id' => $cbe->id,
            'code' => 'LA001',
            'name' => 'Mathematical Activities',
            'description' => 'Pre-primary mathematical activities focusing on numbers, measurement, and pre-number concepts',
            'lessons_per_week' => 5,
            'grade_level' => 'PP1',
            'order' => 1
        ]);

        // Strand 1.0: Pre-Number Activities
        $strand1 = Strand::create([
            'learning_area_id' => $mathArea->id,
            'code' => '1.0',
            'name' => 'Pre-Number Activities',
            'order' => 1
        ]);

        SubStrand::create(['strand_id' => $strand1->id, 'code' => '1.1', 'name' => 'Sorting and Grouping', 'lesson_count' => 8, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand1->id, 'code' => '1.2', 'name' => 'Matching and Pairing', 'lesson_count' => 8, 'order' => 2]);
        SubStrand::create(['strand_id' => $strand1->id, 'code' => '1.3', 'name' => 'Ordering', 'lesson_count' => 8, 'order' => 3]);

        // Strand 2.0: Numbers
        $strand2 = Strand::create([
            'learning_area_id' => $mathArea->id,
            'code' => '2.0',
            'name' => 'Numbers',
            'order' => 2
        ]);

        SubStrand::create(['strand_id' => $strand2->id, 'code' => '2.1', 'name' => 'Number Recognition', 'order' => 1]);
        SubStrand::create(['strand_id' => $strand2->id, 'code' => '2.2', 'name' => 'Counting to 10', 'order' => 2]);
        SubStrand::create(['strand_id' => $strand2->id, 'code' => '2.3', 'name' => 'Counting Concrete Objects', 'lesson_count' => 10, 'order' => 3]);
        SubStrand::create(['strand_id' => $strand2->id, 'code' => '2.4', 'name' => 'Number Sequencing', 'lesson_count' => 10, 'order' => 4]);
        SubStrand::create(['strand_id' => $strand2->id, 'code' => '2.5', 'name' => 'Number Writing', 'lesson_count' => 10, 'order' => 5]);

        // Strand 3.0: Measurement
        $strand3 = Strand::create([
            'learning_area_id' => $mathArea->id,
            'code' => '3.0',
            'name' => 'Measurement',
            'order' => 3
        ]);

        SubStrand::create(['strand_id' => $strand3->id, 'code' => '3.1', 'name' => 'Sides of Objects', 'lesson_count' => 10, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand3->id, 'code' => '3.2', 'name' => 'Mass (Heavy and Light)', 'lesson_count' => 10, 'order' => 2]);
        SubStrand::create(['strand_id' => $strand3->id, 'code' => '3.3', 'name' => 'Capacity', 'lesson_count' => 10, 'order' => 3]);

        // LANGUAGE ACTIVITIES
        $langArea = LearningArea::create([
            'curriculum_type_id' => $cbe->id,
            'code' => 'LA002',
            'name' => 'Language Activities',
            'description' => 'Listening, speaking, reading, and writing activities',
            'lessons_per_week' => 5,
            'grade_level' => 'PP1',
            'order' => 2
        ]);

        $strand4 = Strand::create([
            'learning_area_id' => $langArea->id,
            'code' => '1.0',
            'name' => 'Listening and Speaking',
            'order' => 1
        ]);

        SubStrand::create(['strand_id' => $strand4->id, 'code' => '1.1', 'name' => 'Active Listening', 'lesson_count' => 4, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand4->id, 'code' => '1.2', 'name' => 'Self-Expression', 'lesson_count' => 4, 'order' => 2]);
        SubStrand::create(['strand_id' => $strand4->id, 'code' => '1.3', 'name' => 'Polite Language', 'lesson_count' => 3, 'order' => 3]);

        $strand5 = Strand::create([
            'learning_area_id' => $langArea->id,
            'code' => '2.0',
            'name' => 'Reading',
            'order' => 2
        ]);

        SubStrand::create(['strand_id' => $strand5->id, 'code' => '2.1', 'name' => 'Book Handling', 'lesson_count' => 3, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand5->id, 'code' => '2.2', 'name' => 'Reading Posture', 'lesson_count' => 3, 'order' => 2]);

        $strand6 = Strand::create([
            'learning_area_id' => $langArea->id,
            'code' => '3.0',
            'name' => 'Writing',
            'order' => 3
        ]);

        SubStrand::create(['strand_id' => $strand6->id, 'code' => '3.1', 'name' => 'Writing Posture', 'lesson_count' => 3, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand6->id, 'code' => '3.2', 'name' => 'Pre-Writing Skills', 'lesson_count' => 3, 'order' => 2]);

        // CREATIVE ACTIVITIES
        $creativeArea = LearningArea::create([
            'curriculum_type_id' => $cbe->id,
            'code' => 'LA003',
            'name' => 'Creative Activities',
            'description' => 'Art, craft, music, and movement activities',
            'lessons_per_week' => 6,
            'grade_level' => 'PP1',
            'order' => 3
        ]);

        $strand7 = Strand::create([
            'learning_area_id' => $creativeArea->id,
            'code' => '1.0',
            'name' => 'Art and Craft',
            'order' => 1
        ]);

        SubStrand::create(['strand_id' => $strand7->id, 'code' => '1.1', 'name' => 'Modelling', 'lesson_count' => 20, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand7->id, 'code' => '1.2', 'name' => 'Colouring', 'lesson_count' => 20, 'order' => 2]);
        SubStrand::create(['strand_id' => $strand7->id, 'code' => '1.3', 'name' => 'Joining Dots', 'lesson_count' => 20, 'order' => 3]);

        $strand8 = Strand::create([
            'learning_area_id' => $creativeArea->id,
            'code' => '2.0',
            'name' => 'Music and Movement',
            'order' => 2
        ]);

        SubStrand::create(['strand_id' => $strand8->id, 'code' => '2.1', 'name' => 'Musical Sounds Identification', 'lesson_count' => 20, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand8->id, 'code' => '2.2', 'name' => 'Singing Games', 'lesson_count' => 20, 'order' => 2]);

        // ENVIRONMENTAL ACTIVITIES
        $envArea = LearningArea::create([
            'curriculum_type_id' => $cbe->id,
            'code' => 'LA004',
            'name' => 'Environmental Activities',
            'description' => 'Activities exploring immediate environment and community',
            'lessons_per_week' => 5,
            'grade_level' => 'PP1',
            'order' => 4
        ]);

        $strand9 = Strand::create([
            'learning_area_id' => $envArea->id,
            'code' => '1.0',
            'name' => 'My Immediate Environment',
            'order' => 1
        ]);

        SubStrand::create(['strand_id' => $strand9->id, 'code' => '1.1', 'name' => 'Living and Non-Living Things', 'order' => 1]);
        SubStrand::create(['strand_id' => $strand9->id, 'code' => '1.2', 'name' => 'Family Members, Plants and Animals', 'order' => 2]);

        $strand10 = Strand::create([
            'learning_area_id' => $envArea->id,
            'code' => '2.0',
            'name' => 'My Community',
            'order' => 2
        ]);

        SubStrand::create(['strand_id' => $strand10->id, 'code' => '2.1', 'name' => 'Manifestations of Paramatma', 'lesson_count' => 4, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand10->id, 'code' => '2.2', 'name' => 'Paramatma as Trimurti', 'order' => 2]);

        $strand11 = Strand::create([
            'learning_area_id' => $envArea->id,
            'code' => '3.0',
            'name' => 'My Neighbourhood',
            'order' => 3
        ]);

        SubStrand::create(['strand_id' => $strand11->id, 'code' => '3.1', 'name' => 'My Classmates', 'lesson_count' => 10, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand11->id, 'code' => '3.2', 'name' => 'My Friends', 'lesson_count' => 10, 'order' => 2]);
        SubStrand::create(['strand_id' => $strand11->id, 'code' => '3.3', 'name' => 'Parts of a Plant', 'lesson_count' => 10, 'order' => 3]);

        // CRE (CHRISTIAN RELIGIOUS EDUCATION)
        $creArea = LearningArea::create([
            'curriculum_type_id' => $cbe->id,
            'code' => 'LA005',
            'name' => 'CRE - Christian Religious Education',
            'description' => 'Christian religious education and values',
            'lessons_per_week' => 3,
            'grade_level' => 'PP1',
            'order' => 5
        ]);

        $strand12 = Strand::create([
            'learning_area_id' => $creArea->id,
            'code' => '1.0',
            'name' => 'Creation',
            'order' => 1
        ]);

        SubStrand::create(['strand_id' => $strand12->id, 'code' => '1.1', 'name' => 'Our God', 'order' => 1]);
        SubStrand::create(['strand_id' => $strand12->id, 'code' => '1.2', 'name' => 'God Our Creator', 'order' => 2]);

        $strand13 = Strand::create([
            'learning_area_id' => $creArea->id,
            'code' => '2.0',
            'name' => 'The Holy Bible',
            'order' => 2
        ]);

        SubStrand::create(['strand_id' => $strand13->id, 'code' => '2.1', 'name' => 'God Our Loving Father', 'order' => 1]);
        SubStrand::create(['strand_id' => $strand13->id, 'code' => '2.2', 'name' => 'Bible as Holy Book', 'order' => 2]);
        SubStrand::create(['strand_id' => $strand13->id, 'code' => '2.3', 'name' => 'Bible Stories', 'lesson_count' => 6, 'order' => 3]);

        $strand14 = Strand::create([
            'learning_area_id' => $creArea->id,
            'code' => '3.0',
            'name' => 'Christian Values',
            'order' => 3
        ]);

        SubStrand::create(['strand_id' => $strand14->id, 'code' => '3.1', 'name' => 'Love for God', 'lesson_count' => 7, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand14->id, 'code' => '3.2', 'name' => 'Love for Neighbour', 'lesson_count' => 9, 'order' => 2]);
        SubStrand::create(['strand_id' => $strand14->id, 'code' => '3.3', 'name' => 'Sharing with Others', 'lesson_count' => 8, 'order' => 3]);

        // HRE (HINDU RELIGIOUS EDUCATION)
        $hreArea = LearningArea::create([
            'curriculum_type_id' => $cbe->id,
            'code' => 'LA006',
            'name' => 'HRE - Hindu Religious Education',
            'description' => 'Hindu religious education and values',
            'lessons_per_week' => 3,
            'grade_level' => 'PP1',
            'order' => 6
        ]);

        $strand15 = Strand::create([
            'learning_area_id' => $hreArea->id,
            'code' => '1.0',
            'name' => 'Creation',
            'order' => 1
        ]);

        SubStrand::create(['strand_id' => $strand15->id, 'code' => '1.1', 'name' => 'Myself', 'order' => 1]);
        SubStrand::create(['strand_id' => $strand15->id, 'code' => '1.2', 'name' => 'My Family', 'order' => 2]);
        SubStrand::create(['strand_id' => $strand15->id, 'code' => '1.3', 'name' => 'Surroundings', 'order' => 3]);

        $strand16 = Strand::create([
            'learning_area_id' => $hreArea->id,
            'code' => '2.0',
            'name' => 'Manifestations of Paramatma',
            'order' => 2
        ]);

        SubStrand::create(['strand_id' => $strand16->id, 'code' => '2.1', 'name' => 'Enlightened Beings', 'order' => 1]);
        SubStrand::create(['strand_id' => $strand16->id, 'code' => '2.2', 'name' => 'Paramatma as Trimurti', 'lesson_count' => 4, 'order' => 2]);

        $strand17 = Strand::create([
            'learning_area_id' => $hreArea->id,
            'code' => '3.0',
            'name' => 'Sadachaar (Good Character)',
            'order' => 3
        ]);

        SubStrand::create(['strand_id' => $strand17->id, 'code' => '3.1', 'name' => 'Forms of Greetings', 'lesson_count' => 4, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand17->id, 'code' => '3.2', 'name' => 'Practice Gratitude', 'order' => 2]);
        SubStrand::create(['strand_id' => $strand17->id, 'code' => '3.3', 'name' => 'Sewa (Selfless Service)', 'order' => 3]);

        $strand18 = Strand::create([
            'learning_area_id' => $hreArea->id,
            'code' => '4.0',
            'name' => 'Worship',
            'order' => 4
        ]);

        SubStrand::create(['strand_id' => $strand18->id, 'code' => '4.1', 'name' => 'Basic Chants', 'lesson_count' => 4, 'order' => 1]);
        SubStrand::create(['strand_id' => $strand18->id, 'code' => '4.2', 'name' => 'Protocols in Worship', 'order' => 2]);
    }
}
